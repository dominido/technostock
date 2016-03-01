<?php

class Brander_NewPost_Adminhtml_LocalesController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$this->_title($this->__('System'))->_title($this->__('Nova Poshta Locales'));

		$this->_initAction()
			->_addContent($this->getLayout()->createBlock('brander_newpost/adminhtml_locales'))
			->renderLayout();

		return $this;
	}

	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('system/newpost')
			->_addBreadcrumb($this->__('System'), $this->__('System'))
			->_addBreadcrumb($this->__('Nova Poshta Locales'), $this->__('Nova Poshta Locales'))
		;
		return $this;
	}

	public function editAction()
	{
		$this->_initAction();

		// Get id if available
		$id  = $this->getRequest()->getParam('id');
		$model = Mage::getModel('brander_newpost/locale');

		if ($id) {
			// Load record
			$model->load($id);

			// Check if record is loaded
			if (!$model->getId()) {
				Mage::getSingleton('adminhtml/session')->addError($this->__('This locale does not exist.'));
				$this->_redirect('*/*/');

				return;
			}
		}

		$this->_title($model->getName());

		$data = Mage::getSingleton('adminhtml/session')->getQuestionData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register('brander_newpost/locale', $model);

		$this->_initAction()
			->_addContent($this->getLayout()->createBlock('brander_newpost/adminhtml_locales_edit')->setData('action',
				$this->getUrl('*/*/save')))
			->renderLayout();
	}

	public function saveAction()
	{
		if ($postData = $this->getRequest()->getPost()) {
			$model = Mage::getSingleton('brander_newpost/locale');
			$model->setData($postData);

			try {
				$model->save();

				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The locale has been saved.'));
				$this->_redirect('*/*/');

				return;
			}
			catch (Mage_Core_Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this locale.'));
			}

			Mage::getSingleton('adminhtml/session')->setBazData($postData);
			$this->_redirectReferer();
		}
	}
} 