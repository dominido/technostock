<?php
require_once Mage::getModuleDir('controllers', 'Mage_Cms') . DS . 'IndexController.php';
class Brander_CmsPages_Cms_IndexController extends Mage_Cms_IndexController
{


    /**
     * Render CMS 404 Not found page
     *
     * @param string $coreRoute
     */
    public function noRouteAction($coreRoute = null)
    {
        $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
        $this->getResponse()->setHeader('Status','404 File not found');

        $pageId = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE);
        if (strpos($pageId, 'advancedCMS') === 0) {
            $pageData = explode(':', $pageId);
            $this->loadLayout();

            //$this->getLayout()->getBlockSingleton('cms_no_route');
            $this->getLayout()->createBlock('brander_cmspages/pageNoRoute',
                'cms_no_route',
                array('page_id' => $pageData[1]))
                ->setTemplate('brander/page/page404.phtml');
            $this->renderLayout();
            return $this;
        } else {
            if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
                $this->_forward('defaultNoRoute');
            }
        }
    }

    /**
     * Default no route page action
     * Used if no route page don't configure or available
     *
     */
    public function defaultNoRouteAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
        $this->getResponse()->setHeader('Status','404 File not found');

        $this->loadLayout();
        $this->renderLayout();
    }

}
