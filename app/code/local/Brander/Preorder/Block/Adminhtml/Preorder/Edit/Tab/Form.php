<?php
/**
 * Brander_Preorder extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Brander
 * @package		Brander_Preorder
 * @copyright  	Copyright (c) 2015
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Pre Order edit form tab
 *
 * @category	Brander
 * @package		Brander_Preorder
 * @author Ultimate Module Creator
 */
class Brander_Preorder_Block_Adminhtml_Preorder_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form{	
	/**
	 * prepare the form
	 * @access protected
	 * @return Preorder_Preorder_Block_Adminhtml_Preorder_Edit_Tab_Form
	 * @author Ultimate Module Creator
	 */
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('preorder_');
		$form->setFieldNameSuffix('preorder');
		$this->setForm($form);
		$fieldset = $form->addFieldset('preorder_form', array('legend'=>Mage::helper('preorder')->__('Pre Order')));

        $fieldset->addType('text_only', 'Brander_Preorder_Block_Adminhtml_Renderer_Text');

		$fieldset->addField('user_name', 'text_only', array(
			'label' => Mage::helper('preorder')->__('User Name'),
			'name'  => 'user_name',
			'required'  => true,

		));
        $fieldset->addField('user_phone', 'text_only', array(
            'label' => Mage::helper('preorder')->__('User Phone'),
            'name'  => 'user_phone',
            'required'  => true,

        ));

		$fieldset->addField('user_comment', 'text_only', array(
			'label' => Mage::helper('preorder')->__('User comment'),
			'name'  => 'user_comment',
            'readonly' => true,
		));

        $fieldset->addType('product', 'Brander_Preorder_Block_Adminhtml_Renderer_Product');

		$fieldset->addField('product_id', 'product', array(
			'label' => Mage::helper('preorder')->__('Product'),
			'name'  => 'product_id',
            'renderer'      => 'adminhtml/preorder_renderer_product',
//			'class' => 'required-entry',

		));

		$fieldset->addField('product_qty', 'text_only', array(
			'label' => Mage::helper('preorder')->__('Qty'),
			'name'  => 'product_qty',
			'class' => 'required-entry',

		));

        $fieldset->addField('manager_comment', 'textarea', array(
            'label' => Mage::helper('preorder')->__('Manager Comment'),
            'name'  => 'manager_comment',
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('preorder')->__('Status'),
            'name'  => 'status',
            'values'=> array(
                array(
                    'value' => 2,
                    'label' => Mage::helper('preorder')->__('Approved'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('preorder')->__('Canceled'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('preorder')->__('New'),
                ),
            ),
        ));

		if (Mage::getSingleton('adminhtml/session')->getPreorderData()){
			$form->setValues(Mage::getSingleton('adminhtml/session')->getPreorderData());
			Mage::getSingleton('adminhtml/session')->setPreorderData(null);
		}
		elseif (Mage::registry('current_preorder')){
			$form->setValues(Mage::registry('current_preorder')->getData());
		}
		return parent::_prepareForm();
	}
}