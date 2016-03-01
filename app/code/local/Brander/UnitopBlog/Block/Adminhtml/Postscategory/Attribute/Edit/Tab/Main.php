<?php
/**
 * Brander UnitopBlog extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        UnitopBlog
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_UnitopBlog_Block_Adminhtml_Postscategory_Attribute_Edit_Tab_Main extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
{
    /**
     * Adding product form elements for editing attribute
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Adminhtml_Postscategory_Attribute_Edit_Tab_Main

     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $attributeObject = $this->getAttributeObject();
        $form = $this->getForm();
        $fieldset = $form->getElement('base_fieldset');
        $frontendInputElm = $form->getElement('frontend_input');
        $additionalTypes = array(
            array(
                'value' => 'image',
                'label' => Mage::helper('brander_unitopblog')->__('Image')
            ),
            array(
                'value' => 'file',
                'label' => Mage::helper('brander_unitopblog')->__('File')
            )
        );
        $response = new Varien_Object();
        $response->setTypes(array());
        Mage::dispatchEvent('adminhtml_postscategory_attribute_types', array('response'=>$response));
        $_disabledTypes = array();
        $_hiddenFields = array();
        foreach ($response->getTypes() as $type) {
            $additionalTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
            if (isset($type['disabled_types'])) {
                $_disabledTypes[$type['value']] = $type['disabled_types'];
            }
        }
        Mage::register('attribute_type_hidden_fields', $_hiddenFields);
        Mage::register('attribute_type_disabled_types', $_disabledTypes);

        $frontendInputValues = array_merge($frontendInputElm->getValues(), $additionalTypes);
        $frontendInputElm->setValues($frontendInputValues);

        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $scopes = array(
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE   =>
                Mage::helper('brander_unitopblog')->__('Store View'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>
                Mage::helper('brander_unitopblog')->__('Website'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL  =>
                Mage::helper('brander_unitopblog')->__('Global'),
        );

        $fieldset->addField(
            'is_global',
            'select',
            array(
                'name'  => 'is_global',
                'label' => Mage::helper('brander_unitopblog')->__('Scope'),
                'title' => Mage::helper('brander_unitopblog')->__('Scope'),
                'note'  => Mage::helper('brander_unitopblog')->__('Declare attribute value saving scope'),
                'values'=> $scopes
            ),
            'attribute_code'
        );
        $fieldset->addField(
            'position',
            'text',
            array(
                'name'  => 'position',
                'label' => Mage::helper('brander_unitopblog')->__('Position'),
                'title' => Mage::helper('brander_unitopblog')->__('Position'),
                'note'  => Mage::helper('brander_unitopblog')->__('Position in the admin form'),
            ),
            'is_global'
        );
        $fieldset->addField(
            'note',
            'textarea',
            array(
                'name'  => 'note',
                'label' => Mage::helper('brander_unitopblog')->__('Note'),
                'title' => Mage::helper('brander_unitopblog')->__('Note'),
                'note'  => Mage::helper('brander_unitopblog')->__('Text to appear below the input.'),
            ),
            'position'
        );

        $fieldset->removeField('is_unique');
        // frontend properties fieldset
        $fieldset = $form->addFieldset(
            'front_fieldset',
            array(
                'legend'=>Mage::helper('brander_unitopblog')->__('Frontend Properties')
            )
        );
        $fieldset->addField(
            'is_wysiwyg_enabled',
            'select',
            array(
                'name' => 'is_wysiwyg_enabled',
                'label' => Mage::helper('brander_unitopblog')->__('Enable WYSIWYG'),
                'title' => Mage::helper('brander_unitopblog')->__('Enable WYSIWYG'),
                'values' => $yesnoSource,
            )
        );
        Mage::dispatchEvent(
            'brander_unitopblog_adminhtml_postscategory_attribute_edit_prepare_form',
            array(
                'form'      => $form,
                'attribute' => $attributeObject
            )
        );
        return $this;
    }
}
