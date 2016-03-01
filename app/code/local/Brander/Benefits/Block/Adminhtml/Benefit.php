<?php
/**
 * Brander Benefits extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        Benefits
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_Benefits_Block_Adminhtml_Benefit extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->_controller         = 'adminhtml_benefit';
        $this->_blockGroup         = 'brander_benefits';
        parent::__construct();
        $this->_headerText         = Mage::helper('brander_benefits')->__('Benefit');
        $this->_updateButton('add', 'label', Mage::helper('brander_benefits')->__('Add Benefit'));

        $this->setTemplate('brander_benefits/grid.phtml');
    }
}
