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
class Brander_OurContacts_Block_Adminhtml_Contact_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'brander_ourcontacts';
        $this->_controller = 'adminhtml_contact';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('brander_ourcontacts')->__('Save Contact')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('brander_ourcontacts')->__('Delete Contact')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('brander_ourcontacts')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ),
            -100
        );
        $this->_formScripts[] = "
            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * get the edit form header
     *
     * @access public
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_contact') && Mage::registry('current_contact')->getId()) {
            return Mage::helper('brander_ourcontacts')->__(
                "Edit Contact '%s'",
                $this->escapeHtml(Mage::registry('current_contact')->getTitle())
            );
        } else {
            return Mage::helper('brander_ourcontacts')->__('Add Contact');
        }
    }
}
