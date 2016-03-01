<?php
/**
 * @author
 * @copyright Copyright (c) 2015
 * @package Brander_LayeredNavigation
 */

/**
 *
 */   
class Brander_LayeredNavigation_Block_Adminhtml_Range extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_range';
    $this->_blockGroup = 'brander_layerednavigation';
    $this->_headerText = Mage::helper('brander_layerednavigation')->__('Ranges');
    $this->_addButtonLabel = Mage::helper('brander_layerednavigation')->__('Add Range');
    parent::__construct();
  }
}