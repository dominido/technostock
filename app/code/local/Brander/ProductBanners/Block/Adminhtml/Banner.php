<?php
/**
 * Brander ProductBanners extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        ProductBanners
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_ProductBanners_Block_Adminhtml_Banner extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void

     */
    public function __construct()
    {
        $this->_controller         = 'adminhtml_banner';
        $this->_blockGroup         = 'brander_productbanners';
        parent::__construct();
        $this->_headerText         = Mage::helper('brander_productbanners')->__('Banner');
        $this->_updateButton('add', 'label', Mage::helper('brander_productbanners')->__('Add Banner'));

        $this->setTemplate('brander_productbanners/grid.phtml');
    }
}
