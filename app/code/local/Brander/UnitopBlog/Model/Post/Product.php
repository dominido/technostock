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
class Brander_UnitopBlog_Model_Post_Product extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @access protected
     * @return void

     */
    protected function _construct()
    {
        $this->_init('brander_unitopblog/post_product');
    }

    /**
     * Save data for post-product relation
     * @access public
     * @param  Brander_UnitopBlog_Model_Post $post
     * @return Brander_UnitopBlog_Model_Post_Product

     */
    public function savePostRelation($post)
    {
        $data = $post->getProductsData();
        if (!is_null($data)) {
            $this->_getResource()->savePostRelation($post, $data);
        }
        return $this;
    }

    /**
     * get products for post
     *
     * @access public
     * @param Brander_UnitopBlog_Model_Post $post
     * @return Brander_UnitopBlog_Model_Resource_Post_Product_Collection

     */
    public function getProductCollection($post)
    {
        $collection = Mage::getResourceModel('brander_unitopblog/post_product_collection')
            ->addPostFilter($post);
        return $collection;
    }
}
