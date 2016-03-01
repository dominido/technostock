<?php
/**
 * Brander_Preorder extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Brander
 * @package		Brander_Preorder
 * @copyright  	Copyright (c) 2015
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Pre Order admin grid block
 *
 * @category	Brander
 * @package		Brander_Preorder
 * @author Ultimate Module Creator
 */
class Brander_Preorder_Block_Adminhtml_Preorder_Grid extends Mage_Adminhtml_Block_Widget_Grid{
	/**
	 * constructor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function __construct(){
		parent::__construct();
		$this->setId('preorderGrid');
		$this->setDefaultSort('entity_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	/**
	 * prepare collection
	 * @access protected
	 * @return Brander_Preorder_Block_Adminhtml_Preorder_Grid
	 * @author Ultimate Module Creator
	 */
	protected function _prepareCollection(){
		$collection = Mage::getModel('preorder/preorder')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	/**
	 * prepare grid collection
	 * @access protected
	 * @return Brander_Preorder_Block_Adminhtml_Preorder_Grid
	 * @author Ultimate Module Creator
	 */
	protected function _prepareColumns(){
		$this->addColumn('entity_id', array(
			'header'	=> Mage::helper('preorder')->__('Id'),
			'index'		=> 'entity_id',
			'type'		=> 'number'
		));
		$this->addColumn('user_name', array(
			'header'=> Mage::helper('preorder')->__('User Name'),
			'index' => 'user_name',
			'type'	 	=> 'text',

		));
		$this->addColumn('user_phone', array(
			'header'=> Mage::helper('preorder')->__('User Phone'),
			'index' => 'user_phone',
			'type'	 	=> 'text',

		));
		$this->addColumn('product_id', array(
			'header'=> Mage::helper('preorder')->__('Product'),
			'index' => 'product_id',
//			'type'	 	=> 'text',
            'renderer'  => 'Brander_Preorder_Block_Adminhtml_Renderer_ProductGrid'

		));
		$this->addColumn('product_qty', array(
			'header'=> Mage::helper('preorder')->__('Qty'),
			'index' => 'product_qty',
			'type'	 	=> 'text',

		));
        $this->addColumn('status', array(
            'header'        => Mage::helper('preorder')->__('Status'),
            'index'         => 'status',
            'type'          => 'options',
            'options'       => array(
                '1' => Mage::helper('preorder')->__('Canceled'),
                '0' => Mage::helper('preorder')->__('New'),
                '2' => Mage::helper('preorder')->__('Approved'),
            )
        ));
		$this->addColumn('created_at', array(
			'header'	=> Mage::helper('preorder')->__('Created at'),
			'index' 	=> 'created_at',
			'width' 	=> '120px',
			'type'  	=> 'datetime',
		));
		$this->addColumn('updated_at', array(
			'header'	=> Mage::helper('preorder')->__('Updated at'),
			'index' 	=> 'updated_at',
			'width' 	=> '120px',
			'type'  	=> 'datetime',
		));
		$this->addColumn('action',
			array(
				'header'=>  Mage::helper('preorder')->__('Action'),
				'width' => '100',
				'type'  => 'action',
				'getter'=> 'getId',
				'actions'   => array(
					array(
						'caption'   => Mage::helper('preorder')->__('Edit'),
						'url'   => array('base'=> '*/*/edit'),
						'field' => 'id'
					)
				),
				'filter'=> false,
				'is_system'	=> true,
				'sortable'  => false,
		));
		$this->addExportType('*/*/exportCsv', Mage::helper('preorder')->__('CSV'));
		$this->addExportType('*/*/exportExcel', Mage::helper('preorder')->__('Excel'));
		$this->addExportType('*/*/exportXml', Mage::helper('preorder')->__('XML'));
		return parent::_prepareColumns();
	}
	/**
	 * prepare mass action
	 * @access protected
	 * @return Brander_Preorder_Block_Adminhtml_Preorder_Grid
	 * @author Ultimate Module Creator
	 */
	protected function _prepareMassaction(){
		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setFormFieldName('preorder');
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> Mage::helper('preorder')->__('Delete'),
			'url'  => $this->getUrl('*/*/massDelete'),
			'confirm'  => Mage::helper('preorder')->__('Are you sure?')
		));
		return $this;
	}
	/**
	 * get the row url
	 * @access public
	 * @param Brander_Preorder_Model_Preorder
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
	 * @return Brander_Preorder_Block_Adminhtml_Preorder_Grid
	 * @author Ultimate Module Creator
	 */
	protected function _afterLoadCollection(){
		$this->getCollection()->walk('afterLoad');
		parent::_afterLoadCollection();
	}
	/**
	 * filter store column
	 * @access protected
	 * @param Brander_Preorder_Model_Resource_Preorder_Collection $collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
	 * @return Brander_Preorder_Block_Adminhtml_Preorder_Grid
	 * @author Ultimate Module Creator
	 */
	protected function _filterStoreCondition($collection, $column){
		if (!$value = $column->getFilter()->getValue()) {
        	return;
		}
		$collection->addStoreFilter($value);
		return $this;
    }
}