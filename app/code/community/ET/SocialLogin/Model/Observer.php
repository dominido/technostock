<?php

/**
 * NOTICE OF LICENSE
 *
 * You may not give, sell, distribute, sub-license, rent, lease or lend
 * any portion of the Software or Documentation to anyone.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   ET
 * @package    ET_SocialLogin
 * @copyright  Copyright (c) 2013 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-commercial-v1/   ETWS Commercial License (ECL1)
 */
class ET_SocialLogin_Model_Observer extends Varien_Object
{

    /**
     * Inserts social block to checkout page
     *
     * Events: core_block_abstract_to_html_before
     * @param $observer Varien_Event_Observer
     */
    public function insertSocialBlockToCheckoutPage($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $blockName = $block->getNameInLayout();

        if ($blockName == 'checkout.onepage.login') {
            $formAdditionalBlock = $block->getChild('form.additional.info');
            if (is_object($formAdditionalBlock) && $formAdditionalBlock->getData('type')) {
                $socialBlock = $this->createSocialAuthBlock();
                $formAdditionalBlock->append($socialBlock, 'checkout.onepage.auth');
            } else {
                $formAdditionalBlock = Mage::app()->getLayout()->createBlock('core/text_list', 'form.additional.info');
                $socialBlock = $this->createSocialAuthBlock();
                $formAdditionalBlock->append($socialBlock, 'checkout.onepage.auth');
                $block->append($formAdditionalBlock, 'form.additional.info');
            }
        }
    }

    /**
     * Insert social block to login page
     *
     * Events: core_block_abstract_to_html_before
     * @param $observer Varien_Event_Observer
     */

    public function insertSocialBlockToLoginPage($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $blockName = $block->getNameInLayout();

        if ($blockName == 'customer_form_login') {
            $formAdditionalBlock = $block->getChild('form.additional.info');
            if (is_object($formAdditionalBlock) && $formAdditionalBlock->getData('type')) {
                $socialBlock = $this->createSocialAuthBlock();
                $formAdditionalBlock->append($socialBlock, 'customer.account.login.auth');
            } else {
                $formAdditionalBlock = Mage::app()->getLayout()->createBlock('core/text_list', 'form.additional.info');
                $socialBlock = $this->createSocialAuthBlock();
                $formAdditionalBlock->append($socialBlock, 'customer.account.login.auth');
                $block->append($formAdditionalBlock, 'form.additional.info');
            }
        }
    }

    /**
     * Insert social block to register account page
     *
     * Events: core_block_abstract_to_html_before
     * @param $observer Varien_Event_Observer
     */

    public function insertSocialBlockToRegisterPage($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $blockName = $block->getNameInLayout();

        if ($blockName == 'customer_form_register') {
            $formAdditionalBlock = $block->getChild('form.additional.info');
            if (is_object($formAdditionalBlock) && $formAdditionalBlock->getData('type')) {
                $socialBlock = $this->createSocialAuthBlock();
                $formAdditionalBlock->append($socialBlock, 'customer.account.register.auth');
            } else {
                $formAdditionalBlock = Mage::app()->getLayout()->createBlock('core/text_list', 'form.additional.info');
                $socialBlock = $this->createSocialAuthBlock();
                $formAdditionalBlock->append($socialBlock, 'customer.account.register.auth');
                $block->append($formAdditionalBlock, 'form.additional.info');
            }
        }
    }

    public function createSocialAuthBlock()
    {
        $socialBlock = Mage::app()->getLayout()->createBlock('et_sociallogin/auth', 'customer.account.login.auth');
        return $socialBlock;
    }

    /**
     * Deletes social linked accounts on customer delete from admin panel
     *
     * Events:
     * @param $observer Varien_Event_Observer
     */
    public function deleteSocialLinkedAccounts($observer)
    {
        $customerId = (int)Mage::app()->getRequest()->getParam('id');
        $customerModel = Mage::getModel('customer/customer');
        $customer = $customerModel->load($customerId);

        if ($customer->getId()) {
            try {
                $socialAccounts = Mage::getModel('et_sociallogin/socialCustomer')
                    ->getCollection()
                    ->addFieldToFilter('customer_id', $customer->getId());
                foreach ($socialAccounts as $socialAccount) {
                    $socialAccount->delete();
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        } else {
            $customerId = Mage::app()->getRequest()->getParam('customer');

            foreach ($customerId as $id) {
                try {
                    $socialAccounts = Mage::getModel('et_sociallogin/socialCustomer')
                        ->getCollection()
                        ->addFieldToFilter('customer_id', $id);
                    foreach ($socialAccounts as $socialAccount) {
                        $socialAccount->delete();
                    }
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
        }
    }
}