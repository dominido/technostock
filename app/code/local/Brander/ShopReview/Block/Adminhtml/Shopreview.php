<?php
/**
 * Brander_ShopReview extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Brander
 * @package		Brander_ShopReview
 * @copyright  	Copyright (c) 2016
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Shop Review admin block
 *
 * @category    Brander
 * @package     Brander_ShopReview
 * @author      Ultimate Module Creator
 */
class Brander_ShopReview_Block_Adminhtml_Shopreview
    extends Mage_Adminhtml_Block_Widget_Grid_Container {
    /**
     * constructor
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function __construct(){
        $this->_controller         = 'adminhtml_shopreview';
        $this->_blockGroup         = 'brander_shopreview';
        parent::__construct();
        $this->_headerText         = Mage::helper('brander_shopreview')->__('Shop Review');
        $this->_updateButton('add', 'label', Mage::helper('brander_shopreview')->__('Add Shop Review'));

    }
}
