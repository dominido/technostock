<?php
/**
 * {{Brander}}_CategoryBanner extension
 */
/**
 * Category Image admin block
 *
 * @category    Brander
 * @package     Brander_CategoryBanner
 * @author      Ultimate Module Creator
 */
class Brander_CategoryBanner_Block_Adminhtml_Categorybanner extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        $this->_controller         = 'adminhtml_categorybanner';
        $this->_blockGroup         = 'brander_categorybanner';
        parent::__construct();
        $this->_headerText         = Mage::helper('brander_categorybanner')->__('Category Image');
        $this->_updateButton('add', 'label', Mage::helper('brander_categorybanner')->__('Add Category Image'));

    }
}
