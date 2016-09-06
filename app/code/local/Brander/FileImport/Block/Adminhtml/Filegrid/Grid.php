<?php
/**
 * Brander_FileImport extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Brander
 * @package		Brander_FileImport
 * @copyright  	Copyright (c) 2014
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * File Grid admin grid block
 *
 * @category	Brander
 * @package		Brander_FileImport
 */
class Brander_FileImport_Block_Adminhtml_Filegrid_Grid extends Mage_Adminhtml_Block_Widget_Grid{
	/**
	 * constructor
	 * @access public
	 * @return void
	 */
	public function __construct(){
		parent::__construct();
		$this->setId('filegridGrid');
		$this->setDefaultSort('entity_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	/**
	 * prepare collection
	 * @access protected
	 * @return Brander_FileImport_Block_Adminhtml_Filegrid_Grid
	 */
	protected function _prepareCollection(){
		$collection = Mage::getModel('fileimport/filegrid')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	/**
	 * prepare grid collection
	 * @access protected
	 * @return Brander_FileImport_Block_Adminhtml_Filegrid_Grid
	 * @author Ultimate Module Creator
	 */
	protected function _prepareColumns(){
		$this->addColumn('entity_id', array(
			'header'	=> Mage::helper('fileimport')->__('Id'),
			'index'		=> 'entity_id',
			'type'		=> 'number'
		));
        $this->addColumn('file_csv_name', array(
            'header'=> Mage::helper('fileimport')->__('File name'),
            'index' => 'file_csv_name',
            'type'	 	=> 'text',

        ));

		$this->addColumn('path', array(
			'header' => Mage::helper('fileimport')->__('File URL'),
			'index'  => 'path',
			'type'   => 'text',
			'renderer' => 'Brander_FileImport_Block_Adminhtml_Filegrid_Renderer_Path'
		));

		$this->addColumn('file_size', array(
			'header'=> Mage::helper('fileimport')->__('Size file'),
			'index' => 'file_size',
			'type'	 	=> 'text',
			'width' 	=> '120px',
		));

/*		if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_id', array(
				'header'=> Mage::helper('fileimport')->__('Store Views'),
				'index' => 'store_id',
				'type'  => 'store',
				'store_all' => true,
				'store_view'=> true,
				'sortable'  => false,
				'filter_condition_callback'=> array($this, '_filterStoreCondition'),
			));
		}*/
		$this->addColumn('created_at', array(
			'header'	=> Mage::helper('fileimport')->__('Created at'),
			'index' 	=> 'created_at',
			'width' 	=> '120px',
			'type'  	=> 'datetime',
		));
		$this->addColumn('updated_at', array(
			'header'	=> Mage::helper('fileimport')->__('Updated at'),
			'index' 	=> 'updated_at',
			'width' 	=> '120px',
			'type'  	=> 'datetime',
		));
/*		$this->addColumn('action',
			array(
				'header'=>  Mage::helper('fileimport')->__('Action'),
				'width' => '100',
				'type'  => 'action',
				'getter'=> 'getId',
				'actions'   => array(
					array(
						'caption'   => Mage::helper('fileimport')->__('Edit'),*/
//						'url'   => array('base'=> '*/*/edit'),
/*						'field' => 'id'
					)
				),
				'filter'=> false,
				'is_system'	=> true,
				'sortable'  => false,
		));*/

//		$this->addExportType('*/*/exportCsv', Mage::helper('fileimport')->__('CSV'));
//		$this->addExportType('*/*/exportExcel', Mage::helper('fileimport')->__('Excel'));
//		$this->addExportType('*/*/exportXml', Mage::helper('fileimport')->__('XML'));
		return parent::_prepareColumns();
	}
	/**
	 * prepare mass action
	 * @access protected
	 * @return Brander_FileImport_Block_Adminhtml_Filegrid_Grid
	 * @author Ultimate Module Creator
	 */
	protected function _prepareMassaction(){
		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setFormFieldName('filegrid');
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> Mage::helper('fileimport')->__('Delete'),
			'url'  => $this->getUrl('*/*/massDelete'),
			'confirm'  => Mage::helper('fileimport')->__('Are you sure?')
		));
		return $this;
	}
	/**
	 * get the row url
	 * @access public
	 * @param Brander_FileImport_Model_Filegrid
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
	 * @return Brander_FileImport_Block_Adminhtml_Filegrid_Grid
	 * @author Ultimate Module Creator
	 */
	protected function _afterLoadCollection(){
		$this->getCollection()->walk('afterLoad');
		parent::_afterLoadCollection();
	}
	/**
	 * filter store column
	 * @access protected
	 * @param Brander_FileImport_Model_Resource_Filegrid_Collection $collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
	 * @return Brander_FileImport_Block_Adminhtml_Filegrid_Grid
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