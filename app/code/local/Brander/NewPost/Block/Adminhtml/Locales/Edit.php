<?php

class Brander_NewPost_Block_Adminhtml_Locales_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 * Init class
	 */
	public function __construct()
	{

		$this->_blockGroup = 'brander_newpost';
		$this->_controller = 'adminhtml_locales';
		$this->_headerText = $this->__('Edit locale');

		parent::__construct();

		$this->_updateButton('save', 'label', $this->__('Save locale'));
		$this->_removeButton('delete');
	}
} 