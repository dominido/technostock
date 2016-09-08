<?php
/**
 * Brander_FileImport extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Brander
 * @package		Brander_FileImport
 * @copyright  	Copyright (c) 2014
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * File Grid admin controller
 *
 * @category	Brander
 * @package		Brander_FileImport
 * @author Ultimate Module Creator
 */
class Brander_FileImport_Adminhtml_Fileimport_FilegridController extends Brander_FileImport_Controller_Adminhtml_FileImport{
	/**
	 * init the filegrid
	 * @access protected
	 * @return Brander_FileImport_Model_Filegrid
	 */
	protected function _initFilegrid(){
		$filegridId  = (int) $this->getRequest()->getParam('id');
		$filegrid	= Mage::getModel('fileimport/filegrid');
		if ($filegridId) {
			$filegrid->load($filegridId);
		}
		Mage::register('current_filegrid', $filegrid);
		return $filegrid;
	}
 	/**
	 * default action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function indexAction() {
		$this->loadLayout();
		$this->_title(Mage::helper('fileimport')->__('FileImport'))
			 ->_title(Mage::helper('fileimport')->__('File grid'));
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
	 * edit file grid - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function editAction() {
		$filegridId	= $this->getRequest()->getParam('id');
		$filegrid  	= $this->_initFilegrid();
		if ($filegridId && !$filegrid->getId()) {
			$this->_getSession()->addError(Mage::helper('fileimport')->__('This file grid no longer exists.'));
			$this->_redirect('*/*/');
			return;
		}
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (!empty($data)) {
			$filegrid->setData($data);
		}
		Mage::register('filegrid_data', $filegrid);
		$this->loadLayout();
		$this->_title(Mage::helper('fileimport')->__('FileImport'))
			 ->_title(Mage::helper('fileimport')->__('File grid'));
		if ($filegrid->getId()){
			$this->_title($filegrid->getFileSize());
		}
		else{
			$this->_title(Mage::helper('fileimport')->__('Add file grid'));
		}
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) { 
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true); 
		}
		$this->renderLayout();
	}
	/**
	 * new file grid action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function newAction() {
		$this->_forward('edit');
	}
	/**
	 * save file grid - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function saveAction() {
		if ($data = $this->getRequest()->getPost('filegrid')) {
			try {
				$filegrid = $this->_initFilegrid();
				$filegrid->addData($data);
				$file_csv_nameName = $this->_uploadAndGetName('file_csv_name', Mage::helper('fileimport/filegrid')->getFileBaseDir(), $data);
				if (empty($file_csv_nameName)) {
					Mage::getSingleton('adminhtml/session')->addError('File not uploaded');
					Mage::getSingleton('adminhtml/session')->setFormData($data);
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					return;
				}
				$filegrid->setData('file_csv_name', $file_csv_nameName);
                $filegrid->setData('file_size', Mage::helper('fileimport/filegrid')->formatSizeUnits(filesize(Mage::helper('fileimport/filegrid')->getFileBaseDir().'/'.$file_csv_nameName)));
                $filegrid->setData('file_name', basename(Mage::helper('fileimport/filegrid')->getFileBaseDir().'/'.$file_csv_nameName));
				$filegrid->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('fileimport')->__('File Grid was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $filegrid->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} 
			catch (Mage_Core_Exception $e){
                Mage::logException($e);
				if (isset($data['file_csv_name']['value'])){
					$data['file_csv_name'] = $data['file_csv_name']['value'];
				}
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
			catch (Exception $e) {
				Mage::logException($e);
				if (isset($data['file_csv_name']['value'])){
					$data['file_csv_name'] = $data['file_csv_name']['value'];
				}
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('fileimport')->__('There was a problem saving the file grid.'));
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('fileimport')->__('Unable to find file grid to save.'));
		$this->_redirect('*/*/');
	}
	/**
	 * delete file grid - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0) {
			try {
				$filegrid = Mage::getModel('fileimport/filegrid');
                $fileName = Mage::helper('fileimport/filegrid')->getFileBaseDir().'/'.Mage::getModel('fileimport/filegrid')->load($this->getRequest()->getParam('id'))->getFileCsvName();
                unlink($fileName);
				$filegrid->setId($this->getRequest()->getParam('id'))->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('fileimport')->__('File Grid was successfully deleted.'));
				$this->_redirect('*/*/');
				return; 
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('fileimport')->__('There was an error deleteing file grid.'));
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				Mage::logException($e);
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('fileimport')->__('Could not find file grid to delete.'));
		$this->_redirect('*/*/');
	}
	/**
	 * mass delete file grid - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function massDeleteAction() {
		$filegridIds = $this->getRequest()->getParam('filegrid');
		if(!is_array($filegridIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('fileimport')->__('Please select file grid to delete.'));
		}
		else {
			try {
				foreach ($filegridIds as $filegridId) {
					$filegrid = Mage::getModel('fileimport/filegrid');
					$filegrid->setId($filegridId)->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('fileimport')->__('Total of %d file grid were successfully deleted.', count($filegridIds)));
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('fileimport')->__('There was an error deleteing file grid.'));
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
		$fileName   = 'filegrid.csv';
		$content	= $this->getLayout()->createBlock('fileimport/adminhtml_filegrid_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName, $content);
	}
	/**
	 * export as MsExcel - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function exportExcelAction(){
		$fileName   = 'filegrid.xls';
		$content	= $this->getLayout()->createBlock('fileimport/adminhtml_filegrid_grid')->getExcelFile();
		$this->_prepareDownloadResponse($fileName, $content);
	}
	/**
	 * export as xml - action
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function exportXmlAction(){
		$fileName   = 'filegrid.xml';
		$content	= $this->getLayout()->createBlock('fileimport/adminhtml_filegrid_grid')->getXml();
		$this->_prepareDownloadResponse($fileName, $content);
	}
}