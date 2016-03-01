<?php

class Brander_NewPost_Adminhtml_WarehousesController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$this->_title($this->__('System'))->_title($this->__('Nova Poshta Warehouses'));

		$this->_initAction()
			->_addContent($this->getLayout()->createBlock('brander_newpost/adminhtml_warehouses'))
			->renderLayout();

		return $this;
	}

	public function syncAction()
	{
		if (!Mage::helper("brander_newpost")->getApiKey())
			$this->_getSession()->addError($this->__('Error during sync: API key is empty.'));
		else
			try {
				Mage::getModel('brander_newpost/api')->processImport();
				$this->_getSession()->addSuccess($this->__('City and Warehouse API sync finished'));
			}
			catch (Exception $e) {
				$this->_getSession()->addError($this->__('Error during sync: %s', $e->getMessage()));
			}

		$this->_redirect('*/*/index');

		return $this;
	}

	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('system/newpost')
			->_addBreadcrumb($this->__('System'), $this->__('System'))
			->_addBreadcrumb($this->__('Nova Poshta Warehouses'), $this->__('Nova Poshta Warehouses'))
		;
		return $this;
	}
} 