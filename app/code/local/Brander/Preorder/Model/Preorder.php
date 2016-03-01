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
 * Pre Order model
 *
 * @category	Brander
 * @package		Brander_Preorder
 * @author Ultimate Module Creator
 */
class Brander_Preorder_Model_Preorder extends Mage_Core_Model_Abstract{
	/**
	 * Entity code.
	 * Can be used as part of method name for entity processing
	 */
	const ENTITY= 'preorder_preorder';
	const CACHE_TAG = 'preorder_preorder';
	/**
	 * Prefix of model events names
	 * @var string
	 */
	protected $_eventPrefix = 'preorder_preorder';
	
	/**
	 * Parameter name in event
	 * @var string
	 */
	protected $_eventObject = 'preorder';
	/**
	 * constructor
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function _construct(){
		parent::_construct();
		$this->_init('preorder/preorder');
	}
	/**
	 * before save pre order
	 * @access protected
	 * @return Brander_Preorder_Model_Preorder
	 * @author Ultimate Module Creator
	 */
	protected function _beforeSave(){
		parent::_beforeSave();
		$now = Mage::getSingleton('core/date')->gmtDate();
		if ($this->isObjectNew()){
			$this->setCreatedAt($now);
		}
		$this->setUpdatedAt($now);
		return $this;
	}
	/**
	 * save preorder relation
	 * @access public
	 * @return Brander_Preorder_Model_Preorder
	 * @author Ultimate Module Creator
	 */
	protected function _afterSave() {
		return parent::_afterSave();
	}
}