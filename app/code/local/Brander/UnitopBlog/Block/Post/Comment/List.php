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
class Brander_UnitopBlog_Block_Post_Comment_List extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public

     */
    public function __construct()
    {
        parent::__construct();
        $post = $this->getPost();
        $comments = Mage::getResourceModel('brander_unitopblog/post_comment_collection')
            ->addFieldToFilter('post_id', $post->getId())
            ->addStoreFilter(Mage::app()->getStore())
             ->addFieldToFilter('status', 1);
        $comments->setOrder('created_at', 'desc');
        $this->setComments($comments);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Post_Comment_List

     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'brander_unitopblog.post.html.pager'
        )
        ->setCollection($this->getComments());
        $this->setChild('pager', $pager);
        $this->getComments()->load();
        return $this;
    }

    /**
     * get the pager html
     *
     * @access public
     * @return string

     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    /**
     * get the current post
     *
     * @access protected
     * @return Brander_UnitopBlog_Model_Post

     */
    public function getPost()
    {
        return Mage::registry('current_post');
    }
}
