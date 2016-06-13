<?php

/**
 * Brander AutoImport extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        AutoImport
 * @copyright      Copyright (c) 2014-2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */

class Brander_AutoImport_Block_Adminhtml_Stages_Stagetwo_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('stagetwoGrid');
        $this->setDefaultSort('updated_at');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    /**
     * Prepare data for grid
     **/
    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        if($store->getId()) {
            Mage::app()->setCurrentStore($store);
        } else {
            Mage::app()->setCurrentStore(Mage::app()->getDefaultStoreView());
        }

        $collection = Mage::getModel('autoimport/source_stages_stageTwo')->getGridCollection();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'index'  => 'entity_id',
            'type'   => 'number',
        ));

        $this->addColumn(
            'name',
            array(
                'header' => Mage::helper('catalog')->__('Name'),
                'index'  => 'name',
            )
        );

        $this->addColumn(
            'type',
            array(
                'header'  => Mage::helper('catalog')->__('Type'),
                'width'   => 100,
                'index'   => 'type_id',
                'type'    => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            )
        );

        $this->addColumn(
            'sku',
            array(
                'header' => Mage::helper('catalog')->__('SKU'),
                'width'  => 80,
                'index'  => 'sku',
            )
        );

        $this->addColumn(
            'updated_at',
            array(
                'header' => Mage::helper('catalog')->__('Updated At'),
                'width'  => 140,
                'index'  => 'updated_at',
            )
        );

        $this->addColumn(
            'price',
            array(
                'header'        => Mage::helper('catalog')->__('Price'),
                'type'          => 'currency',
                'currency_code' => (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
                'index'         => 'price',
            )
        );

        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('catalog')->__('Status'),
                'width'   => 90,
                'index'   => 'status',
                'type'    => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
            )
        );

        $this->addColumn(
            'visibility',
            array(
                'header'  => Mage::helper('catalog')->__('Visibility'),
                'width'   => 90,
                'index'   => 'visibility',
                'type'    => 'options',
                'options' => Mage::getSingleton('catalog/product_visibility')->getOptionArray(),
            )
        );

        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('autoimport')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('autoimport')->__('Edit'),
                        'url'     => array('base'=> 'adminhtml/catalog_product/edit'),
                        'field'   => 'id',
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('autoimport')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('autoimport')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('autoimport')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * get the row url
     *
     * @access public
     * @return string

     */
    public function getRowUrl($row)
    {
        return '';
    }

    /**
     * get the grid url
     *
     * @access public
     * @return string

     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}
