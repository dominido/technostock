<?php
/**
 * Brander HotCategories extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        HotCategories
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_HotCategories_Block_Adminhtml_Hotcategory_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('hotcategoryGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return Brander_HotCategories_Block_Adminhtml_Hotcategory_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('brander_hotcategories/hotcategory')
            ->getCollection()
            ->addAttributeToSelect('*')
/*            ->addAttributeToSelect('mode')
            ->addAttributeToSelect('category_id')
            ->addAttributeToSelect('category_url')
            ->addAttributeToSelect('position')
            ->addAttributeToSelect('status')*/
        ;
        
        $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
        $store = $this->_getStore();
        $collection->joinAttribute(
            'title', 
            'brander_hotcategories_hotcategory/title', 
            'entity_id', 
            null, 
            'inner', 
            $adminStore
        );
        if ($store->getId()) {
            $collection->joinAttribute(
                'brander_hotcategories_hotcategory_title',
                'brander_hotcategories_hotcategory/title', 
                'entity_id', 
                null, 
                'inner', 
                $store->getId()
            );
            $collection->joinAttribute(
                'status',
                'brander_hotcategories_hotcategory/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );

        }

        if ($store->getId()) {
            $collection->joinAttribute(
                'hotcategory_image',
                'brander_hotcategories_hotcategory/image',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        } else {
            $collection->joinAttribute(
                'hotcategory_image',
                'brander_hotcategories_hotcategory/image',
                'entity_id',
                null,
                'left',
                $adminStore
            );
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return Brander_HotCategories_Block_Adminhtml_Hotcategory_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('brander_hotcategories')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );

        if ($this->_getStore()->getId()) {
            $this->addColumn(
                'brander_hotcategories_hotcategory_title', 
                array(
                    'header'    => Mage::helper('brander_hotcategories')->__('Title in %s', $this->_getStore()->getName()),
                    'align'     => 'left',
                    'index'     => 'brander_hotcategories_hotcategory_title',
                )
            );
        }
        else {
            $this->addColumn(
                'title',
                array(
                    'header'    => Mage::helper('brander_hotcategories')->__('Title'),
                    'align'     => 'left',
                    'index'     => 'title',
                )
            );
        }

        $this->addColumn(
            'hotcategory_image',
            array(
                'header'    => Mage::helper('brander_hotcategories')->__('Image'),
                'align'     => 'center',
                'index'     => 'hotcategory_image',
                'style'     => 'max-height: 200px; max-width:200px',
                'width'     => '200px',
                'renderer'  => 'Brander_HotCategories_Block_Adminhtml_Hotcategory_Renderer_Image'

            )
        );

        $this->addColumn(
            'mode',
            array(
                'header' => Mage::helper('brander_hotcategories')->__('Category Mode'),
                'index'  => 'mode',
                'type'  => 'options',
                'options' => Mage::helper('brander_hotcategories')->convertOptions(Mage::getModel('brander_hotcategories/adminhtml_source_urlmode')->getAllOptions(false)),
            )
        );
        $this->addColumn(
            'category_id',
            array(
                'header' => Mage::helper('brander_hotcategories')->__('Category Id'),
                'index'  => 'category_id',
                'type'  => 'options',
                'options' => Mage::helper('brander_hotcategories')->convertCategoryOptions(Mage::getModel('brander_hotcategories/adminhtml_source_categoriestree')->getAllOptions(false)),
            )
        );
        $this->addColumn(
            'category_url',
            array(
                'header' => Mage::helper('brander_hotcategories')->__('Category URL'),
                'index'  => 'category_url',
                'type'=> 'text',

            )
        );

        $this->addColumn(
            'position',
            array(
                'header' => Mage::helper('brander_hotcategories')->__('Position'),
                'index'  => 'position',
                'type'=> 'number',

            )
        );

        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('brander_hotcategories')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('brander_hotcategories')->__('Enabled'),
                    '0' => Mage::helper('brander_hotcategories')->__('Disabled'),
                )
            )
        );

        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('brander_hotcategories')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('brander_hotcategories')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('brander_hotcategories')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('brander_hotcategories')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('brander_hotcategories')->__('XML'));
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
     * @return Brander_HotCategories_Block_Adminhtml_Hotcategory_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('hotcategory');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('brander_hotcategories')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('brander_hotcategories')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('brander_hotcategories')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('brander_hotcategories')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('brander_hotcategories')->__('Enabled'),
                            '0' => Mage::helper('brander_hotcategories')->__('Disabled'),
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
     * @param Brander_HotCategories_Model_Hotcategory
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
