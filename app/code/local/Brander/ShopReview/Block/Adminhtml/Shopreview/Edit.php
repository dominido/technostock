<?php
/**
 * Brander_ShopReview extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Brander
 * @package		Brander_ShopReview
 * @copyright  	Copyright (c) 2016
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Shop Review admin edit form
 *
 * @category    Brander
 * @package     Brander_ShopReview
 * @author      Ultimate Module Creator
 */
class Brander_ShopReview_Block_Adminhtml_Shopreview_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container {
    /**
     * constructor
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function __construct(){
        parent::__construct();
        $this->_blockGroup = 'brander_shopreview';
        $this->_controller = 'adminhtml_shopreview';
        $this->_updateButton('save', 'label', Mage::helper('brander_shopreview')->__('Save Shop Review'));
        $this->_updateButton('delete', 'label', Mage::helper('brander_shopreview')->__('Delete Shop Review'));
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('brander_shopreview')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    /**
     * get the edit form header
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getHeaderText(){
        if( Mage::registry('current_shopreview') && Mage::registry('current_shopreview')->getId() ) {
            return Mage::helper('brander_shopreview')->__("Edit Shop Review '%s'", $this->escapeHtml(Mage::registry('current_shopreview')->getUserName()));
        }
        else {
            return Mage::helper('brander_shopreview')->__('Add Shop Review');
        }
    }
}
