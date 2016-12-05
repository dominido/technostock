<?php
/**
 * {{Brander}}_CategoryBanner extension
 */
/**
 * store selection tab
 *
 * @category    Brander
 * @package     Brander_CategoryBanner
 * @author      Ultimate Module Creator
 */
class Brander_CategoryBanner_Block_Adminhtml_Categorybanner_Edit_Tab_Stores extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Brander_CategoryBanner_Block_Adminhtml_Categorybanner_Edit_Tab_Stores
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setFieldNameSuffix('categorybanner');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'categorybanner_stores_form',
            array('legend' => Mage::helper('brander_categorybanner')->__('Store views'))
        );
        $field = $fieldset->addField(
            'store_id',
            'multiselect',
            array(
                'name'     => 'stores[]',
                'label'    => Mage::helper('brander_categorybanner')->__('Store Views'),
                'title'    => Mage::helper('brander_categorybanner')->__('Store Views'),
                'required' => true,
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            )
        );
        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
        $field->setRenderer($renderer);
        $form->addValues(Mage::registry('current_categorybanner')->getData());
        return parent::_prepareForm();
    }
}
