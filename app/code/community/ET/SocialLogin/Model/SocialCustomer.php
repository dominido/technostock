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

class ET_SocialLogin_Model_SocialCustomer extends Mage_Core_Model_Abstract
{

    protected $_customer = null;

    public function _construct()
    {
        parent::_construct();
        $this->_init('et_sociallogin/socialCustomer');
    }

    public function getCustomer()
    {
        if (!$this->_customer) {
            $this->_customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
        }

        return $this->_customer;
    }
}
