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
 * File Grid admin block
 *
 * @category	Brander
 * @package		Brander_FileImport
 * @author Ultimate Module Creator
 */
class Brander_FileImport_Block_Adminhtml_Filegrid extends Mage_Adminhtml_Block_Widget_Grid_Container{
	/**
	 * constructor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function __construct(){

		$this->_controller 		= 'adminhtml_filegrid';
		$this->_blockGroup 		= 'fileimport';
		$this->_headerText 		= Mage::helper('fileimport')->__('Load file');
		$this->_addButtonLabel 	= Mage::helper('fileimport')->__('Add File');
		parent::__construct();
	}
}