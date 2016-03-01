<?php

class Brander_NewPost_Block_Adminhtml_Warehouses extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_blockGroup = 'brander_newpost';
		$this->_controller = 'adminhtml_warehouses';
		$this->_headerText = $this->__('View warehouses');

		parent::__construct();

		$this->_removeButton('add');
		$this->_addButton('synchronize', array(
			'label'     => $this->__('Sync with API'),
			'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/sync') .'\')'
		));
	}
} 