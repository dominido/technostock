<?php
/**
 * Brander HotCategories extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        HotCategories
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_HotCategories_Block_Adminhtml_Hotcategory_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Widget_Form
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
        $form->setDataObject(Mage::registry('current_hotcategory'));
        $fieldset = $form->addFieldset(
            'info',
            array(
                'legend' => Mage::helper('brander_hotcategories')->__('Hot Category Information'),
                'class' => 'fieldset-wide',
            )
        );
        $attributes = $this->getAttributes();
        foreach ($attributes as $attribute) {
            $attribute->setEntity(Mage::getResourceModel('brander_hotcategories/hotcategory'));
        }
        $this->_setFieldset($attributes, $fieldset, array());
        $formValues = Mage::registry('current_hotcategory')->getData();
        if (!Mage::registry('current_hotcategory')->getId()) {
            foreach ($attributes as $attribute) {
                if (!isset($formValues[$attribute->getAttributeCode()])) {
                    $formValues[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                }
            }
        }
        $form->addValues($formValues);
        $form->setFieldNameSuffix('hotcategory');
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
            $this->getLayout()->createBlock('brander_hotcategories/adminhtml_hotcategories_renderer_fieldset_element')
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
                'brander_hotcategories/adminhtml_hotcategory_helper_file'
            ),
            'image'    => Mage::getConfig()->getBlockClassName(
                'brander_hotcategories/adminhtml_hotcategory_helper_image'
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
     * @return Brander_HotCategories_Model_Hotcategory
     */
    public function getHotcategory()
    {
        return Mage::registry('current_hotcategory');
    }

    /**
     * get after element html
     *
     * @access protected
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getAdditionalElementHtml($element)
    {
        if ($element->getName() == 'mode') {
            $html = '<script type="text/javascript">
                window.onload = function(){
                    var elementUrl  = $(\'category_url\');
                    var elementId   = $(\'category_id\');
                    if ($(\'mode\').value == \'\') {
                        elementId.up().up().hide();
                        elementUrl.up().up().hide();
                    } else if ($(\'mode\').value == \'1\') {
                        elementId.up().up().show();elementId.addClassName("required-entry");
                        elementUrl.up().up().hide();elementUrl.removeClassName("required-entry");
                    } else if ($(\'mode\').value == \'2\') {
                        elementId.up().up().hide();elementId.removeClassName("required-entry");
                        elementUrl.up().up().show();elementUrl.addClassName("required-entry");
                    }
                };
                function selectCategoryMode() {
                    var elementUrl  = $(\'category_url\');
                    var elementId   = $(\'category_id\');
                    if ($(\'mode\').value == \'\') {
                        elementId.up().up().hide();elementId.removeClassName("required-entry");
                        elementUrl.up().up().hide();elementUrl.removeClassName("required-entry");
                    } else if ($(\'mode\').value == \'1\') {
                        elementId.up().up().show();elementId.addClassName("required-entry");
                        elementUrl.up().up().hide();elementUrl.removeClassName("required-entry");elementUrl.value = \'\';
                    } else if ($(\'mode\').value == \'2\') {
                        elementId.up().up().hide();elementId.removeClassName("required-entry");elementId.value = \'\';
                        elementUrl.up().up().show();elementUrl.addClassName("required-entry");
                    }
                }
                $(\'mode\').observe(\'change\', selectCategoryMode);
            </script>
            ';
            return $html;
        }
        elseif ($element->getName() == 'grid_parts') {
            $html = '


            <span class="grid-part-selector" style="width: 250px; padding-left: 25px; padding-top: 10px; display: inline-block;">
                <input type="text" class="js-callback" />
                <span id="js-display-callback" class="display-box" style="display: block; margin-left: 300px; margin-top: -10px" ></span>
            </span>


            <script type="text/javascript">

                var grid_parts = $(\'grid_parts\');
                var currentValue = grid_parts.value;

                grid_parts.hide(); $(\'js-display-callback\').innerHTML = currentValue;

                jQuery(document).ready(function() {
                    var clbk = document.querySelector(\'.js-callback\');
                    var initClbk = new Powerange(clbk, { min: 1, max: 12, start: currentValue });

                    clbk.onchange = function() {
                    //function displayValue() {
                        if (clbk.value >= 1) {
                            $(\'grid_parts\').value = clbk.value;
                            $(\'js-display-callback\').innerHTML = clbk.value;
                        }
                    }
                });
            </script>
            ';
            return $html;
        }
        return '';
    }
}
