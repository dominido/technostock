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
class ET_SocialLogin_Model_Mysql4_SocialCustomer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('et_sociallogin/socialCustomer');
    }

    public function setPeriod($data = NULL)
    {
        return $this;
    }

    public function setDateRange($data = NULL)
    {
        return $this;
    }

    public function addStoreFilter($data = NULL)
    {
        return $this;
    }

    public function setAggregatedColumns($data = NULL)
    {
        return $this;
    }

    public function isTotals($data = NULL)
    {
        return $this;
    }

    public function addOrderStatusFilter($data = NULL)
    {
        return $this;
    }
}
