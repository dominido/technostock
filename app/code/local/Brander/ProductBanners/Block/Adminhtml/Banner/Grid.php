<?php
/**
 * Brander ProductBanners extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        ProductBanners
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_ProductBanners_Block_Adminhtml_Banner_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public

     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('bannerGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return Brander_ProductBanners_Block_Adminhtml_Banner_Grid

     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('brander_productbanners/banner')
            ->getCollection()
            ->addAttributeToSelect('status');
        
        $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
        $store = $this->_getStore();
        if ($store->getId()) {
            $currentStore = $store->getId();
        } else {
            $currentStore = $adminStore;
        }


        $collection->joinAttribute(
            'title',
            'brander_productbanners_banner/title',
            'entity_id',
            null,
            'inner',
            $adminStore
        );
        if ($store->getId()) {
            $collection->joinAttribute(
                'brander_productbanners_banner_title',
                'brander_productbanners_banner/title', 
                'entity_id', 
                null, 
                'inner', 
                $store->getId()
            );
        }

        $collection->joinAttribute(
            'banner_image',
            'brander_productbanners_banner/image',
            'entity_id',
            null,
            'inner',
            $currentStore
        );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return Brander_ProductBanners_Block_Adminhtml_Banner_Grid

     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('brander_productbanners')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );

        
        if ($this->_getStore()->getId()) {
            $this->addColumn(
                'brander_productbanners_banner_title', 
                array(
                    'header'    => Mage::helper('brander_productbanners')->__('Title in %s', $this->_getStore()->getName()),
                    'align'     => 'left',
                    'index'     => 'brander_productbanners_banner_title',
                )
            );
        }
        else {
            $this->addColumn(
                'title',
                array(
                    'header'    => Mage::helper('brander_productbanners')->__('Title'),
                    'align'     => 'left',
                    'index'     => 'title',
                )
            );
        }

        $this->addColumn(
            'banner_image',
            array(
                'header'    => Mage::helper('brander_productbanners')->__('Image'),
                'align'     => 'left',
                'index'     => 'banner_image',
                'renderer'  => 'brander_productbanners/adminhtml_helper_column_renderer_image'
            )
        );

        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('brander_productbanners')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('brander_productbanners')->__('Enabled'),
                    '0' => Mage::helper('brander_productbanners')->__('Disabled'),
                )
            )
        );
        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('brander_productbanners')->__('Created at'),
                'index'  => 'created_at',
                'width'  => '120px',
                'type'   => 'datetime',
            )
        );
        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('brander_productbanners')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('brander_productbanners')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('brander_productbanners')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('brander_productbanners')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('brander_productbanners')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * get the selected store
     *
     * @access protected
     * @return Mage_Core_Model_Store

     */
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return Brander_ProductBanners_Block_Adminhtml_Banner_Grid

     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('banner');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('brander_productbanners')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('brander_productbanners')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('brander_productbanners')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('brander_productbanners')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('brander_productbanners')->__('Enabled'),
                            '0' => Mage::helper('brander_productbanners')->__('Disabled'),
                        )
                    )
                )
            )
        );
        return $this;
    }

    /**
     * get the row url
     *
     * @access public
     * @param Brander_ProductBanners_Model_Banner
     * @return string

     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
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
