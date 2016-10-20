<?php
/**
 * Brander_ShopReview extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Brander
 * @package		Brander_ShopReview
 * @copyright  	Copyright (c) 2016
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Shop Review admin grid block
 *
 * @category    Brander
 * @package     Brander_ShopReview
 * @author      Ultimate Module Creator
 */
class Brander_ShopReview_Block_Adminhtml_Shopreview_Grid
    extends Mage_Adminhtml_Block_Widget_Grid {
    /**
     * constructor
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct(){
        parent::__construct();
        $this->setId('shopreviewGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    /**
     * prepare collection
     * @access protected
     * @return Brander_ShopReview_Block_Adminhtml_Shopreview_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareCollection(){
        $collection = Mage::getModel('brander_shopreview/shopreview')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    /**
     * prepare grid collection
     * @access protected
     * @return Brander_ShopReview_Block_Adminhtml_Shopreview_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareColumns(){
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('brander_shopreview')->__('Id'),
            'index'        => 'entity_id',
            'type'        => 'number'
        ));
        $this->addColumn('user_name', array(
            'header'    => Mage::helper('brander_shopreview')->__('User Name'),
            'align'     => 'left',
            'index'     => 'user_name',
        ));
        $this->addColumn('review_status', array(
            'header'    => Mage::helper('brander_shopreview')->__('Status Review'),
            'index'        => 'review_status',
            'type'        => 'options',
            'options'    => array(
                '0' => Mage::helper('brander_shopreview')->__('New'),
                '2' => Mage::helper('brander_shopreview')->__('Approved'),
                '3' => Mage::helper('brander_shopreview')->__('Canceled'),
            )
        ));
        $this->addColumn('subject_review', array(
            'header'=> Mage::helper('brander_shopreview')->__('Subject Review'),
            'index' => 'subject_review',
            'type'=> 'text',

        ));
        $this->addColumn('user_phone', array(
            'header'=> Mage::helper('brander_shopreview')->__('User Phone'),
            'index' => 'user_phone',
            'type'=> 'text',

        ));
        $this->addColumn('user_email', array(
            'header'=> Mage::helper('brander_shopreview')->__('User Email'),
            'index' => 'user_email',
            'type'=> 'text',

        ));
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('brander_shopreview')->__('Created at'),
            'index'     => 'created_at',
            'width'     => '120px',
            'type'      => 'text',
        ));
        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('brander_shopreview')->__('Updated at'),
            'index'     => 'updated_at',
            'width'     => '120px',
            'type'      => 'text',
        ));
        $this->addColumn('action',
            array(
                'header'=>  Mage::helper('brander_shopreview')->__('Action'),
                'width' => '100',
                'type'  => 'action',
                'getter'=> 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('brander_shopreview')->__('Edit'),
                        'url'   => array('base'=> '*/*/edit'),
                        'field' => 'id'
                    )
                ),
                'filter'=> false,
                'is_system'    => true,
                'sortable'  => false,
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('brander_shopreview')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('brander_shopreview')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('brander_shopreview')->__('XML'));
        return parent::_prepareColumns();
    }
    /**
     * prepare mass action
     * @access protected
     * @return Brander_ShopReview_Block_Adminhtml_Shopreview_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareMassaction(){
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('shopreview');
        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('brander_shopreview')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('brander_shopreview')->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('brander_shopreview')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'status' => array(
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('brander_shopreview')->__('Status'),
                        'values' => array(
                                '1' => Mage::helper('brander_shopreview')->__('Enabled'),
                                '0' => Mage::helper('brander_shopreview')->__('Disabled'),
                        )
                )
            )
        ));
        $this->getMassactionBlock()->addItem('review_status', array(
            'label'=> Mage::helper('brander_shopreview')->__('Change Review Status'),
            'url'  => $this->getUrl('*/*/massReviewStatus', array('_current'=>true)),
            'additional' => array(
                'flag_review_status' => array(
                        'name' => 'flag_review_status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('brander_shopreview')->__('Review Status'),
                        'values' => Mage::getModel('brander_shopreview/shopreview_attribute_source_reviewstatus')->getAllOptions(true),

                )
            )
        ));
        return $this;
    }
    /**
     * get the row url
     * @access public
     * @param Brander_ShopReview_Model_Shopreview
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRowUrl($row){
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    /**
     * get the grid url
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getGridUrl(){
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
    /**
     * after collection load
     * @access protected
     * @return Brander_ShopReview_Block_Adminhtml_Shopreview_Grid
     * @author Ultimate Module Creator
     */
    protected function _afterLoadCollection(){
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}
