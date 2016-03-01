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
 * @copyright  Copyright (c) 2015 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-commercial-v1/   ETWS Commercial License (ECL1)
 */
class ET_SocialLogin_Block_Adminhtml_SocialAccountGrid_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('account_grid');
    }

    public function _prepareCollection()
    {
        $collection = Mage::getModel('et_sociallogin/socialCustomer')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function _prepareColumns()
    {
        $configData = Mage::getStoreConfig('social_login');
        $providers = array();
        foreach ($configData as  $configItem) {
            if (isset($configItem['provider']) && $configItem['provider'] != '') {
                $providers[$configItem['provider']] = trim($configItem['provider']);
            }
        }

        $this->addColumn('id', array(
            'header' => $this->__('ID'),
            'index' => 'id',
            'type' => 'text',
            'width' => '10px',
        ));

        $this->addColumn('customer_id', array(
            'header' => $this->__('Customer name'),
            'index' => 'customer_id',
            'type' => 'text',
            'width' => '10px',
            'renderer' => 'et_sociallogin/adminhtml_socialAccountGrid_renderer_customerName'
        ));

        $this->addColumn('customer_email', array(
            'header' => $this->__('Customer email'),
            'index' => 'customer_email',
            'type' => 'text',
            'width' => '10px',
            'renderer' => 'et_sociallogin/adminhtml_socialAccountGrid_renderer_customerEmail'
        ));

        $this->addColumn('social_provider', array(
            'header' => $this->__('Social provider'),
            'index' => 'social_provider',
            'type' => 'options',
            'width' => '50px',
            'options' => $providers
        ));

        $this->addColumn('social_name', array(
            'header' => $this->__('Social name'),
            'index' => 'social_name',
            'type' => 'text',
            'width' => '50px'
        ));

        return parent::_prepareColumns();
    }
}