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
 * Pre Order admin block
 *
 * @category	Brander
 * @package		Brander_Preorder
 * @author Ultimate Module Creator
 */
class Brander_Preorder_Block_Adminhtml_Preorder extends Mage_Adminhtml_Block_Widget_Grid_Container{
	/**
	 * constructor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function __construct(){
		$this->_controller 		= 'adminhtml_preorder';
		$this->_blockGroup 		= 'preorder';
		$this->_headerText 		= Mage::helper('preorder')->__('One Click Buy');
//		$this->_addButtonLabel 	= Mage::helper('preorder')->__('Add Pre Order');
		parent::__construct();
        $this->_removeButton('add');
	}
}