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
class Brander_UnitopBlog_Model_Post_Category extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @access protected
     * @return void

     */
    protected function _construct()
    {
        $this->_init('brander_unitopblog/post_category');
    }

    /**
     * Save data for post-category relation
     *
     * @access public
     * @param  Brander_UnitopBlog_Model_Post $post
     * @return Brander_UnitopBlog_Model_Post_Category

     */
    public function savePostRelation($post)
    {
        $data = $post->getCategoriesData();
        if (!is_null($data)) {
            $this->_getResource()->savePostRelation($post, $data);
        }
        return $this;
    }

    /**
     * get categories for post
     *
     * @access public
     * @param Brander_UnitopBlog_Model_Post $post
     * @return Brander_UnitopBlog_Model_Resource_Post_Category_Collection

     */
    public function getCategoryCollection($post)
    {
        $collection = Mage::getResourceModel('brander_unitopblog/post_category_collection')
            ->addPostFilter($post);
        return $collection;
    }
}
