<?php
/**
 * Brander OurContacts extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        OurContacts
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_OurContacts_Block_Adminhtml_Contact_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('contactGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return Brander_OurContacts_Block_Adminhtml_Contact_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('brander_ourcontacts/contact')
            ->getCollection()
            ->addAttributeToSelect('phone')
            ->addAttributeToSelect('show_in_header')
            ->addAttributeToSelect('position')
            ->addAttributeToSelect('status');
        
        $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
        $store = $this->_getStore();
        $collection->joinAttribute(
            'title', 
            'brander_ourcontacts_contact/title', 
            'entity_id', 
            null, 
            'inner', 
            $adminStore
        );
        if ($store->getId()) {
            $collection->joinAttribute(
                'brander_ourcontacts_contact_title',
                'brander_ourcontacts_contact/title', 
                'entity_id', 
                null, 
                'inner', 
                $store->getId()
            );
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return Brander_OurContacts_Block_Adminhtml_Contact_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('brander_ourcontacts')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );

        
        if ($this->_getStore()->getId()) {
            $this->addColumn(
                'brander_ourcontacts_contact_title', 
                array(
                    'header'    => Mage::helper('brander_ourcontacts')->__('Title in %s', $this->_getStore()->getName()),
                    'align'     => 'left',
                    'index'     => 'brander_ourcontacts_contact_title',
                )
            );
        }
        else {
            $this->addColumn(
                'title',
                array(
                    'header'    => Mage::helper('brander_ourcontacts')->__('Title'),
                    'align'     => 'left',
                    'index'     => 'title',
                )
            );
        }

        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('brander_ourcontacts')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('brander_ourcontacts')->__('Enabled'),
                    '0' => Mage::helper('brander_ourcontacts')->__('Disabled'),
                )
            )
        );
        $this->addColumn(
            'phone',
            array(
                'header' => Mage::helper('brander_ourcontacts')->__('Phone'),
                'index'  => 'phone',
                'type'=> 'text',

            )
        );
        $this->addColumn(
            'show_in_header',
            array(
                'header' => Mage::helper('brander_ourcontacts')->__('Show in Header'),
                'index'  => 'show_in_header',
                'type'    => 'options',
                    'options'    => array(
                    '1' => Mage::helper('brander_ourcontacts')->__('Yes'),
                    '0' => Mage::helper('brander_ourcontacts')->__('No'),
                )

            )
        );
        $this->addColumn(
            'position',
            array(
                'header' => Mage::helper('brander_ourcontacts')->__('Position'),
                'index'  => 'position',
                'type'=> 'number',

            )
        );
        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('brander_ourcontacts')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('brander_ourcontacts')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('brander_ourcontacts')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('brander_ourcontacts')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('brander_ourcontacts')->__('XML'));
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
     * @return Brander_OurContacts_Block_Adminhtml_Contact_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('contact');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('brander_ourcontacts')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('brander_ourcontacts')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('brander_ourcontacts')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('brander_ourcontacts')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('brander_ourcontacts')->__('Enabled'),
                            '0' => Mage::helper('brander_ourcontacts')->__('Disabled'),
                        )
                    )
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'show_in_header',
            array(
                'label'      => Mage::helper('brander_ourcontacts')->__('Change Show in Header'),
                'url'        => $this->getUrl('*/*/massShowInHeader', array('_current'=>true)),
                'additional' => array(
                    'flag_show_in_header' => array(
                        'name'   => 'flag_show_in_header',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('brander_ourcontacts')->__('Show in Header'),
                        'values' => array(
                                '1' => Mage::helper('brander_ourcontacts')->__('Yes'),
                                '0' => Mage::helper('brander_ourcontacts')->__('No'),
                            )

                    )
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'show_in_footer',
            array(
                'label'      => Mage::helper('brander_ourcontacts')->__('Change Show In Footer'),
                'url'        => $this->getUrl('*/*/massShowInFooter', array('_current'=>true)),
                'additional' => array(
                    'flag_show_in_footer' => array(
                        'name'   => 'flag_show_in_footer',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('brander_ourcontacts')->__('Show In Footer'),
                        'values' => array(
                                '1' => Mage::helper('brander_ourcontacts')->__('Yes'),
                                '0' => Mage::helper('brander_ourcontacts')->__('No'),
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
     * @param Brander_OurContacts_Model_Contact
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
