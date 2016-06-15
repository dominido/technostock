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

class Brander_AutoImport_Block_Adminhtml_Importgrid_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct(){
		parent::__construct();
		$this->setId('importGrid');
		$this->setDefaultSort('entity_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}

	protected function _prepareCollection(){
		$collection = Mage::getModel('autoimport/importgrid')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns(){

		$this->addColumn('entity_id', array(
			'header'	=> Mage::helper('autoimport')->__('Id'),
			'index'		=> 'entity_id',
			'type'		=> 'number'
		));

        $this->addColumn('planned_at', array(
            'header'	=> Mage::helper('autoimport')->__('Planned time'),
            'index' 	=> 'planned_at',
            'width' 	=> '120px',
            'type'  	=> 'datetime',
        ));

        $this->addColumn('import_type', array(
            'header'=> Mage::helper('autoimport')->__('Import Type'),
            'index' => 'import_type',
            'type'	 	=> 'text',
            'renderer'  => 'Brander_AutoImport_Block_Adminhtml_Importgrid_Renderer_ImportType'
        ));

        $this->addColumn('file_type', array(
            'header'=> Mage::helper('autoimport')->__('File Import Type'),
            'index' => 'file_type',
            'type'	 	=> 'text',
            'renderer'  => 'Brander_AutoImport_Block_Adminhtml_Importgrid_Renderer_FileType'
        ));

		$this->addColumn('start_at', array(
			'header'	=> Mage::helper('autoimport')->__('Start time'),
			'index' 	=> 'start_at',
			'width' 	=> '120px',
			'type'  	=> 'datetime',
		));

        $this->addColumn('finish_at', array(
                'header'	=> Mage::helper('autoimport')->__('Finish time'),
                'index' 	=> 'finish_at',
                'width' 	=> '120px',
                'type'  	=> 'datetime',
            ));

		$this->addColumn('import_status', array(
			'header'=> Mage::helper('autoimport')->__('Import status'),
			'index' => 'import_status',
			'type'	 	=> 'text',
            'renderer'  => 'Brander_AutoImport_Block_Adminhtml_Importgrid_Renderer_Status'
		));

/*		$this->addColumn('log_filename', array(
			'header'=> Mage::helper('autoimport')->__('Log file'),
			'index' => 'log_filename',
			'type'	 	=> 'text',
		));*/

        $this->addColumn('updated_at', array(
            'header'	=> Mage::helper('autoimport')->__('Create/Update time'),
            'index' 	=> 'updated_at',
            'width' 	=> '120px',
            'type'  	=> 'datetime',
        ));

        /*$link= '/var/log/$log_filename';


        $link = $this->getViewFileUrl('/var/log/$log_filename');

        $this->addColumn('action_read', array(
            'header'   => $this->helper('catalog')->__('Action'),
            'width'    => 15,
            'sortable' => false,
            'filter'   => false,
            'type'     => 'action',
            'actions'  => array(
                array(
                    'url'     => $linkFileName,
                    'caption' => $this->helper('catalog')->__('Read'),
                ),
            )
        ));*/

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction(){
		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setFormFieldName('importgrid');
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> Mage::helper('autoimport')->__('Delete'),
			'url'  => $this->getUrl('*/*/massDelete'),
			'confirm'  => Mage::helper('autoimport')->__('Are you sure?')
		));
		return $this;
	}

	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

	public function getGridUrl(){
		return $this->getUrl('*/*/grid', array('_current'=>true));
	}

	protected function _afterLoadCollection(){
		$this->getCollection()->walk('afterLoad');
		parent::_afterLoadCollection();
	}

	protected function _filterStoreCondition($collection, $column){
		if (!$value = $column->getFilter()->getValue()) {
        	return;
		}
		$collection->addStoreFilter($value);
		return $this;
    }
}