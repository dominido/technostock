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

class Brander_AutoImport_Block_Adminhtml_Importgrid_Edit extends Mage_Adminhtml_Block_Widget_Form_Container{

    public function __construct(){
        parent::__construct();
        $this->_blockGroup = 'autoimport';
        $this->_controller = 'adminhtml_importgrid';
        $this->_updateButton('save', 'label', Mage::helper('autoimport')->__('Save process'));
        $this->_updateButton('delete', 'label', Mage::helper('autoimport')->__('Delete process'));

        if (Mage::helper('autoimport')->checkForEditMode()) {
            $this->_removeButton('save');
            $this->_removeButton('reset');
        }

    }

    public function getHeaderText(){
        if( Mage::registry('importgrid_data') && Mage::registry('importgrid_data')->getId() ) {
            return Mage::helper('autoimport')->__("Import Task Details");
        }
        else {
            return Mage::helper('autoimport')->__('Create New Import Task');
        }
    }
}