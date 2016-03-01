<?php

class Brander_NewPost_Block_Adminhtml_Locales extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_blockGroup = 'brander_newpost';
		$this->_controller = 'adminhtml_locales';
		$this->_headerText = $this->__('Manage locales');

		parent::__construct();

		$this->_removeButton('add');
	}
} 