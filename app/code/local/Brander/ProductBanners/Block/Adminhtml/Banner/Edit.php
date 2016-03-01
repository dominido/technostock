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
class Brander_ProductBanners_Block_Adminhtml_Banner_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
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
        $this->_blockGroup = 'brander_productbanners';
        $this->_controller = 'adminhtml_banner';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('brander_productbanners')->__('Save Banner')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('brander_productbanners')->__('Delete Banner')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('brander_productbanners')->__('Save And Continue Edit'),
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
        if (Mage::registry('current_banner') && Mage::registry('current_banner')->getId()) {
            return Mage::helper('brander_productbanners')->__(
                "Edit Banner '%s'",
                $this->escapeHtml(Mage::registry('current_banner')->getTitle())
            );
        } else {
            return Mage::helper('brander_productbanners')->__('Add Banner');
        }
    }
}
