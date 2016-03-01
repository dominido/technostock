<?php
/**
 * Brander Benefits extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        Benefits
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_Benefits_Block_Adminhtml_Benefit_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_currentStore = null;

    /**
     * constructor
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('benefitGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return Brander_Benefits_Block_Adminhtml_Benefit_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('brander_benefits/benefit')
            ->getCollection()
            ->addAttributeToSelect('*');

        $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
        $store = $this->_getStore();

        if ($store->getId()) {
            $collection->joinAttribute(
                'brander_benefits_benefit_title',
                'brander_benefits_benefit/title', 
                'entity_id', 
                null, 
                'inner', 
                $store->getId()
            );
        } else {
            $collection->joinAttribute(
                'title',
                'brander_benefits_benefit/title',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
        }

        if ($store->getId()) {
            $collection->joinAttribute(
                'benefit_image',
                'brander_benefits_benefit/image',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        } else {
            $collection->joinAttribute(
                'benefit_image',
                'brander_benefits_benefit/image',
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
     * @return Brander_Benefits_Block_Adminhtml_Benefit_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('brander_benefits')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );

        if ($this->_getStore()->getId()) {
            $this->addColumn(
                'brander_benefits_benefit_title', 
                array(
                    'header'    => Mage::helper('brander_benefits')->__('Title in %s', $this->_getStore()->getName()),
                    'align'     => 'left',
                    'index'     => 'brander_benefits_benefit_title',
                )
            );
        } else {
            $this->addColumn(
                'title',
                array(
                    'header'    => Mage::helper('brander_benefits')->__('Title'),
                    'align'     => 'left',
                    'index'     => 'title',
                )
            );
        }

        $this->addColumn(
            'benefit_image',
            array(
                'header'    => Mage::helper('brander_benefits')->__('Image'),
                'align'     => 'center',
                'index'     => 'benefit_image',
                'style'     => 'max-height: 200px; max-width:200px',
                'width'     => '200px',
                'renderer'  => 'Brander_Benefits_Block_Adminhtml_Benefit_Renderer_Image'
            )
        );

        $this->addColumn(
            'title_on_front',
            array(
                'header' => Mage::helper('brander_benefits')->__('Show Title on Front'),
                'index'  => 'title_on_front',
                'type'    => 'options',
                    'options'    => array(
                    '1' => Mage::helper('brander_benefits')->__('Yes'),
                    '0' => Mage::helper('brander_benefits')->__('No'),
                )
            )
        );

        $this->addColumn(
            'show_on_homepage',
            array(
                'header' => Mage::helper('brander_benefits')->__('Show on homepage'),
                'index'  => 'show_on_homepage',
                'type'    => 'options',
                    'options'    => array(
                    '1' => Mage::helper('brander_benefits')->__('Yes'),
                    '0' => Mage::helper('brander_benefits')->__('No'),
                )
            )
        );

        $this->addColumn(
            'order_position',
            array(
                'header' => Mage::helper('brander_benefits')->__('Order Position'),
                'index'  => 'order_position',
                'type'=> 'number',
            )
        );

        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('brander_benefits')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('brander_benefits')->__('Enabled'),
                    '0' => Mage::helper('brander_benefits')->__('Disabled'),
                )
            )
        );

        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('brander_benefits')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('brander_benefits')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );

        $this->addExportType('*/*/exportCsv', Mage::helper('brander_benefits')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('brander_benefits')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('brander_benefits')->__('XML'));
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
     * @return Brander_Benefits_Block_Adminhtml_Benefit_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('benefit');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('brander_benefits')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('brander_benefits')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('brander_benefits')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('brander_benefits')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('brander_benefits')->__('Enabled'),
                            '0' => Mage::helper('brander_benefits')->__('Disabled'),
                        )
                    )
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'title_on_front',
            array(
                'label'      => Mage::helper('brander_benefits')->__('Change Show Title on Front'),
                'url'        => $this->getUrl('*/*/massTitleOnFront', array('_current'=>true)),
                'additional' => array(
                    'flag_title_on_front' => array(
                        'name'   => 'flag_title_on_front',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('brander_benefits')->__('Show Title on Front'),
                        'values' => array(
                                '1' => Mage::helper('brander_benefits')->__('Yes'),
                                '0' => Mage::helper('brander_benefits')->__('No'),
                            )
                    )
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'show_on_homepage',
            array(
                'label'      => Mage::helper('brander_benefits')->__('Change Show on homepage'),
                'url'        => $this->getUrl('*/*/massShowOnHomepage', array('_current'=>true)),
                'additional' => array(
                    'flag_show_on_homepage' => array(
                        'name'   => 'flag_show_on_homepage',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('brander_benefits')->__('Show on homepage'),
                        'values' => array(
                                '1' => Mage::helper('brander_benefits')->__('Yes'),
                                '0' => Mage::helper('brander_benefits')->__('No'),
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
     * @param Brander_Benefits_Model_Benefit
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
