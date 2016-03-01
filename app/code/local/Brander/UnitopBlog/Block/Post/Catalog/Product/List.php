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
class Brander_UnitopBlog_Block_Post_Catalog_Product_List extends Mage_Catalog_Block_Product_Abstract
{

    protected function _construct() {
        parent::_construct();
        $this->addData(array(
            'cache_lifetime' => 72000,
            'cache_tags' => array(
                'UNIBLOG',
                'UNIBLOG_POST_PRODUCTS'
            )
        ));
    }

    public function getCacheKeyInfo() {
        $post = Mage::registry('current_post');
        $cacheId = array(
            'UNIBLOG_POST_PRODUCTS',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout(),
            'postid' => $post->getEntityId()

        );

        return $cacheId;
    }

    /**
     * get the list of products
     *
     * @access public
     * @return Mage_Catalog_Model_Resource_Product_Collection

     */
    public function getProductCollection()
    {
        $collection = $this->getPost()->getSelectedProductsCollection();
        $collection->addAttributeToSelect('name');
        $collection->addUrlRewrite();
        $collection->getSelect()->order('related.position');
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        return $collection;
    }

    /**
     * get current post
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Post

     */
    public function getPost()
    {
        return Mage::registry('current_post');
    }
}
