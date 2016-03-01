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
class Brander_UnitopBlog_Helper_Category extends Brander_UnitopBlog_Helper_Data
{

    /**
     * get the selected posts for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return array()

     */
    public function getSelectedPosts(Mage_Catalog_Model_Category $category)
    {
        if (!$category->hasSelectedPosts()) {
            $posts = array();
            foreach ($this->getSelectedPostsCollection($category) as $post) {
                $posts[] = $post;
            }
            $category->setSelectedPosts($posts);
        }
        return $category->getData('selected_posts');
    }

    /**
     * get post collection for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return Brander_UnitopBlog_Model_Resource_Post_Collection

     */
    public function getSelectedPostsCollection(Mage_Catalog_Model_Category $category)
    {
        return Mage::getResourceSingleton('brander_unitopblog/post_collection')->addCategoryFilter($category);
    }

    public function getTabsList()
    {
        $settings = Mage::helper('brander_shop')->getCfg('brander_unitopblog/product_category');
        if (!$settings->getShowCategoryTabs()) {
            return array();
        }

        $categoriesIds = $settings->getCategoryPostsTabs();
        if($categoriesIds) {
            $categoriesIds = explode(',', $categoriesIds);
            if (current($categoriesIds) == '') array_shift($categoriesIds);
        } else {
            return array();
        }

        $categoriesCollection = Mage::getResourceModel('brander_unitopblog/postscategory_collection')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', array('in' => $categoriesIds));
        if ($categoriesCollection->getSize()) {
            return $categoriesCollection;
        }
        return array();
    }
}
