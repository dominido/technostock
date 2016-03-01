<?php

class Brander_AdminForms_Block_Adminhtml_Block_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_blockGroup = 'adminforms';
	protected $_additionalElementTypes = array();

    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        if (($block = $this->getLayout()->getBlock('head'))) {
            $block->setCanLoadTinyMce(true);
        }


        if ($group = $this->getGroup()) {
            $form = new Varien_Data_Form();
            /**
             * Initialize block object as form property
             * for using it in elements generation
             */
            $form->setDataObject(Mage::registry('block'));

            $fieldset = $form->addFieldset('group_fields'.$group->getId(),
                array(
                    'legend'=>Mage::helper('adminforms')->__($group->getAttributeGroupName()),
                    'class'=>'fieldset-wide',
            ));

            $attributes = $this->getGroupAttributes();

            Mage::dispatchEvent('block_edit_prepare_form_additional_element_types', array('form'=>$form, 'tab_attributes'=>$this));
			Mage::dispatchEvent('block_edit_prepare_form_additional_element_types_'.Mage::registry('block')->getEntityType(), array('form'=>$form,'tab_attributes'=>$this,'block'=>Mage::registry('block')));

            $this->_setFieldset($attributes, $fieldset, array('gallery'));

            $values = Mage::registry('block')->getData();
            /**
             * Set attribute default values for new block
             */
            if (!Mage::registry('block')->getId()) {
                foreach ($attributes as $attribute) {
                    if (!isset($values[$attribute->getAttributeCode()])) {
                        $values[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                    }
                }
            }
            else {
                foreach ($attributes as $attribute) {
                    if (isset($values[$attribute->getAttributeCode()])) {
                        $values[$attribute->getAttributeCode()] = $attribute->getFrontendInput()=='image'?Mage::registry('block')->getImageUrl($values[$attribute->getAttributeCode()]):$values[$attribute->getAttributeCode()];
                    }
                }
            }
            if (Mage::registry('block')->hasLockedAttributes()) {
                foreach (Mage::registry('block')->getLockedAttributes() as $attribute) {
                    if ($element = $form->getElement($attribute)) {
                        $element->setReadonly(true, true);
                    }
                }
            }

            Mage::dispatchEvent('block_edit_prepare_form', array('form'=>$form));
			Mage::dispatchEvent('block_edit_prepare_form_'.Mage::registry('block')->getEntityType(), array('form'=>$form,'tab_attributes'=>$this));

            $form->addValues($values);
            $form->setFieldNameSuffix('block');
            $this->setForm($form);
        }
    }
	
	public function addAdditionalElementTypes($type,$blockClassName)
	{
		$this->_additionalElementTypes[$type] = $blockClassName;
	}

    /**
     * Retrieve Additional Element Types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        $array = array_merge(array(
            'textarea' => Mage::getConfig()->getBlockClassName('adminforms/adminhtml_helper_form_wysiwyg'),
            'file' => Mage::getConfig()->getBlockClassName('adminforms/adminhtml_helper_form_file'),
            'image' => Mage::getConfig()->getBlockClassName('adminforms/adminhtml_helper_form_image')
        ),$this->_additionalElementTypes);
		return $array;
    }
}
