<?php
/**
 * {{Brander}}_CategoryBanner extension
 */
/**
 * Category Image edit form tab
 *
 * @category    Brander
 * @package     Brander_CategoryBanner
 * @author      Ultimate Module Creator
 */
class Brander_CategoryBanner_Block_Adminhtml_Categorybanner_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Brander_CategoryBanner_Block_Adminhtml_Categorybanner_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('categorybanner_');
        $form->setFieldNameSuffix('categorybanner');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'categorybanner_form',
            array('legend' => Mage::helper('brander_categorybanner')->__('Category Image'))
        );
        $fieldset->addType(
            'image',
            Mage::getConfig()->getBlockClassName('brander_categorybanner/adminhtml_categorybanner_helper_image')
        );

        $fieldset->addField(
            'category_image_name',
            'text',
            array(
                'label' => Mage::helper('brander_categorybanner')->__('category image name'),
                'name'  => 'category_image_name',
                'note'	=> $this->__('it\'s category image name'),
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'category_image_url',
            'text',
            array(
                'label' => Mage::helper('brander_categorybanner')->__('category image url'),
                'name'  => 'category_image_url',
                'note'	=> $this->__('it\'s category image url'),

           )
        );

        $fieldset->addField(
            'category_image',
            'image',
            array(
                'label' => Mage::helper('brander_categorybanner')->__('category image'),
                'name'  => 'category_image',
                'note'	=> $this->__('i\'ts category image'),

           )
        );

        $fieldset->addField(
            'category_image_seo',
            'textarea',
            array(
                'label' => Mage::helper('brander_categorybanner')->__('category image SEO'),
                'name'  => 'category_image_seo',

           )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('brander_categorybanner')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('brander_categorybanner')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('brander_categorybanner')->__('Disabled'),
                    ),
                ),
            )
        );
        if (Mage::app()->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'hidden',
                array(
                    'name'      => 'stores[]',
                    'value'     => Mage::app()->getStore(true)->getId()
                )
            );
            Mage::registry('current_categorybanner')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $formValues = Mage::registry('current_categorybanner')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getCategorybannerData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getCategorybannerData());
            Mage::getSingleton('adminhtml/session')->setCategorybannerData(null);
        } elseif (Mage::registry('current_categorybanner')) {
            $formValues = array_merge($formValues, Mage::registry('current_categorybanner')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
