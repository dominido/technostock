<?php
/**
 * Brander MarketExport extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        MarketExport
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_MarketExport_Block_Adminhtml_Export_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('marketexportGrid');
        $this->setUseAjax(false);
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare data for grid
     **/
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('marketexport/export_collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => Mage::helper('marketexport')->__('ID'),
            'index'  => 'entity_id',
            'type'   => 'number',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('marketexport')->__('Title'),
            'index'  => 'name',
            'type'   => 'text',
        ));

        $this->addColumn('path', array(
            'header' => Mage::helper('marketexport')->__('File name'),
            'index'  => 'path',
            'type'   => 'text',
            'renderer' => 'Brander_MarketExport_Block_Adminhtml_Export_Grid_Renderer_Path'
        ));

        $this->addColumn('updated_at', array(
            'header' => Mage::helper('marketexport')->__('Updated date'),
            'index'  => 'updated_at',
            'type'   => 'text',
        ));

        $this->addColumn('type', array(
            'header' => Mage::helper('marketexport')->__('Type'),
            'index'  => 'type',
            'type'   => 'options',
            'options' => Brander_MarketExport_Model_Export::$_MARKET_EXPORT_TYPES
        ));

        $this->addColumn('is_active', array(
            'header' => Mage::helper('marketexport')->__('Status'),
            'index'  => 'is_active',
            'type'   => 'options',
            'options'=> Brander_MarketExport_Model_Export::$_STATUS_ARRAY
        ));


        $this->addColumn('action',
            array(
                'header'  => Mage::helper('marketexport')->__('Action'),
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('marketexport')->__('Active'),
                        'url'     => array('base' => '*/*/active'),
                        'field'   => 'id',
                        'confirm'  => Mage::helper('marketexport')->__('Are you sure?')
                    ),

                    array(
                        'caption' => Mage::helper('marketexport')->__('Not active'),
                        'url'     => array('base' => '*/*/disactive'),
                        'field'   => 'id',
                        'confirm'  => Mage::helper('marketexport')->__('Are you sure?')
                    ),
                    
                    array(
                        'caption' => Mage::helper('marketexport')->__('Delete'),
                        'url'     => array('base' => '*/*/delete'),
                        'field'   => 'id',
                        'confirm'  => Mage::helper('marketexport')->__('Are you sure?')
                    ),
                    array(
                        'caption' => Mage::helper('marketexport')->__('Update'),
                        'url'     => array('base' => '*/*/update'),
                        'field'   => 'id',
                    )
                ),

                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('export_ids');
        if (Mage::getSingleton('admin/session')->isAllowed('marketexport/marketexport/action/active')) {
            $this->getMassactionBlock()->addItem('active', array(
                 'label'=> Mage::helper('marketexport')->__('Active'),
                 'url'  => $this->getUrl('*/*/massActive'),
                 'confirm'  => Mage::helper('marketexport')->__('Are you sure?')
            ));
        }
        if (Mage::getSingleton('admin/session')->isAllowed('marketexport/marketexport/action/disactive')) {
            $this->getMassactionBlock()->addItem('disactive', array(
                 'label'=> Mage::helper('marketexport')->__('Не активен'),
                 'url'  => $this->getUrl('*/*/massDisactive'),
                 'confirm'  => Mage::helper('marketexport')->__('Are you sure?')
            ));
        }
        if (Mage::getSingleton('admin/session')->isAllowed('marketexport/marketexport/action/delete')) {
            $this->getMassactionBlock()->addItem('delete', array(
                 'label'=> Mage::helper('marketexport')->__('Delete'),
                 'url'  => $this->getUrl('*/*/massDelete'),
                 'confirm'  => Mage::helper('marketexport')->__('Are you sure?')
            ));
        }
        return $this;
    }
}
