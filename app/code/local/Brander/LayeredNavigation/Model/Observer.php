<?php

class Brander_LayeredNavigation_Model_Observer
{
    public function handleControllerFrontInitRouters($observer)
    {
        $observer->getEvent()->getFront()
            ->addRouter('layerednavigation', new Brander_LayeredNavigation_Controller_Router());
    }

    public function handleCatalogControllerCategoryInitAfter($observer)
    {
        if (Mage::getStoreConfig('brander_layerednavigation/seo/urls')) {
            if (Mage::getStoreConfig('brander_layerednavigation/seo/redirects_enabled')) {
                $this->checkRedirectToSeo();
            }

            /** @var Mage_Core_Controller_Front_Action $controller */
            $controller = $observer->getEvent()->getControllerAction();
            /** @var Mage_Catalog_Model_Category $cat */
            $cat = $observer->getEvent()->getCategory();

            if (!Mage::helper('brander_layerednavigation/url')->saveParams($controller->getRequest())){
                if ($cat->getId()  == Mage::app()->getStore()->getRootCategoryId()){
                    $cat->setId(0);
                    return;
                }
                else {
                    Mage::helper('brander_layerednavigation')->error404();
                }
            }

            if ($cat->getDisplayMode() == 'PAGE' && Mage::registry('brander_layerednavigation_current_params')){
                $cat->setDisplayMode('PRODUCTS');
            }
        }

        Mage::helper('brander_layerednavigation')->restrictMultipleSelection();
    }

    protected function checkRedirectToSeo()
    {
        if (Mage::registry('brander_layerednavigation_forwarded_category_id')) {
            // Already forwarded by our router
            return;
        }

        if (Mage::app()->getRequest()->getParam('am_landing')) {
            // Not implemented and works incorrectly
            return;
        }

        /** @var Brander_LayeredNavigation_Model_Url_Builder $urlBuilder */
        $urlBuilder = Mage::getModel('brander_layerednavigation/url_builder');
        $urlBuilder->reset();

        $isAJAX = Mage::app()->getRequest()->getParam('is_ajax', false);
        $isAJAX = $isAJAX && Mage::app()->getRequest()->isXmlHttpRequest();
        if ($isAJAX) {
            $urlBuilder->setAllowAjaxFlag(true);
        }

        $seoUrl = $urlBuilder->getUrl();

        $currentUrl = Mage::helper('core/url')->getCurrentUrl();

        if ($currentUrl != $seoUrl) {
            Mage::app()->getResponse()->setRedirect($seoUrl, 301);
        }
    }

    public function handleLayoutRender()
    {
        if (Mage::app()->getRequest()->getParam('is_scroll', false))
            return;

        /** @var Mage_Core_Model_Layout $layout */
        $layout = Mage::getSingleton('core/layout');
        $headBlock = $layout->getBlock('head');
        if (!$layout)
            return;

        $isAJAX = Mage::app()->getRequest()->getParam('is_ajax', false);
        $isAJAX = $isAJAX && Mage::app()->getRequest()->isXmlHttpRequest();
        if (!$isAJAX)
            return;

        $layout->removeOutputBlock('root');

        $page = $layout->getBlock('category.products');
        if (!$page){
            $page = $layout->getBlock('search.result');
        }

        if (!$page)
            return;

        $blocks = array();
        foreach ($layout->getAllBlocks() as $b){
            if (!in_array($b->getNameInLayout(), array('layerednavigation.navleft','layerednavigation.navtop','layerednavigation.navright', 'layerednavigation.top', 'amfinder89'))){
                continue;
            }
            $b->setIsAjax(true);
            $html = $b->toHtml();

            if ($b->getBlockId()) {
                $blocks[$b->getBlockId()] = $this->_removeAjaxParam($html);
            }
        }

        if (!$blocks)
            return;

        $container = $layout->createBlock('core/template', 'brander_layerednavigation_container');
        $container->setData('blocks', $blocks);
        $container->setData('page', $this->_removeAjaxParam($page->toHtml()));
        $container->setData('title', $headBlock ? $headBlock->getTitle() : null);

        $layout->addOutputBlock('brander_layerednavigation_container', 'toJson');
    }

    protected function _removeAjaxParam($html)
    {
        // Now "is_ajax" parameter stripped in URL Builder
//        $html = preg_replace('@[\?&]?is_ajax=1([^&])@', '$1', $html);
//        $html = str_replace('is_ajax=1&amp;', '', $html);
//        $html = str_replace('is_ajax=1&', '', $html);

        $html = str_replace('___SID=U', '', $html);

        return $html;
    }

    public function handleBlockOutput($observer)
    {
        if (!Mage::getStoreConfigFlag('brander_layerednavigation/block/ajax'))
            return;

        /* @var $block Mage_Core_Block_Abstract */
        $block = $observer->getBlock();

        $classMatch = $block instanceof Mage_Catalog_Block_Category_View || $block instanceof Mage_CatalogSearch_Block_Result;
        $nameMatch = $block->getNameInLayout() == 'category.products' || $block->getNameInLayout() == 'search.result';

        if ($classMatch && $nameMatch) {
            $transport = $observer->getTransport();
            $html = $transport->getHtml();

            if (strpos($html, "brander_layerednavigation-page-container") === FALSE){
                $html = '<div class="layerednavigation-page-container" id="brander_layerednavigation-page-container">' .
                            $html .
                            '<div style="display:none" class="layerednavigation-overlay"><div></div></div>'.
                        '</div>';

                $transport->setHtml($html);
            }
        }
    }

    /**
     * Reset search engine if it is enabled for catalog navigation
     *
     * @param Varien_Event_Observer $observer
     */
    public function resetCurrentCatalogLayer(Varien_Event_Observer $observer)
    {
        if ($this->_getDataHelper()->useSolr()) {
            Mage::register('_singleton/catalog/layer', Mage::getSingleton('enterprise_search/catalog_layer'));
        }
    }

    /**
     * Reset search engine if it is enabled for search navigation
     *
     * @param Varien_Event_Observer $observer
     */
    public function resetCurrentSearchLayer(Varien_Event_Observer $observer)
    {
        if ($this->_getDataHelper()->useSolr()) {
            Mage::register('_singleton/catalogsearch/layer', Mage::getSingleton('enterprise_search/search_layer'));
        }
    }

    public function settingsChanged()
    {
        /** @var Brander_LayeredNavigation_Model_Mysql4_Filter_Collection $filterCollection */
        $filterCollection = Mage::getResourceModel('brander_layerednavigation/filter_collection');
        $count = $filterCollection->count();
        if ($count == 0) {
            Mage::getResourceModel('brander_layerednavigation/filter')->createFilters();
        }
        $this->invalidateCache();
    }

    public function attributeChanged()
    {
        Mage::getResourceModel('brander_layerednavigation/filter')->createFilters();
        $this->invalidateCache();
    }

    protected function invalidateCache()
    {
        $this->_getDataHelper()->invalidateCache();
    }

    protected function _getDataHelper()
    {
        /** @var Brander_LayeredNavigation_Helper_Data $helper */
        $helper = Mage::helper('brander_layerednavigation');
        return $helper;
    }
}
