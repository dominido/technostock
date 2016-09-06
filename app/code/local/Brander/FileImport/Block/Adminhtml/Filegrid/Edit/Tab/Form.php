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
 * File Grid edit form tab
 *
 * @category	Brander
 * @package		Brander_FileImport
 */
class Brander_FileImport_Block_Adminhtml_Filegrid_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form{	
	/**
	 * prepare the form
	 * @access protected
	 * @return Brander_FileImport_Block_Adminhtml_Filegrid_Edit_Tab_Form
	 */
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('filegrid_');
		$form->setFieldNameSuffix('filegrid');
		$this->setForm($form);
		$fieldset = $form->addFieldset('filegrid_form', array('legend'=>Mage::helper('fileimport')->__('Add File')));
		$fieldset->addType('file', Mage::getConfig()->getBlockClassName('fileimport/adminhtml_filegrid_helper_file'));

		if (Mage::helper('fileimport/data')->checkForEditMode()){
			$fieldset->addField('file_name', 'text', array(
				'label' => Mage::helper('fileimport')->__('Load File'),
				'enable' => false,
			));
		}
		else {
			$fieldset->addField( 'file_csv_name', 'file', array(
				'label' => Mage::helper( 'fileimport' )->__( 'Select the Price List file' ),
				'name'  => 'file_csv_name',
				'note'  => Mage::helper( 'fileimport' )->__( 'upload and save price-list file' )

			) );

		}


		if (Mage::app()->isSingleStoreMode()){
			$fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            Mage::registry('current_filegrid')->setStoreId(Mage::app()->getStore(true)->getId());
		}
		if (Mage::getSingleton('adminhtml/session')->getFilegridData()){
			$form->setValues(Mage::getSingleton('adminhtml/session')->getFilegridData());
			Mage::getSingleton('adminhtml/session')->setFilegridData(null);
		}
		elseif (Mage::registry('current_filegrid')){
			$form->setValues(Mage::registry('current_filegrid')->getData());
		}
		return parent::_prepareForm();
	}
}