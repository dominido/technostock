<?php
class Brander_CustomerCallbacks_Block_Adminhtml_Callbacks extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct() {
        $this->_controller     = "adminhtml_callbacks";
        $this->_blockGroup     = "brander_customercallbacks";
        $this->_headerText     = Mage::helper("brander_customercallbacks")->__("Customer Callbacks Manager");
        parent::__construct();
    }

}