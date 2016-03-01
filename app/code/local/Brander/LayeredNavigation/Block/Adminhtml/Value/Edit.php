<?php
/**
 * @author
 * @copyright Copyright (c) 2015
 * @package Brander_LayeredNavigation
 */

class Brander_LayeredNavigation_Block_Adminhtml_Value_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'brander_layerednavigation';
        $this->_controller = 'adminhtml_value';

        $this->_removeButton('reset');
        $this->_removeButton('delete');

        $this->_addButton('save_and_continue', array(
            'label'     => Mage::helper('brander_layerednavigation')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class' => 'save'
        ), 10);
        $this->_formScripts[] = "function saveAndContinueEdit(){ editForm.submit($('edit_form').action + 'continue/edit') }";

        $this->_formScripts[] = " function featured(sel) {

            if (sel.value ==  1) {
                sel.up('tr').next('tr').show();
            } else {
                sel.up('tr').next('tr').hide();
            }

        }featured($('is_featured'));";
    }

    public function getHeaderText()
    {
        return Mage::helper('brander_layerednavigation')->__('Option Properties');
    }

    public function getBackUrl()
    {
        /** @var Brander_LayeredNavigation_Model_Value $value */
        $value = Mage::registry('brander_layerednavigation_value');
        return $this->getUrl('brander_layerednavigation/adminhtml_filter/edit', array('id' => $value->getFilterId()));
    }
}