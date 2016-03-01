<?php
/**
 * Brander HotCategories extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        HotCategories
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_HotCategories_Block_Adminhtml_Hotcategory_Attribute_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constructor
     *
     * @access public
     */
    public function __construct()
    {
        $this->_objectId = 'attribute_id';
        $this->_controller = 'adminhtml_hotcategory_attribute';
        $this->_blockGroup = 'brander_hotcategories';

        parent::__construct();
        $this->_addButton(
            'save_and_edit_button',
            array(
                'label'     => Mage::helper('brander_hotcategories')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     => 'save'
            ),
            100
        );
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('brander_hotcategories')->__('Save Hot Category Attribute')
        );
        $this->_updateButton('save', 'onclick', 'saveAttribute()');

        if (!Mage::registry('entity_attribute')->getIsUserDefined()) {
            $this->_removeButton('delete');
        } else {
            $this->_updateButton(
                'delete',
                'label',
                Mage::helper('brander_hotcategories')->__('Delete Hot Category Attribute')
            );
        }
    }

    /**
     * get the header text for the form
     *
     * @access public
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('entity_attribute')->getId()) {
            $frontendLabel = Mage::registry('entity_attribute')->getFrontendLabel();
            if (is_array($frontendLabel)) {
                $frontendLabel = $frontendLabel[0];
            }
            return Mage::helper('brander_hotcategories')->__('Edit Hot Category Attribute "%s"', $this->escapeHtml($frontendLabel));
        } else {
            return Mage::helper('brander_hotcategories')->__('New Hot Category Attribute');
        }
    }

    /**
     * get validation url for form
     *
     * @access public
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    /**
     * get save url for form
     *
     * @access public
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/'.$this->_controller.'/save', array('_current'=>true, 'back'=>null));
    }
}
