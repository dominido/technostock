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
class Brander_UnitopBlog_Block_Post_Customer_Comment_List extends Mage_Customer_Block_Account_Dashboard
{
    /**
     * Post comments collection
     *
     * @var Brander_UnitopBlog_Model_Resource_Post_Comment_Post_Collection
     */
    protected $_collection;

    /**
     * Initializes collection
     *
     * @access public

     */
    protected function _construct()
    {
        $this->_collection = Mage::getResourceModel(
            'brander_unitopblog/post_comment_post_collection'
        );
        $this->_collection
            ->setStoreFilter(Mage::app()->getStore()->getId(), true)
            ->addAttributeToFilter('status', 1) //only active

            ->addStatusFilter(Brander_UnitopBlog_Model_Post_Comment::STATUS_APPROVED) //only approved comments
            ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId()) //only my comments
            ->setDateOrder();
    }

    /**
     * Gets collection items count
     *
     * @access public
     * @return int

     */
    public function count()
    {
        return $this->_collection->getSize();
    }

    /**
     * Get html code for toolbar
     *
     * @access public
     * @return string

     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Initializes toolbar
     *
     * @access protected
     * @return Mage_Core_Block_Abstract

     */
    protected function _prepareLayout()
    {
        $toolbar = $this->getLayout()->createBlock('page/html_pager', 'customer_post_comments.toolbar')
            ->setCollection($this->getCollection());

        $this->setChild('toolbar', $toolbar);
        return parent::_prepareLayout();
    }

    /**
     * Get collection
     *
     * @access protected
     * @return Brander_UnitopBlog_Model_Resource_Post_Comment_Post_Collection

     */
    protected function _getCollection()
    {
        return $this->_collection;
    }

    /**
     * Get collection
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Resource_Post_Comment_Post_Collection

     */
    public function getCollection()
    {
        return $this->_getCollection();
    }

    /**
     * Get review link
     *
     * @access public
     * @param mixed $comment
     * @return string

     */
    public function getCommentLink($comment)
    {
        if ($comment instanceof Varien_Object) {
            $comment = $comment->getCtCommentId();
        }
        return Mage::getUrl(
            'brander_unitopblog/post_customer_comment/view/',
            array('id' => $comment)
        );
    }

    /**
     * Get product link
     *
     * @access public
     * @param mixed $comment
     * @return string

     */
    public function getPostLink($comment)
    {
        return $comment->getPostUrl();
    }

    /**
     * Format date in short format
     *
     * @access public
     * @param $date
     * @return string

     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }
}
