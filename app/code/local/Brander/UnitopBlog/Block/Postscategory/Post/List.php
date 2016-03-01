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
class Brander_UnitopBlog_Block_Postscategory_Post_List extends Brander_UnitopBlog_Block_Post_List
{
    /**
     * initialize
     *
     * @access public

     */
    public function __construct()
    {
        parent::__construct();
        $postscategory = $this->getPostscategory();
        if ($postscategory) {
            $this->getPosts()->addFieldToFilter('postscategory_id', $postscategory->getId());
        }
    }

    /**
     * prepare the layout - actually do nothing
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Postscategory_Post_List

     */
    protected function _prepareLayout()
    {
        return $this;
    }

    /**
     * get the current post category
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Postscategory

     */
    public function getPostscategory()
    {
        return Mage::registry('current_postscategory');
    }
}
