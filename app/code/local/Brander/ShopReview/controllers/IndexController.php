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
 * Shop Review front contrller
 *
 * @category    Brander
 * @package     Brander_ShopReview
 * @author      Ultimate Module Creator
 */
class Brander_ShopReview_IndexController
    extends Mage_Core_Controller_Front_Action {
    /**
      * default action
      * @access public
      * @return void
      * @author Ultimate Module Creator
      */
    public function indexAction(){
         $this->loadLayout();
         $this->_initLayoutMessages('catalog/session');
         $this->_initLayoutMessages('customer/session');
         $this->_initLayoutMessages('checkout/session');
         if (Mage::helper('brander_shopreview/shopreview')->getUseBreadcrumbs()){
             if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')){
                 $breadcrumbBlock->addCrumb('home', array(
                            'label'    => Mage::helper('brander_shopreview')->__('Home'),
                            'link'     => Mage::getUrl(),
                        )
                 );
                 $breadcrumbBlock->addCrumb('shopreviews', array(
                            'label'    => Mage::helper('brander_shopreview')->__('Shop Review'),
                            'link'    => '',
                    )
                 );
             }
         }
        $this->renderLayout();
    }

    public function postAction()
    {
        if ($data = $this->getRequest()->getPost('company_review')) {
            if (isset($_POST["g-recaptcha-response"])){
                $captcha = $_POST["g-recaptcha-response"];
            }
            if (!$captcha && Mage::getStoreConfig('brander_shopreview/shopreview/google_id')){
                Mage::getSingleton('core/session')->addError($this->__('Validation failed. Make sure that you correctly fill in the fields and CAPTCHA.'));
                $this->_redirectReferer();
                return;
            }
            if (!$this->_validateFormKey()) {
                // returns to the product item page
                $this->_redirectReferer();
                return;
            }
            try {
                $shopreview = Mage::getModel('brander_shopreview/shopreview');
                $shopreview->addData($data);
                $shopreview->save();
                Mage::helper('brander_shopreview')->sendMail($data);
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('brander_shopreview')->__('Shop Review was successfully saved'));
                Mage::getSingleton('core/session')->setFormData(false);
                $this->_redirectReferer();
                return;
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('core/session')->addError($e->getMessage());
                Mage::getSingleton('core/session')->setShopreviewData($data);
                $this->_redirectReferer();
                return;
            }
            catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('core/session')->addError(Mage::helper('brander_shopreview')->__('There was a problem saving the shop review.'));
                Mage::getSingleton('core/session')->setShopreviewData($data);
                $this->_redirectReferer();
                return;
            }
        }
        Mage::getSingleton('core/session')->addError(Mage::helper('brander_shopreview')->__('Unable to find shop review to save.'));
        $this->_redirectReferer();
    }
}
