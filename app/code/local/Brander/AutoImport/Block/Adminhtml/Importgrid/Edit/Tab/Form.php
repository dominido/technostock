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

class Brander_AutoImport_Block_Adminhtml_Importgrid_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('importgrid_');
		$form->setFieldNameSuffix('importgrid');
		$this->setForm($form);

		$fieldset = $form->addFieldset('importgrid_form', array('legend'=>Mage::helper('autoimport')->__('Import confuguration')));
		//$fieldset->addType('file', Mage::getConfig()->getBlockClassName('autoimport/adminhtml_importgrid_helper_file'));

        $id = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('autoimport/importgrid')->load($id);
        $formValues = Mage::registry('importgrid_data');

        //$dateFormatIso=Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);


        if (Mage::helper('autoimport')->checkForEditMode()) {
            $fieldset->addField('planned_at', 'date', array(
                'name'         => 'planned_at',
                'time'         => true,
                'required'     => false,
                'disabled'     => true,
                'readonly'     => true,
                'label'        => 'START date and time',
                //'image'        => $this->getSkinUrl('images/grid-cal.gif'),
                'format'       => 'yyyy-MM-dd HH:mm',
                'input_format' => 'yyyy-MM-dd HH:mm',
            ));
        }
        else {
            $fieldset->addField('planned_at', 'date', array(
                'name'         => 'planned_at',
                'time'         => true,
                'required'     => true,
                'label'        => 'Select START date and time',
                'image'        => $this->getSkinUrl('images/grid-cal.gif'),
                'format'       => 'yyyy-MM-dd HH:mm',
                'input_format' => 'yyyy-MM-dd HH:mm',
                'class'        => 'validate-date-time'
            ));
        }

/*        $fieldset->addField('configuration', 'select', array(
                'label'     => Mage::helper('autoimport')->__('Import configuration'),
                'class'     => 'required-entry',
                'value'     => $collection->getConfiguration(),
                'values'    => Mage::helper('autoimport')->importTypes(),
                'name'      => 'configuration',
            ));*/

/*        $fieldset->addField('reindex_status', 'checkbox', array(
                'label'     => Mage::helper('autoimport')->__('Enable reindex?'),
                'name'      => 'reindex_status',
                'checked'   => $collection->getReindexStatus(),
                'onclick'   => 'this.value = this.checked ? 1 : 0;',
            ));*/

        $fieldset->addField('note_proc', 'note', array(
            'text'     => Mage::helper('autoimport')->__('Import process info :'),
        ));

		$fieldset->addField('import_status', 'text', array(
			'label'=> Mage::helper('autoimport')->__('Import status'),
			'name' => 'import_status',
            'disabled' => true,
            'readonly' => true,
		));

        $fieldset->addField('start_at', 'text', array(
			'label'	=> Mage::helper('autoimport')->__('Started at'),
			'name' 	=> 'created_at',
            'disabled' => true,
            'readonly' => true,
		));

        $fieldset->addField('finish_at', 'text', array(
			'label'	=> Mage::helper('autoimport')->__('Finished at'),
			'name' 	=> 'finish_at',
            'disabled' => true,
            'readonly' => true,
		));

        if ($formValues->getLogItemsStat()) {
            $importFiles = json_decode($formValues->getLogItemsStat());
            if (!empty($importFiles)) {

                $fieldset->addField(
                    'note_items', 'note', array(
                    'text' => Mage::helper('autoimport')->__('Import items info :'),
                ));

                $fieldset->addField(
                    'new_items', 'text', array(
                    'label'    => Mage::helper('autoimport')->__('New Items'),
                    'name'     => 'new_items',
                    'disabled' => true,
                    'readonly' => true,
                ));

                $fieldset->addField(
                    'update_items', 'text', array(
                    'label'    => Mage::helper('autoimport')->__('Updated Items'),
                    'name'     => 'update_items',
                    'disabled' => true,
                    'readonly' => true,
                ));

                $fieldset->addField(
                    'oofs_items', 'text', array(
                    'label'    => Mage::helper('autoimport')->__('Out of Stock Items'),
                    'name'     => 'oofs_items',
                    'disabled' => true,
                    'readonly' => true,
                ));
                $fieldset->addField(
                    'remove_items', 'text', array(
                    'label'    => Mage::helper('autoimport')->__('Removed Items'),
                    'name'     => 'remove_items',
                    'disabled' => true,
                    'readonly' => true,
                ));
            }
        }

        $importStats = $formValues->getLogItemsStat();
        if (!empty($importStats)) {
            $itemStats = json_decode($importStats);
            if (!empty($itemStats->new) || $itemStats->new == 0) {
                $formValues->setNewItems($itemStats->new);
            }
            if (!empty($itemStats->update) || $itemStats->update == 0) {
                $formValues->setUpdateItems($itemStats->update);
            }
            if (!empty($itemStats->out_of_stock) || $itemStats->out_of_stock == 0) {
                $formValues->setOofsItems($itemStats->out_of_stock);
            }
            if (!empty($itemStats->remove) || $itemStats->remove == 0) {
                $formValues->setRemoveItems($itemStats->remove);
            }
        }

        $datetime = array ('start_at', 'finish_at');
        foreach ($datetime as $_datetime) {
            if ($formValues->getData($_datetime)) {
                $formValues->setData($_datetime, Mage::helper('autoimport')->convertTimeGmtToNow($formValues->getData($_datetime)));
            }
        }
        if ($formValues->getPlannedAt()) {
            $formValues->setPlannedAt(Mage::getModel('core/date')->timestamp($collection->getPlannedAt()));
        }

        $form->setValues($formValues);

		return parent::_prepareForm();
	}

}