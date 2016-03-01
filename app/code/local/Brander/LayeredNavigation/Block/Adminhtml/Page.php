<?php

class Brander_LayeredNavigation_Block_Adminhtml_Page extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_page';
    $this->_blockGroup = 'brander_layerednavigation';
    $this->_headerText = Mage::helper('brander_layerednavigation')->__('Pages');
    $this->_addButtonLabel = Mage::helper('brander_layerednavigation')->__('Add Page');
    parent::__construct();
  }
}


