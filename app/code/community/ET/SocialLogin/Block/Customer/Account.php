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

class ET_SocialLogin_Block_Customer_Account extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $storeId = Mage::app()->getStore()->getId();
        $helper = Mage::helper('et_sociallogin');
        $this->setData('size', 'small');
        if ($helper->isSocialEnabled($storeId)) {
            $this->setTemplate('et_sociallogin/customer/account.phtml');
        }
    }

    public function getCustomerId()
    {
        return Mage::getSingleton('customer/session')->getCustomerId();
    }

    /**
     * Linked social accounts
     *
     * @return mixed
     */
    public function getLinkedAccounts()
    {
        $socialCustomer = Mage::getModel('et_sociallogin/socialCustomer')
            ->getCollection()
            ->addFieldToFilter('customer_id', $this->getCustomerId())
            //->addFieldToFilter('website_id', Mage::app()->getStore()->getId())
            ->addFieldToFilter('website_id', Mage::app()->getStore()->getWebsiteId())
            ->getItems();

        return $socialCustomer;
    }

    /**
     * Not linked social accounts
     *
     * @return mixed
     */
    public function getUnlinkedAccounts()
    {
        $linkedAccounts = array();
        $unlinkedAccounts = array();

        $activeAccounts = Mage::helper('et_sociallogin')->getActiveSocialAccounts();

        foreach ($this->getLinkedAccounts() as $account) {
            $linkedAccounts[] = $account->getSocialProvider();
        }

        foreach ($activeAccounts as $key => $active) {
            if (!in_array($key, $linkedAccounts)) {
                $unlinkedAccounts[$key] = $active;
            }
        }

        return $unlinkedAccounts;
    }
}