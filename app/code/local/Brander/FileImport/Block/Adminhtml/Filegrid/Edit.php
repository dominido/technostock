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

class Brander_FileImport_Block_Adminhtml_Filegrid_Edit extends Mage_Adminhtml_Block_Widget_Form_Container{

	public function __construct()
	{
		parent::__construct();
		$this->_blockGroup = 'fileimport';
		$this->_controller = 'adminhtml_filegrid';
		if (Mage::helper('fileimport/data')->checkForEditMode()){
			$this->_removeButton('save');
			$this->_removeButton('delete');
		}
		else {
			$this->_updateButton('save', 'label', Mage::helper('fileimport')->__('Save File'));
		}
		$this->_updateButton('delete', 'label', Mage::helper('fileimport')->__('Delete File'));

	}

	public function getHeaderText()
	{
		if (Mage::helper('fileimport/data')->checkForEditMode()) {
			return Mage::helper('fileimport')->__("Edit File '%s'", $this->htmlEscape(Mage::registry( 'filegrid_data' )->getFileCsvName()));
		}
		else {
			return Mage::helper('fileimport')->__('Add File Grid');
		}
	}


}