<?php
/**
 * Brander_Preorder extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Brander
 * @package		Brander_Preorder
 * @copyright  	Copyright (c) 2015
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Pre Order admin controller
 *
 * @category	Brander
 * @package		Brander_Preorder
 * @author Ultimate Module Creator
 */
class Brander_Preorder_Adminhtml_Preorder_PreorderController extends Brander_Preorder_Controller_Adminhtml_Preorder{
	/**
	 * init the preorder
	 * @access protected
	 * @return Brander_Preorder_Model_Preorder
	 */
	protected function _initPreorder(){
		$preorderId  = (int) $this->getRequest()->getParam('id');
		$preorder	= Mage::getModel('preorder/preorder');
		if ($preorderId) {
			$preorder->load($preorderId);
		}
		Mage::register('current_preorder', $preorder);
		return $preorder;
	}
 	/**
	 * default action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function indexAction() {
		$this->loadLayout();
		$this->_title(Mage::helper('preorder')->__('OneClick'))
			 ->_title(Mage::helper('preorder')->__('One Click'));
		$this->renderLayout();
	}
	/**
	 * grid action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function gridAction() {
		$this->loadLayout()->renderLayout();
	}
	/**
	 * edit pre order - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function editAction() {
		$preorderId	= $this->getRequest()->getParam('id');
		$preorder  	= $this->_initPreorder();
		if ($preorderId && !$preorder->getId()) {
			$this->_getSession()->addError(Mage::helper('preorder')->__('This pre order no longer exists.'));
			$this->_redirect('*/*/');
			return;
		}
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (!empty($data)) {
			$preorder->setData($data);
		}
		Mage::register('preorder_data', $preorder);
		$this->loadLayout();
		$this->_title(Mage::helper('preorder')->__('OneClick'))
			 ->_title(Mage::helper('preorder')->__('One Click'));
		if ($preorder->getId()){
			$this->_title($preorder->getProductId());
		}
		else{
			$this->_title(Mage::helper('preorder')->__('Add One Click'));
		}
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) { 
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true); 
		}
		$this->renderLayout();
	}
	/**
	 * new pre order action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function newAction() {
		$this->_forward('edit');
	}
	/**
	 * save pre order - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function saveAction() {
		if ($data = $this->getRequest()->getPost('preorder')) {
			try {
				$preorder = $this->_initPreorder();
				$preorder->addData($data);
				$preorder->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('preorder')->__('Pre Order was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $preorder->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} 
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
			catch (Exception $e) {
				Mage::logException($e);
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('preorder')->__('There was a problem saving the pre order.'));
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('preorder')->__('Unable to find pre order to save.'));
		$this->_redirect('*/*/');
	}
	/**
	 * delete pre order - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0) {
			try {
				$preorder = Mage::getModel('preorder/preorder');
				$preorder->setId($this->getRequest()->getParam('id'))->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('preorder')->__('Pre Order was successfully deleted.'));
				$this->_redirect('*/*/');
				return; 
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('preorder')->__('There was an error deleteing pre order.'));
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				Mage::logException($e);
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('preorder')->__('Could not find pre order to delete.'));
		$this->_redirect('*/*/');
	}
	/**
	 * mass delete pre order - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function massDeleteAction() {
		$preorderIds = $this->getRequest()->getParam('preorder');
		if(!is_array($preorderIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('preorder')->__('Please select pre order to delete.'));
		}
		else {
			try {
				foreach ($preorderIds as $preorderId) {
					$preorder = Mage::getModel('preorder/preorder');
					$preorder->setId($preorderId)->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('preorder')->__('Total of %d pre order were successfully deleted.', count($preorderIds)));
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('preorder')->__('There was an error deleteing pre order.'));
				Mage::logException($e);
			}
		}
		$this->_redirect('*/*/index');
	}
	/**
	 * export as csv - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function exportCsvAction(){
		$fileName   = 'oneclick.csv';
		$content	= $this->getLayout()->createBlock('preorder/adminhtml_preorder_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName, $content);
	}
	/**
	 * export as MsExcel - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function exportExcelAction(){
		$fileName   = 'oneclick.xls';
		$content	= $this->getLayout()->createBlock('preorder/adminhtml_preorder_grid')->getExcelFile();
		$this->_prepareDownloadResponse($fileName, $content);
	}
	/**
	 * export as xml - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function exportXmlAction(){
		$fileName   = 'oneclick.xml';
		$content	= $this->getLayout()->createBlock('preorder/adminhtml_preorder_grid')->getXml();
		$this->_prepareDownloadResponse($fileName, $content);
	}
}