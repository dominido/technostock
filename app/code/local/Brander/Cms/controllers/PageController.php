<?php
class Brander_Cms_PageController extends Mage_Core_Controller_Front_Action
{
    /* Returns current page
     *
     * @return Brander_Cms_Model_Page
     */
    public function getPage()
    {
        return Mage::registry('current_page');
    }

    /*
     * Initializes current page
     *
     * @return Brander_Cms_Model_Page
     */
    public function _initPage()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $storeId = Mage::app()->getStore()->getId();
            $page = Mage::getModel('cmsadvanced/page')->setStoreId($storeId)->load($id);

            if (($page && $page->getIsActive()) || Mage::helper('cmsadvanced')->isPreviewMode()) {
                Mage::register('current_page', $page);
            } else {
                return;
            }
        }

        return $page;
    }

    /*
     * Page view action
     */
    public function viewAction()
    {
        if (!$page = $this->_initPage()) {
            $this->_forward('noroute');
            return;
        };
        
        if ($redirectUrl = $page->getRedirectUrl()) {
            $this->_redirectUrl($redirectUrl);
            return;
        }
        
        $this->loadLayout();

        $this->_preparePageBlock();
        $this->_appendBodyClass();

        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        
        $this->renderLayout();
    }

    /*
     * Prepares page block and children
     *
     * @return Brander_Cms_PageController
     */
    protected function _preparePageBlock()
    {
        $layout = $this->getLayout();
        $page = $this->getPage();
        $pageType = $page->getPageType();
        
        $pageBlock = $layout->getBlock('cmsadvanced.page');
        $children = $pageBlock->getChild();

        $pageBlock = $layout->createBlock($pageType->getBlock())
                   ->setTemplate($pageType->getTemplate())
                   ->addData($page->getData())
                   ;
        //copy children from default block to custom
        foreach ($children as $child) {
            $pageBlock->append($child);
        }

        $layout->getBlock('content')->append($pageBlock, 'cmsadvanced.page');

        return $this;
    }

    /**
     * Append body class based on page type
     *
     * @return Brander_Cms_PageController
     */
    protected function _appendBodyClass()
    {
        $root = $this->getLayout()->getBlock('root');
        $pageType = $this->getPage()->getPageType();

        $pageTypeCode = $pageType->getCode();
        
        $root->addBodyClass($pageType->getCode());

        foreach (explode('/', $this->getRequest()->getPathInfo()) as $class) {
            if ($class && strpos($root->getBodyClass(), $class) === false) {
                $root->addBodyClass($class);
            }
        }

        return $this;
    }

    /*
     * Adds page type and page type parents layout handles
     */
    public function addActionLayoutHandles()
    {
        parent::addActionLayoutHandles();
        
        $pageType = $this->getPage()->getPageType();

        $handles = array();
        
        foreach ($pageType->getParentCodes() as $parentCode) {
            $handles[] = 'PAGE_TYPE_' . strtoupper($parentCode);
        }

        $handles[] = 'PAGE_TYPE_' . strtoupper($pageType->getCode());
        
        $this->getLayout()->getUpdate()->addHandle($handles);

        return $this;
    }
}
