<?php
class Brander_AdminForms_Block_Adminhtml_Block_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_blockGroup = 'adminforms';

    protected function _prepareForm()
    {
        $this->_objectId    = 'entity_id';
        $this->_controller  = 'adminhtml_block';

        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post', 'enctype'=>'multipart/form-data'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
