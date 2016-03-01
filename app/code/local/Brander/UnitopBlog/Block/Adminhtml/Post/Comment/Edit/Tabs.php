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
class Brander_UnitopBlog_Block_Adminhtml_Post_Comment_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public

     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('post_comment_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('brander_unitopblog')->__('Post Comment'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Adminhtml_Post_Edit_Tabs

     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_post_comment',
            array(
                'label'   => Mage::helper('brander_unitopblog')->__('Post comment'),
                'title'   => Mage::helper('brander_unitopblog')->__('Post comment'),
                'content' => $this->getLayout()->createBlock('brander_unitopblog/adminhtml_post_comment_edit_tab_form')->toHtml(),
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_post_comment',
                array(
                    'label'   => Mage::helper('brander_unitopblog')->__('Store views'),
                    'title'   => Mage::helper('brander_unitopblog')->__('Store views'),
                    'content' => $this->getLayout()->createBlock('brander_unitopblog/adminhtml_post_comment_edit_tab_stores')->toHtml(),
                )
            );
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve comment
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Post_Comment

     */
    public function getComment()
    {
        return Mage::registry('current_comment');
    }
}
