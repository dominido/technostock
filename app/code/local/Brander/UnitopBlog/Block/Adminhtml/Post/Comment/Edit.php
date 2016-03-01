<?php
/**
 * Brander UnitopBlog extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        UnitopBlog
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_UnitopBlog_Block_Adminhtml_Post_Comment_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
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
        $this->_blockGroup = 'brander_unitopblog';
        $this->_controller = 'adminhtml_post_comment';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('brander_unitopblog')->__('Save Post comment')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('brander_unitopblog')->__('Delete Post comment')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'        => Mage::helper('brander_unitopblog')->__('Save And Continue Edit'),
                'onclick'    => 'saveAndContinueEdit()',
                'class'        => 'save',
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
        if (Mage::registry('comment_data') && Mage::registry('comment_data')->getId()) {
            return Mage::helper('brander_unitopblog')->__(
                "Edit Post comment '%s'",
                $this->escapeHtml(Mage::registry('comment_data')->getTitle())
            );
        }
        return '';
    }
}
