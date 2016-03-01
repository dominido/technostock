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

class ET_SocialLogin_Block_Auth extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    protected $_template = 'et_sociallogin/auth.phtml';

    public function __construct()
    {
        parent::__construct();
        $this->setData('size', 'small');
        $this->setData('response_type', 'popup');

        $storeId = Mage::app()->getStore()->getId();
        $label = Mage::getStoreConfig('social_login/icons/block_label', $storeId);
        $this->setData('label_text', $label);
    }

    protected function _toHtml()
    {
        /** @var $helper ET_SocialLogin_Helper_Data*/
        $helper = Mage::helper('et_sociallogin');

        $blockName = $this->getNameInLayout();
        $content = '';

        $storeId = Mage::app()->getStore()->getId();;
        if (($blockName == 'customer.account.login.auth' && !$helper->isSocialEnabledOnLoginPage($storeId))
            || ($blockName == 'checkout.onepage.auth' && !$helper->isSocialEnabledOnCheckoutPage($storeId))
        ) {
            return $content;
        }

        if (Mage::getSingleton('customer/session')->isLoggedIn() == false) {
            $content = parent::_toHtml();
        }

        return $content;
    }


    public function getSocialProviders()
    {
        /** @var $helper ET_SocialLogin_Helper_Data*/
        $helper = Mage::helper('et_sociallogin');

        return $helper->getActiveSocialAccounts();
    }
}
