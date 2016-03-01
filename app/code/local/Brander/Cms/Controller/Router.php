<?php

class Brander_Cms_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    static protected $_config = null;
    public function __construct() {
        if (!self::$_config) {
            self::$_config = Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');
            self::$_config = self::$_config->asArray();
        }
    }

	public function initControllerRouters($observer) {
		$front = $observer->getEvent()->getFront();
        // admin fix
        $request = $front->getRequest();
        if (strpos($request->getRequestUri(), '/' . self::$_config) !== false) {
            return false;
        }
		$front->addRouter('cmsadvanced', $this);
		return $this->_matchRequest($request);
	}

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @return bool|void
     */
	protected function _matchRequest($request) {
		// Enterprise Fix for Full Page Cache.
		if ($request->isStraight()) {
			return;
		}
		$identifier = trim($request->getPathInfo(), '/');

		$currentPage = null;

		if (empty($identifier) && $homePageId = Mage::helper('cmsadvanced')->getConfig()->getHomePageId()) {
			$currentPage = new Varien_Object();
			$currentPage->setId($homePageId);
		} elseif ($identifier) {
			$pages = Mage::getResourceModel('cmsadvanced/page_collection')
				->addFieldToFilter('url_path', $identifier)
				->setStoreId(Mage::app()->getStore()->getId())
				->setPage(1, 1);

			if (count($pages)) {
				$currentPage = $pages->getFirstItem();
			}
		}

		if (!$currentPage) {
			$currentPage = new Varien_Object();
			Mage::dispatchEvent('cmsadvanced_route_match', array('page' => $currentPage, 'request' => $request));
		}

		if ($currentPage && $currentPage->getId()) {
			$request->setModuleName('cmsadvanced')
				->setControllerName('page')
				->setActionName('view')
				->setParam('id', $currentPage->getId())
				->setParams((array)$currentPage->getParams());

			return true;
		}

		return false;
	}

	public function match(Zend_Controller_Request_Http $request) {
		if (!Mage::isInstalled()) {
			Mage::app()->getFrontController()->getResponse()
				->setRedirect(Mage::getUrl('install'))
				->sendResponse();
			exit;
		}

		return $this->_matchRequest($request);
	}
}

?>