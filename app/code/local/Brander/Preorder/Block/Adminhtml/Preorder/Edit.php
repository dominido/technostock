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
 * Pre Order admin edit block
 *
 * @category	Brander
 * @package		Brander_Preorder
 * @author Ultimate Module Creator
 */
class Brander_Preorder_Block_Adminhtml_Preorder_Edit extends Mage_Adminhtml_Block_Widget_Form_Container{
	/**
	 * constuctor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function __construct(){
		parent::__construct();
		$this->_blockGroup = 'preorder';
		$this->_controller = 'adminhtml_preorder';
		$this->_updateButton('save', 'label', Mage::helper('preorder')->__('Save Cart'));
		$this->_updateButton('delete', 'label', Mage::helper('preorder')->__('Delete Cart'));
		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('preorder')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);
		$this->_formScripts[] = "
			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}
	/**
	 * get the edit form header
	 * @access public
	 * @return string
	 * @author Ultimate Module Creator
	 */
	public function getHeaderText(){
		if( Mage::registry('preorder_data') && Mage::registry('preorder_data')->getId() ) {
			return Mage::helper('preorder')->__("Edit Pre Order '%s'", $this->htmlEscape(Mage::registry('preorder_data')->getUserName()));
		} 
		else {
			return Mage::helper('preorder')->__('Add Cart');
		}
	}
}