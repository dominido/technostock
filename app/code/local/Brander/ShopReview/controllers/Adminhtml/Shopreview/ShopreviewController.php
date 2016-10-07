<?php
/**
 * Brander_ShopReview extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Brander
 * @package		Brander_ShopReview
 * @copyright  	Copyright (c) 2016
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Shop Review admin controller
 *
 * @category    Brander
 * @package     Brander_ShopReview
 * @author      Ultimate Module Creator
 */
class Brander_ShopReview_Adminhtml_Shopreview_ShopreviewController
    extends Brander_ShopReview_Controller_Adminhtml_ShopReview {
    /**
     * init the shopreview
     * @access protected
     * @return Brander_ShopReview_Model_Shopreview
     */
    protected function _initShopreview(){
        $shopreviewId  = (int) $this->getRequest()->getParam('id');
        $shopreview    = Mage::getModel('brander_shopreview/shopreview');
        if ($shopreviewId) {
            $shopreview->load($shopreviewId);
        }
        Mage::register('current_shopreview', $shopreview);
        return $shopreview;
    }
     /**
     * default action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function indexAction() {
        $this->loadLayout();
        $this->_title(Mage::helper('brander_shopreview')->__('Shop Review'))
             ->_title(Mage::helper('brander_shopreview')->__('Shop Review'));
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
     * edit shop review - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction() {
        $shopreviewId    = $this->getRequest()->getParam('id');
        $shopreview      = $this->_initShopreview();
        if ($shopreviewId && !$shopreview->getId()) {
            $this->_getSession()->addError(Mage::helper('brander_shopreview')->__('This shop review no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getShopreviewData(true);
        if (!empty($data)) {
            $shopreview->setData($data);
        }
        Mage::register('shopreview_data', $shopreview);
        $this->loadLayout();
        $this->_title(Mage::helper('brander_shopreview')->__('Shop Review'))
             ->_title(Mage::helper('brander_shopreview')->__('Shop Review'));
        if ($shopreview->getId()){
            $this->_title($shopreview->getUserName());
        }
        else{
            $this->_title(Mage::helper('brander_shopreview')->__('Add shop review'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }
    /**
     * new shop review action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function newAction() {
        $this->_forward('edit');
    }
    /**
     * save shop review - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost('shopreview')) {
            try {
                $shopreview = $this->_initShopreview();
                $shopreview->addData($data);
                $shopreview->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('brander_shopreview')->__('Shop Review was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $shopreview->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setShopreviewData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brander_shopreview')->__('There was a problem saving the shop review.'));
                Mage::getSingleton('adminhtml/session')->setShopreviewData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brander_shopreview')->__('Unable to find shop review to save.'));
        $this->_redirect('*/*/');
    }
    /**
     * delete shop review - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0) {
            try {
                $shopreview = Mage::getModel('brander_shopreview/shopreview');
                $shopreview->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('brander_shopreview')->__('Shop Review was successfully deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brander_shopreview')->__('There was an error deleting shop review.'));
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brander_shopreview')->__('Could not find shop review to delete.'));
        $this->_redirect('*/*/');
    }
    /**
     * mass delete shop review - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction() {
        $shopreviewIds = $this->getRequest()->getParam('shopreview');
        if(!is_array($shopreviewIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brander_shopreview')->__('Please select shop review to delete.'));
        }
        else {
            try {
                foreach ($shopreviewIds as $shopreviewId) {
                    $shopreview = Mage::getModel('brander_shopreview/shopreview');
                    $shopreview->setId($shopreviewId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('brander_shopreview')->__('Total of %d shop review were successfully deleted.', count($shopreviewIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brander_shopreview')->__('There was an error deleting shop review.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    /**
     * mass status change - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massStatusAction(){
        $shopreviewIds = $this->getRequest()->getParam('shopreview');
        if(!is_array($shopreviewIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brander_shopreview')->__('Please select shop review.'));
        }
        else {
            try {
                foreach ($shopreviewIds as $shopreviewId) {
                $shopreview = Mage::getSingleton('brander_shopreview/shopreview')->load($shopreviewId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d shop review were successfully updated.', count($shopreviewIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brander_shopreview')->__('There was an error updating shop review.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    /**
     * mass Review Status change - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massReviewStatusAction(){
        $shopreviewIds = $this->getRequest()->getParam('shopreview');
        if(!is_array($shopreviewIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brander_shopreview')->__('Please select shop review.'));
        }
        else {
            try {
                foreach ($shopreviewIds as $shopreviewId) {
                $shopreview = Mage::getSingleton('brander_shopreview/shopreview')->load($shopreviewId)
                            ->setReviewStatus($this->getRequest()->getParam('flag_review_status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d shop review were successfully updated.', count($shopreviewIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brander_shopreview')->__('There was an error updating shop review.'));
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
        $fileName   = 'shopreview.csv';
        $content    = $this->getLayout()->createBlock('brander_shopreview/adminhtml_shopreview_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * export as MsExcel - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportExcelAction(){
        $fileName   = 'shopreview.xls';
        $content    = $this->getLayout()->createBlock('brander_shopreview/adminhtml_shopreview_grid')->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * export as xml - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportXmlAction(){
        $fileName   = 'shopreview.xml';
        $content    = $this->getLayout()->createBlock('brander_shopreview/adminhtml_shopreview_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * Check if admin has permissions to visit related pages
     * @access protected
     * @return boolean
     * @author Ultimate Module Creator
     */
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('system/brander_shopreview/shopreview');
    }
}
