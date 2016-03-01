<?php
/**
 * @author
 * @copyright Copyright (c) 2015
 * @package Brander_LayeredNavigation
 */

class Brander_LayeredNavigation_Block_Adminhtml_Filter_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'filer_id';
        $this->_blockGroup = 'brander_layerednavigation';
        $this->_controller = 'adminhtml_filter';

        parent::__construct();
        $this->_removeButton('reset');

        $this->_addButton('save_and_continue', array(
            'label'     => Mage::helper('brander_layerednavigation')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class' => 'save'
        ), 10);
        $this->_formScripts[] = "function saveAndContinueEdit(){ editForm.submit($('edit_form').action + 'continue/edit') }";
    }

    public function getHeaderText()
    {
        $model = Mage::registry('brander_layerednavigation_filter');

        if ($model) {
            $attribute =  Mage::getModel('eav/entity_attribute')->load($model->getAttributeId());
            return Mage::helper('brander_layerednavigation')->__('Edit Filter "' . $attribute->getFrontendLabel() . '" Properties');
        }
    }
}
