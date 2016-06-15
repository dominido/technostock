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

class Brander_AutoImport_Block_Adminhtml_Importgrid_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{

	public function __construct(){
		parent::__construct();
		$this->setId('importgrid_tabs');
		$this->setDestElementId('edit_form');
	}

	protected function _beforeToHtml(){
		$this->addTab('form_importgrid', array(
			'label'		=> Mage::helper('autoimport')->__('Task Details'),
			'title'		=> Mage::helper('autoimport')->__('Import Task Details'),
			'content' 	=> $this->getLayout()->createBlock('autoimport/adminhtml_importgrid_edit_tab_form')->toHtml(),
		));
        $this->addTab('files_importgrid', array(
            'label'		=> Mage::helper('autoimport')->__('Import File'),
            'title'		=> Mage::helper('autoimport')->__('Import File Upload and Information'),
            'content' 	=> $this->getLayout()->createBlock('autoimport/adminhtml_importgrid_edit_tab_files')->toHtml(),
        ));

        $formValues = Mage::registry('importgrid_data');

        if ($formValues->getStartAt() || $formValues->getFinishAt()) {
            $this->addTab('report_importgrid', array(
                'label'   => Mage::helper('autoimport')->__('Report'),
                'title'   => Mage::helper('autoimport')->__('Log and Report'),
                'content' => $this->getLayout()->createBlock('autoimport/adminhtml_importgrid_edit_tab_report')->toHtml(),
            ));
        }
		return parent::_beforeToHtml();
	}
}