<?php
/**
 * Brander OurContacts extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        OurContacts
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_OurContacts_Block_Adminhtml_Contact_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * constructor
     *
     * @access public
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_contact_attribute';
        $this->_blockGroup = 'brander_ourcontacts';
        $this->_headerText = Mage::helper('brander_ourcontacts')->__('Manage Contact Attributes');
        parent::__construct();
        $this->_updateButton(
            'add',
            'label',
            Mage::helper('brander_ourcontacts')->__('Add New Contact Attribute')
        );
    }
}
