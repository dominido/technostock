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
class ET_SocialLogin_Block_Adminhtml_SocialReport_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract
{
    protected $_columnGroupBy = 'id';

    public function __construct()
    {
        parent::__construct();
        $this->setId('socialReportGrid');
        //$this->setUseAjax(false);
        //$this->setDefaultSort('eav_entity_attribute.sort_order');
        //$this->setDefaultDir('asc');
        //$this->setPagerVisibility(false);
        $this->setCountTotals(true);


        //$this->setCollection($collection);
    }

    protected function _prepareCollection()
    {
        $filterData = $this->getFilterData();
        $collection = Mage::getModel("et_sociallogin/socialCustomer")->getCollection();
        $collection->getSelect()->joinRight('customer_entity',
            'main_table.customer_id = customer_entity.entity_id', array('group_id'));

        $collection->getSelect()->group('social_provider');
        $collection->getSelect()->columns(new Zend_Db_Expr('COUNT(customer_entity.entity_id) AS "users_amount"'));
        $collection->getSelect()->columns(new Zend_Db_Expr('IFNULL(social_provider, "' .
            Mage::helper('et_sociallogin')->__('Not using') . '") AS "social_provider_name"'));
        if ($filterData->getData('group_id')) {
            $collection->addFieldToFilter('group_id', $filterData->getData('group_id'));
        }

        $sum = 0;
        foreach ($collection as $item) {
            $sum += $item->getData('users_amount');
        }

        Mage::register('users_amount_total', $sum);
        $totals = new Varien_Object(
            array(
                'users_amount' => $sum,
                'social_provider_name' => Mage::helper('et_sociallogin')->__('Total')
            )
        );
        $this->setTotals($totals);

        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    /*   public function getResourceCollectionName()
       {
           return 'et_sociallogin/socialCustomer_collection';
       }*/

    protected function _prepareColumns()
    {
        $this->addColumn('social_provider', array(
            'header' => Mage::helper('et_sociallogin')->__('Social provider'),
            'index' => 'social_provider_name',
            'filter' => false,
            'sortable' => false
        ));

        $this->addColumn('users_amount', array(
            'header' => Mage::helper('et_sociallogin')->__('Users amount'),
            'index' => 'users_amount',
            'filter' => false,
            'sortable' => false
        ));

        $this->addColumn('users_procent', array(
            'header' => Mage::helper('et_sociallogin')->__('Percents'),
            'index' => 'users_amount',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'ET_SocialLogin_Block_Adminhtml_SocialReport_Renderer_UsersPercent'
        ));


        //$this->addExportType('*/*/exportCsv', Mage::helper('reports')->__('CSV'));
        //$this->addExportType('*/*/exportExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return '';
    }
}
