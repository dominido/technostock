<?php
class Brander_CustomerCallbacks_Block_Adminhtml_Callbacks_Form_Form extends Mage_Adminhtml_Block_Widget_Form {
    protected function _prepareForm() {
        $type     = Mage::registry('callbacks_data');
        $form     = new Varien_Data_Form();
        $fieldset = $form->addFieldset('edit_callbacks', array(
            'legend' => Mage::helper('brander_customercallbacks')->__('Details')
        ));

        if ($type->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name'     => 'id',
                'required' => true
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name'     => 'name',
            'label'    => Mage::helper('brander_customercallbacks')->__('Customer Name'),
            'required' => true,
        ));

        $fieldset->addField('phone', 'text', array(
            'name'     => 'phone',
            'label'    => Mage::helper('brander_customercallbacks')->__('Customer Telephone'),
            'required' => true,
        ));

        $fieldset->addField('created_at', 'date', array(
            'name'     => 'created_at',
            'format'    => 'yyyy-MM-dd HH:mm',
            'input_format'  => 'yyyy-MM-dd HH:mm:ss',
            'disabled' => true,
            'readonly' => true,
            'required' => true,
            'label'    => Mage::helper('brander_customercallbacks')->__('Created At'),
        ));

        $fieldset->addField('current_url', 'note', array(
            'name'     => 'current_url',
            'readonly' => true,
            'required' => false,
            'label'    => Mage::helper('brander_customercallbacks')->__('Page, were Callback called'),
            'text'     => $type['current_url']
        ));

        if (Mage::getStoreConfig('customercallbacks/settings/show_comment_field')) {
            $fieldset->addField('comments', 'textarea', array(
                'name'     => 'comments',
                'readonly' => true,
                'required' => false,
                'label'    => Mage::helper('brander_customercallbacks')->__('Customer Comment'),
            ));
        }

        $fieldset = $form->addFieldset('administrate', array(
                'legend' => Mage::helper('brander_customercallbacks')->__('Administrate'))
        );

        $fieldset->addField('status', 'select', array(
            'name'     => 'status',
            'label'    => Mage::helper('brander_customercallbacks')->__('Status'),
            'values'    => Mage::getModel('brander_customercallbacks/source_status')->toOptionArray(),
            'required' => true,
            'class' => 'required-entry',
        ));

        $fieldset->addField('modify_at', 'date', array(
            'name'     => 'modify_at',
            'format'    => 'yyyy-MM-dd HH:mm',
            'input_format'  => 'yyyy-MM-dd HH:mm:ss',
            'label'    => Mage::helper('brander_customercallbacks')->__('Modify At'),
            'required' => false,
            'readonly' => true,
        ));

        $fieldset->addField('operator_comment', 'textarea', array(
            'name'     => 'operator_comment',
            'label'    => Mage::helper('brander_customercallbacks')->__('Operator Comment'),
            'required' => false,
        ));

        if ($type['created_at']) {
            $type['created_at'] = Mage::getModel('core/date')->timestamp(strtotime($type['created_at']));
        }

        if ($type['modify_at']) {
            $type['modify_at'] = Mage::getModel('core/date')->timestamp(strtotime($type['modify_at']));
        }

        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save'));
        $form->setValues($type->getData());

        $this->setForm($form);
    }
}