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
class Brander_UnitopBlog_Block_Adminhtml_Postscategory_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the attributes for the form
     *
     * @access protected
     * @return void
     * @see Mage_Adminhtml_Block_Widget_Form::_prepareForm()

     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setDataObject(Mage::registry('current_postscategory'));
        $fieldset = $form->addFieldset(
            'info',
            array(
                'legend' => Mage::helper('brander_unitopblog')->__('Post Category Information'),
                'class' => 'fieldset-wide',
            )
        );
        $attributes = $this->getAttributes();
        foreach ($attributes as $attribute) {
            $attribute->setEntity(Mage::getResourceModel('brander_unitopblog/postscategory'));
        }
        if ($this->getAddHiddenFields()) {
            if (!$this->getPostscategory()->getId()) {
                // path
                if ($this->getRequest()->getParam('parent')) {
                    $fieldset->addField(
                        'path',
                        'hidden',
                        array(
                            'name'  => 'path',
                            'value' => $this->getRequest()->getParam('parent')
                        )
                    );
                } else {
                    $fieldset->addField(
                        'path',
                        'hidden',
                        array(
                            'name'  => 'path',
                            'value' => 1
                        )
                    );
                }
            } else {
                $fieldset->addField(
                    'id',
                    'hidden',
                    array(
                        'name'  => 'id',
                        'value' => $this->getPostscategory()->getId()
                    )
                );
                $fieldset->addField(
                    'path',
                    'hidden',
                    array(
                        'name'  => 'path',
                        'value' => $this->getPostscategory()->getPath()
                    )
                );
            }
        }
        $this->_setFieldset($attributes, $fieldset, array());
        $formValues = Mage::registry('current_postscategory')->getData();
        if (!Mage::registry('current_postscategory')->getId()) {
            foreach ($attributes as $attribute) {
                if (!isset($formValues[$attribute->getAttributeCode()])) {
                    $formValues[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                }
            }
        }
        //do not set default value for path
        unset($formValues['path']);
        $form->addValues($formValues);
        $form->setFieldNameSuffix('postscategory');
        $this->setForm($form);
    }

    /**
     * prepare layout
     *
     * @access protected
     * @return void
     * @see Mage_Adminhtml_Block_Widget_Form::_prepareLayout()

     */
    protected function _prepareLayout()
    {
        Varien_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_element')
        );
        Varien_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')
        );
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('brander_unitopblog/adminhtml_unitopblog_renderer_fieldset_element')
        );
    }

    /**
     * get the additional element types for form
     *
     * @access protected
     * @return array()
     * @see Mage_Adminhtml_Block_Widget_Form::_getAdditionalElementTypes()

     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'file'     => Mage::getConfig()->getBlockClassName(
                'brander_unitopblog/adminhtml_postscategory_helper_file'
            ),
            'image'    => Mage::getConfig()->getBlockClassName(
                'brander_unitopblog/adminhtml_postscategory_helper_image'
            ),
            'textarea' => Mage::getConfig()->getBlockClassName(
                'adminhtml/catalog_helper_form_wysiwyg'
            )
        );
    }

    /**
     * get current entity
     *
     * @access protected
     * @return Brander_UnitopBlog_Model_Postscategory

     */
    public function getPostscategory()
    {
        return Mage::registry('current_postscategory');
    }
}
