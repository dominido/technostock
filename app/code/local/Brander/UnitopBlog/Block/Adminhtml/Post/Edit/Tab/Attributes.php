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
class Brander_UnitopBlog_Block_Adminhtml_Post_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Widget_Form
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
        $form->setDataObject(Mage::registry('current_post'));
        $fieldset = $form->addFieldset(
            'info',
            array(
                'legend' => Mage::helper('brander_unitopblog')->__('Post Information'),
                'class' => 'fieldset-wide',
            )
        );
        $attributes = $this->getAttributes();
        foreach ($attributes as $attribute) {
            $attribute->setEntity(Mage::getResourceModel('brander_unitopblog/post'));
        }
        $this->_setFieldset($attributes, $fieldset, array());
        $formValues = Mage::registry('current_post')->getData();
        if (!Mage::registry('current_post')->getId()) {
            foreach ($attributes as $attribute) {
                if (!isset($formValues[$attribute->getAttributeCode()])) {
                    $formValues[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                }
            }
        }
        $form->addValues($formValues);
        $form->setFieldNameSuffix('post');
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
                'brander_unitopblog/adminhtml_post_helper_file'
            ),
            'image'    => Mage::getConfig()->getBlockClassName(
                'brander_unitopblog/adminhtml_post_helper_image'
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
     * @return Brander_UnitopBlog_Model_Post

     */
    public function getPost()
    {
        return Mage::registry('current_post');
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
        if ($element->getName() == 'postscategory_id') {
            $html = '<a href="{#url}" id="postscategory_id_link" target="_blank"></a>';
            $html .= '<script type="text/javascript">
            function changePostscategoryIdLink() {
                if ($(\'postscategory_id\').value == \'\') {
                    $(\'postscategory_id_link\').hide();
                } else {
                    $(\'postscategory_id_link\').show();
                    var url = \''.$this->getUrl('adminhtml/unitopblog_postscategory/edit', array('id'=>'{#id}', 'clear'=>1)).'\';
                    var text = \''.Mage::helper('core')->escapeHtml($this->__('View {#name}')).'\';
                    var realUrl = url.replace(\'{#id}\', $(\'postscategory_id\').value);
                    $(\'postscategory_id_link\').href = realUrl;
                    $(\'postscategory_id_link\').innerHTML = text.replace(\'{#name}\', $(\'postscategory_id\').options[$(\'postscategory_id\').selectedIndex].innerHTML);
                }
            }
            $(\'postscategory_id\').observe(\'change\', changePostscategoryIdLink);
            changePostscategoryIdLink();
            </script>';
            return $html;
        }

        if ($element->getName() == 'author') {

            $html = ('

            <script type="text/javascript">
                // check type
                var data_val = new Array();
                window.onload = function(){
                    $(\'author\').observe("change",function(event){
                        var element = Event.element(event);
                        var author = $(\'manual_author_name\');
                        if (element.value) {
                            var index = element.selectedIndex
                            author.value = element.options[index].innerHTML;
                        }
                    });

                    }
            </script>

            ');

            return $html;
        }
        return '';
    }
}
