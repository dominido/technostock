<?php 
/**
 * Brander_FileImport extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Brander
 * @package		Brander_FileImport
 * @copyright  	Copyright (c) 2014
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * store selection tab
 *
 * @category	Brander
 * @package		Brander_FileImport
 * @author Ultimate Module Creator
 */
class Brander_FileImport_Block_Adminhtml_Filegrid_Edit_Tab_Stores extends Mage_Adminhtml_Block_Widget_Form{
	/**
	 * prepare the form
	 * @access protected
	 * @return Brander_FileImport_Block_Adminhtml_Filegrid_Edit_Tab_Stores
	 * @author Ultimate Module Creator
	 */
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$form->setFieldNameSuffix('filegrid');
		$this->setForm($form);
		$fieldset = $form->addFieldset('filegrid_stores_form', array('legend'=>Mage::helper('fileimport')->__('Store views')));
		$field = $fieldset->addField('store_id', 'multiselect', array(
			'name'  => 'stores[]',
			'label' => Mage::helper('fileimport')->__('Store Views'),
			'title' => Mage::helper('fileimport')->__('Store Views'),
			'required'  => false,
			'values'=> array(0),
			
		));
		$renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
		$field->setRenderer($renderer);
  		$form->addValues(Mage::registry('current_filegrid')->getData());
		return parent::_prepareForm();
	}
}