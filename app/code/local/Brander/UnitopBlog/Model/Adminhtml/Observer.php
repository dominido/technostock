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
class Brander_UnitopBlog_Model_Adminhtml_Observer
{
    /**
     * check if tab can be added
     *
     * @access protected
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    protected function _canAddTab($product)
    {
        if ($product->getId()) {
            return true;
        }
        if (!$product->getAttributeSetId()) {
            return false;
        }
        $request = Mage::app()->getRequest();
        if ($request->getParam('type') == 'configurable') {
            if ($request->getParam('attributes')) {
                return true;
            }
        }
        return false;
    }

    /**
     * add the post tab to products
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Brander_UnitopBlog_Model_Adminhtml_Observer
     */
    public function addProductPostBlock($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $product = Mage::registry('product');
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs && $this->_canAddTab($product)) {
            $block->addTab(
                'posts',
                array(
                    'label' => Mage::helper('brander_unitopblog')->__('Posts'),
                    'url'   => Mage::helper('adminhtml')->getUrl(
                        'adminhtml/unitopblog_post_catalog_product/posts',
                        array('_current' => true)
                    ),
                    'class' => 'ajax',
                )
            );
        }
        return $this;
    }

    /**
     * save post - product relation
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Brander_UnitopBlog_Model_Adminhtml_Observer

     */
    public function saveProductPostData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('posts', -1);
        if ($post != '-1') {
            $post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);
            $product = Mage::registry('product');
            $postProduct = Mage::getResourceSingleton('brander_unitopblog/post_product')
                ->saveProductRelation($product, $post);
        }
        return $this;
    }
    /**
     * add the post tab to categories
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Brander_UnitopBlog_Model_Adminhtml_Observer

     */
    public function addCategoryPostBlock($observer)
    {
        $tabs = $observer->getEvent()->getTabs();
        $content = $tabs->getLayout()->createBlock(
            'brander_unitopblog/adminhtml_catalog_category_tab_post',
            'category.post.grid'
        )->toHtml();
        $serializer = $tabs->getLayout()->createBlock(
            'adminhtml/widget_grid_serializer',
            'category.post.grid.serializer'
        );
        $serializer->initSerializerBlock(
            'category.post.grid',
            'getSelectedPosts',
            'posts',
            'category_posts'
        );
        $serializer->addColumnInputName('position');
        $content .= $serializer->toHtml();
        $tabs->addTab(
            'post',
            array(
                'label'   => Mage::helper('brander_unitopblog')->__('Posts'),
                'content' => $content,
            )
        );
        return $this;
    }

    /**
     * save post - category relation
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Brander_UnitopBlog_Model_Adminhtml_Observer

     */
    public function saveCategoryPostData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('posts', -1);
        if ($post != '-1') {
            $post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);
            $category = Mage::registry('category');
            $postCategory = Mage::getResourceSingleton('brander_unitopblog/post_category')
                ->saveCategoryRelation($category, $post);
        }
        return $this;
    }
}
