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
class Brander_Benefits_Block_Adminhtml_Benefit_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * constructor
     *
     * @access public
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_benefit_attribute';
        $this->_blockGroup = 'brander_benefits';
        $this->_headerText = Mage::helper('brander_benefits')->__('Manage Benefit Attributes');
        parent::__construct();
        $this->_updateButton(
            'add',
            'label',
            Mage::helper('brander_benefits')->__('Add New Benefit Attribute')
        );
    }
}
