<?php

/**
 * Brander AutoImport extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        AutoImport
 * @copyright      Copyright (c) 2014-2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */

class Brander_AutoImport_Block_Adminhtml_Importgrid_Edit_Tab_Files extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm(){
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('importgrid_');
        $form->setFieldNameSuffix('importgrid');
        $this->setForm($form);

        $fieldset = $form->addFieldset('importgrid_files', array('legend'=>Mage::helper('autoimport')->__('Add File')));
        $fieldset->addType('file', Mage::getConfig()->getBlockClassName('autoimport/adminhtml_importgrid_helper_file'));
        $formValues = Mage::registry('importgrid_data');




        if (Mage::helper('autoimport')->checkForEditMode()){

            $type =	$fieldset->addField('file_type', 'select', array(
                'label'     => Mage::helper('autoimport')->__('Menu Type'),
                'name'      => 'file_type',
                'class'     => 'required-entry',
                'required'     => false,
                'disabled'     => true,
                'readonly'     => true,
                'values'    => Mage::getModel('autoimport/source_filetype')->toOptionArray(),
                'onchange'	=>'CheckType(this)',
            ));

            /*$fieldset->addField('import_file_loadtime', 'date', array(
                'name'         => 'import_file_loadtime',
                'time'         => true,
                'required'     => false,
                'disabled'     => true,
                'readonly'     => true,
                'label'        => 'Import File Loaded at',
                //'image'        => $this->getSkinUrl('images/grid-cal.gif'),
                'format'       => 'yyyy-MM-dd HH:mm',
                'input_format' => 'yyyy-MM-dd HH:mm',
                'value'        => Mage::getModel('core/date')->timestamp($formValues->getImportFileLoadtime()),
            ));

            $fieldset->addField(
                'note_file', 'note', array(
                'text' => Mage::helper('autoimport')->__('Import file info :'),
            ));

            $fieldset->addField(
                'import_filename', 'text', array(
                'label'    => Mage::helper('autoimport')->__('Import file name'),
                'name'     => 'import_filename',
                'disabled' => true,
                'readonly' => true,
                //'value'    => $collection->getLogFilename(),
            ));

            $fieldset->addField(
                'import_file_size', 'text', array(
                'label'    => Mage::helper('autoimport')->__('Import file size'),
                'name'     => 'import_file_size',
                'disabled' => true,
                'readonly' => true,
                //'value'    => $collection->getLogFilename(),
            ));*/
        }
        else {

            $type =	$fieldset->addField('file_type', 'select', array(
                'label'     => Mage::helper('autoimport')->__('Menu Type'),
                'name'      => 'file_type',
                'class'     => 'required-entry',
                'required'  => true,
                'values'    => Mage::getModel('autoimport/source_filetype')->toOptionArray(),
                'onchange'	=>'CheckType(this)',
            ));

/*            $fieldset->addField('import_filename', 'file', array(
                'label' => Mage::helper('autoimport')->__('Select Local CSV File'),
                'name'  => 'import_filename',
                'note'  => Mage::helper('autoimport')->__('Only csv files available'),
                'required'     => true,
                'class'     => 'import_filename',
            ));*/
        }


        //$form->setValues($formValues);


        $type->setAfterElementHtml('
					<script type="text/javascript">
						// check type
						var data_val = new Array();
						window.onload = function(){
							$$(".flex").each(function(element){element.hide();});
							$(\''.$form->getHtmlIdPrefix().$type->getId().'\').observe("focus",function(event){
								var element = Event.element(event);
							});
							CheckType($(\''.$form->getHtmlIdPrefix().$type->getId().'\'));
						}

						function CheckType(element){
							type = element.value;

							if(type=="'.Brander_AutoImport_Model_Source_Filetype::FILE_TYPE_MANUAL_LOAD.'"){
							$$(".flex").each(function(element){
                                    element.show();
							    });
							} else {
							    $$(".flex").each(function(element){
                                    element.hide();
							    });
							}
						}
					</script>
		');



        if (Mage::getSingleton('adminhtml/session')->getImportgridData()){
            $form->setValues(Mage::getSingleton('adminhtml/session')->getImportgridData());
            Mage::getSingleton('adminhtml/session')->setImportgridData(null);
        }
        elseif (Mage::registry('importgrid_data')){

            $formValues->setImportFileLoadtime(Mage::getModel('core/date')->timestamp($formValues->getImportFileLoadtime()));
            $form->setValues(Mage::registry('importgrid_data')->getData());
        }



        return parent::_prepareForm();
    }

}