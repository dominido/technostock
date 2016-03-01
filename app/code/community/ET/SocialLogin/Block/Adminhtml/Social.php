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

class ET_SocialLogin_Block_Adminhtml_Social
    extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_template = "et_sociallogin/social_accounts.phtml";

    public function getCustomer()
    {
        return Mage::registry('current_customer');
    }

    public function getCustomerId()
    {
        return $this->getCustomer()->getId();
    }


    public function getLinkedAccounts()
    {
        $socialCustomer = Mage::getModel('et_sociallogin/socialCustomer')
            ->getCollection()
            ->addFieldToFilter('customer_id', $this->getCustomerId())
            //->addFieldToFilter('website_id', Mage::app()->getStore()->getId())
            //->addFieldToFilter('website_id', Mage::app()->getStore()->getWebsiteId())
            ->getItems();

        return $socialCustomer;
    }

    public function getTabLabel()
    {
        return $this->__('Social accounts');
    }

    public function getTabTitle()
    {
        return $this->__('Social accounts');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

}