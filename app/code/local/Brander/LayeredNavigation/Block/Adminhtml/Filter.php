<?php

class Brander_LayeredNavigation_Block_Adminhtml_Filter extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_filter';
        $this->_headerText = Mage::helper('brander_layerednavigation')->__('Manage Filters');
        $this->_blockGroup = 'brander_layerednavigation';
        $this->_addButtonLabel = Mage::helper('brander_layerednavigation')->__('Load');
        parent::__construct();
    }
}