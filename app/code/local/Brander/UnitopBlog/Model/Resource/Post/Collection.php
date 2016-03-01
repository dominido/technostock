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
class Brander_UnitopBlog_Model_Resource_Post_Collection extends Mage_Catalog_Model_Resource_Collection_Abstract
{
    protected $_joinedFields = array();

    /**
     * constructor
     *
     * @access public
     * @return void

     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('brander_unitopblog/post');
    }

    /**
     * get posts as array
     *
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array

     */
    protected function _toOptionArray($valueField='entity_id', $labelField='title', $additional=array())
    {
        $this->addAttributeToSelect('title');
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    /**
     * get options hash
     *
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @return array

     */
    protected function _toOptionHash($valueField='entity_id', $labelField='title')
    {
        $this->addAttributeToSelect('title');
        return parent::_toOptionHash($valueField, $labelField);
    }

    /**
     * add the product filter to collection
     *
     * @access public
     * @param mixed (Mage_Catalog_Model_Product|int) $product
     * @return Brander_UnitopBlog_Model_Resource_Post_Collection

     */
    public function addProductFilter($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $product = $product->getId();
        }
        if (!isset($this->_joinedFields['product'])) {
            $this->getSelect()->join(
                array('related_product' => $this->getTable('brander_unitopblog/post_product')),
                'related_product.post_id = e.entity_id',
                array('position')
            );
            $this->getSelect()->where('related_product.product_id = ?', $product);
            $this->_joinedFields['product'] = true;
        }
        return $this;
    }

    /**
     * add the category filter to collection
     *
     * @access public
     * @param mixed (Mage_Catalog_Model_Category|int) $category
     * @return Brander_UnitopBlog_Model_Resource_Post_Collection

     */
    public function addCategoryFilter($category)
    {
        if ($category instanceof Mage_Catalog_Model_Category) {
            $category = $category->getId();
        }
        if (!isset($this->_joinedFields['category'])) {
            $this->getSelect()->join(
                array('related_category' => $this->getTable('brander_unitopblog/post_category')),
                'related_category.post_id = e.entity_id',
                array('position')
            );
            $this->getSelect()->where('related_category.category_id = ?', $category);
            $this->_joinedFields['category'] = true;
        }
        return $this;
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @access public
     * @return Varien_Db_Select

     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        return $countSelect;
    }
}
