<?php
/**
 * Brander_ShopReview extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Brander
 * @package		Brander_ShopReview
 * @copyright  	Copyright (c) 2016
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Shop Review edit form tab
 *
 * @category    Brander
 * @package     Brander_ShopReview
 * @author      Ultimate Module Creator
 */
class Brander_ShopReview_Block_Adminhtml_Shopreview_Edit_Tab_Form
    extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * prepare the form
     * @access protected
     * @return Brander_ShopReview_Block_Adminhtml_Shopreview_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm(){
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('shopreview_');
        $form->setFieldNameSuffix('shopreview');
        $this->setForm($form);
        $fieldset = $form->addFieldset('shopreview_form', array('legend'=>Mage::helper('brander_shopreview')->__('Shop Review')));

        $fieldset->addField('user_name', 'text', array(
            'label' => Mage::helper('brander_shopreview')->__('User Name'),
            'name'  => 'user_name',
            'required'  => true,
            'class' => 'required-entry',

        ));

        $fieldset->addField('subject_review', 'text', array(
            'label' => Mage::helper('brander_shopreview')->__('Subject Review'),
            'name'  => 'subject_review',
            'required'  => true,
            'class' => 'required-entry',

        ));

        $fieldset->addField('user_phone', 'text', array(
            'label' => Mage::helper('brander_shopreview')->__('User Phone'),
            'name'  => 'user_phone',
            'required'  => true,
            'class' => 'required-entry',

        ));

        $fieldset->addField('user_email', 'text', array(
            'label' => Mage::helper('brander_shopreview')->__('User Email'),
            'name'  => 'user_email',
            'required'  => true,
            'class' => 'required-entry',

        ));

        $fieldset->addField('user_review', 'textarea', array(
            'label' => Mage::helper('brander_shopreview')->__('User Review'),
            'name'  => 'user_review',
            'required'  => true,
            'class' => 'required-entry',

        ));

        $fieldset->addField('review_status', 'select', array(
            'label' => Mage::helper('brander_shopreview')->__('Review Status'),
            'name'  => 'review_status',
            'required'  => true,
            'class' => 'required-entry',

            'values'=> Mage::getModel('brander_shopreview/shopreview_attribute_source_reviewstatus')->getAllOptions(true),
        ));
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('brander_shopreview')->__('Status'),
            'name'  => 'status',
            'values'=> array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('brander_shopreview')->__('Enabled'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('brander_shopreview')->__('Disabled'),
                ),
            ),
        ));
        $formValues = Mage::registry('current_shopreview')->getDefaultValues();
        if (!is_array($formValues)){
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getShopreviewData()){
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getShopreviewData());
            Mage::getSingleton('adminhtml/session')->setShopreviewData(null);
        }
        elseif (Mage::registry('current_shopreview')){
            $formValues = array_merge($formValues, Mage::registry('current_shopreview')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
