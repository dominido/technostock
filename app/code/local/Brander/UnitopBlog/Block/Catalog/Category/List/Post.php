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
class Brander_UnitopBlog_Block_Catalog_Category_List_Post extends Mage_Core_Block_Template
{
    protected $_categoryFilter = array();
    /**
     * get the list of posts
     *
     * @access protected
     * @return Brander_UnitopBlog_Model_Resource_Post_Collection

     */
    public function getPostCollection()
    {
        $category = Mage::registry('current_category');
        $collection = Mage::getResourceModel('brander_unitopblog/post_collection')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', 1)
            ->addCategoryFilter($category);

        if ($this->_categoryFilter) {
            $collection->addAttributeToFilter('postscategory_id', $this->_categoryFilter);
        }

        $collection->setPageSize($this->getTabsPostsLimit())->setCurPage(1);
        $collection->getSelect()->order('related_category.position', 'DESC');

        return $collection;
    }

    public function getTabPostCollection($blogCategory)
    {
        $this->_categoryFilter = $blogCategory->getEntityId();
        return $this->getPostCollection();
    }

    protected function getTabsPostsLimit()
    {
        return Mage::helper('brander_shop')->getCfg('brander_unitopblog/product_category/posts_tab_limit');
    }

    protected function getAllPostsUrl($tabKey)
    {
        $category = Mage::registry('current_category');
        $params = array(
            'category'          => $category->getId(),
            'blog_category'     => $tabKey
        );

        if ($listKey = Mage::helper('brander_shop')->getCfg('brander_unitopblog/postscategory/url_rewrite_list')) {
            return Mage::getUrl('brander_unitopblog/postscategory/index', array('_direct'=>$listKey, '_query' => $params));
        }

        return Mage::getUrl('brander_unitopblog/postscategory/index', array('_query' => $params));
    }
}
