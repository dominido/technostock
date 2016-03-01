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
class Brander_UnitopBlog_Block_Post_Catalog_Category_List extends Mage_Core_Block_Template
{
    /**
     * get the list of products
     *
     * @access public
     * @return Mage_Catalog_Model_Resource_Category_Collection

     */
    public function getCategoryCollection()
    {
        $collection = $this->getPost()->getSelectedCategoriesCollection();
        $collection->addAttributeToSelect('name');
        $collection->getSelect()->order('related.position');
        $collection->addAttributeToFilter('is_active', 1);
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
