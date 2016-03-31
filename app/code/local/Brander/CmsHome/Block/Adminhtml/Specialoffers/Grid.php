<?php
/**
 * Brander CmsHome extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        CmsHome
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_CmsHome_Block_Adminhtml_Specialoffers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('specialoffersGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
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
        $collection = Mage::getModel('brander_cmshome/specialOffers')->getSpecialOffersCollection();
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
            'price',
            array(
                'header'        => Mage::helper('catalog')->__('Price'),
                'type'          => 'currency',
                'currency_code' => (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
                'index'         => 'price',
            )
        );

        $this->addColumn(
            'special_price',
            array(
                'header'        => Mage::helper('catalog')->__('Special Price'),
                'type'          => 'currency',
                'currency_code' => (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
                'index'         => 'special_price',
            )
        );

        $this->addColumn(
            'special_to_date',
            array(
                'header' => Mage::helper('catalog')->__('Special price to:'),
                'index'  => 'special_to_date',
                'type'   => 'date',
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
            'special_offers',
            array(
                'header'  => Mage::helper('catalog')->__('"Special" Attribute'),
                'width'   => 90,
                'index'   => 'special_offers',
                'type'    => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
            )
        );

        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('brander_cmshome')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('brander_cmshome')->__('Edit'),
                        'url'     => array('base'=> 'adminhtml/catalog_product/edit'),
                        'field'   => 'id',
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('brander_cmshome')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('brander_cmshome')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('brander_cmshome')->__('XML'));

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
