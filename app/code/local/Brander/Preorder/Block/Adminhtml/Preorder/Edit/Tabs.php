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
 * Pre Order admin edit tabs
 *
 * @category	Brander
 * @package		Brander_Preorder
 * @author Ultimate Module Creator
 */
class Brander_Preorder_Block_Adminhtml_Preorder_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{
	/**
	 * constructor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function __construct(){
		parent::__construct();
		$this->setId('preorder_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('preorder')->__('One click'));
	}
	/**
	 * before render html
	 * @access protected
	 * @return Brander_Preorder_Block_Adminhtml_Preorder_Edit_Tabs
	 * @author Ultimate Module Creator
	 */
	protected function _beforeToHtml(){
		$this->addTab('form_preorder', array(
			'label'		=> Mage::helper('preorder')->__('One click'),
			'title'		=> Mage::helper('preorder')->__('One click'),
			'content' 	=> $this->getLayout()->createBlock('preorder/adminhtml_preorder_edit_tab_form')->toHtml(),
		));
		/*if (!Mage::app()->isSingleStoreMode()){
			$this->addTab('form_store_preorder', array(
				'label'		=> Mage::helper('preorder')->__('Store views'),
				'title'		=> Mage::helper('preorder')->__('Store views'),
				'content' 	=> $this->getLayout()->createBlock('preorder/adminhtml_preorder_edit_tab_stores')->toHtml(),
			));
		}*/
		return parent::_beforeToHtml();
	}
}