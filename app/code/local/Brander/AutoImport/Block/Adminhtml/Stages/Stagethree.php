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

class Brander_AutoImport_Block_Adminhtml_Stages_Stagethree extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'autoimport';
        $this->_controller = 'adminhtml_stages_stagethree';
        parent::__construct();
        $stageTo = Mage::helper('autoimport/stages')->getStagesConfig()->getData('stage3');
        $this->_headerText = Mage::helper('autoimport')->__('Stage Three Items "Items Prepared For Remove" (after %s days without updates)', $stageTo);
        $this->_removeButton('add', 'label', Mage::helper('autoimport')->__('Add'));

        $this->setTemplate('brander/autoimport/stages/grid.phtml');
    }
}
