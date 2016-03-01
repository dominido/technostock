<?php
class Brander_CustomerCallbacks_Block_Adminhtml_Callbacks_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'brander_customercallbacks';
        $this->_mode = 'callbacks_form';
        $this->_controller = 'adminhtml';
    }

    public function getHeaderText()
    {
        $callback = Mage::registry('callbacks_data');
        if ($callback->getId()) {
            return Mage::helper('brander_customercallbacks')->__("Edit Item '%s'", $this->escapeHtml($callback->getId()));
        } else {
            return Mage::helper('brander_customercallbacks')->__("Add new Callback");
        }
    }
}