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
 * File Grid admin edit tabs
 *
 * @category	Brander
 * @package		Brander_FileImport
 * @author Ultimate Module Creator
 */
class Brander_FileImport_Block_Adminhtml_Filegrid_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{
	/**
	 * constructor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function __construct(){
		parent::__construct();
		$this->setId('filegrid_tabs');
		$this->setDestElementId('edit_form');

	}
	/**
	 * before render html
	 * @access protected
	 * @return Brander_FileImport_Block_Adminhtml_Filegrid_Edit_Tabs
	 * @author Ultimate Module Creator
	 */
	protected function _beforeToHtml(){
		$this->addTab('form_filegrid', array(
			'label'		=> Mage::helper('fileimport')->__('File'),
			'title'		=> Mage::helper('fileimport')->__('File'),
			'content' 	=> $this->getLayout()->createBlock('fileimport/adminhtml_filegrid_edit_tab_form')->toHtml(),
		));
		if (!Mage::app()->isSingleStoreMode()){
			$this->addTab('form_store_filegrid', array(
				'label'		=> Mage::helper('fileimport')->__('Store views'),
				'title'		=> Mage::helper('fileimport')->__('Store views'),
				'content' 	=> $this->getLayout()->createBlock('fileimport/adminhtml_filegrid_edit_tab_stores')->toHtml(),
			));
		}
		return parent::_beforeToHtml();
	}
}