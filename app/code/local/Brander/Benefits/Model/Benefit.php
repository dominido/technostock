<?php
/**
 * Brander Benefits extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        Benefits
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_Benefits_Model_Benefit extends Mage_Catalog_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'brander_benefits_benefit';
    const CACHE_TAG = 'brander_benefits_benefit';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'brander_benefits_benefit';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'benefit';
    protected $_productInstance = null;
    protected $_categoryInstance = null;

    /**
     * constructor
     *
     * @access public
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('brander_benefits/benefit');
    }

    /**
     * before save benefit
     *
     * @access protected
     * @return Brander_Benefits_Model_Benefit
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * get the benefit Benefit Text
     *
     * @access public
     * @return string
     */
    public function getBenefitText()
    {
        $benefit_text = $this->getData('benefit_text');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($benefit_text);
        return $html;
    }

    /**
     * save benefit relation
     *
     * @access public
     * @return Brander_Benefits_Model_Benefit
     */
    protected function _afterSave()
    {
        $this->getProductInstance()->saveBenefitRelation($this);
        $this->getCategoryInstance()->saveBenefitRelation($this);
        return parent::_afterSave();
    }

    /**
     * get product relation model
     *
     * @access public
     * @return Brander_Benefits_Model_Benefit_Product
     */
    public function getProductInstance()
    {
        if (!$this->_productInstance) {
            $this->_productInstance = Mage::getSingleton('brander_benefits/benefit_product');
        }
        return $this->_productInstance;
    }

    /**
     * get selected products array
     *
     * @access public
     * @return array
     */
    public function getSelectedProducts()
    {
        if (!$this->hasSelectedProducts()) {
            $products = array();
            foreach ($this->getSelectedProductsCollection() as $product) {
                $products[] = $product;
            }
            $this->setSelectedProducts($products);
        }
        return $this->getData('selected_products');
    }

    /**
     * Retrieve collection selected products
     *
     * @access public
     * @return Brander_Benefits_Resource_Benefit_Product_Collection
     */
    public function getSelectedProductsCollection()
    {
        $collection = $this->getProductInstance()->getProductCollection($this);
        return $collection;
    }

    /**
     * get category relation model
     *
     * @access public
     * @return Brander_Benefits_Model_Benefit_Category
     */
    public function getCategoryInstance()
    {
        if (!$this->_categoryInstance) {
            $this->_categoryInstance = Mage::getSingleton('brander_benefits/benefit_category');
        }
        return $this->_categoryInstance;
    }

    /**
     * get selected categories array
     *
     * @access public
     * @return array
     */
    public function getSelectedCategories()
    {
        if (!$this->hasSelectedCategories()) {
            $categories = array();
            foreach ($this->getSelectedCategoriesCollection() as $category) {
                $categories[] = $category;
            }
            $this->setSelectedCategories($categories);
        }
        return $this->getData('selected_categories');
    }

    /**
     * Retrieve collection selected categories
     *
     * @access public
     * @return Brander_Benefits_Resource_Benefit_Category_Collection
     */
    public function getSelectedCategoriesCollection()
    {
        $collection = $this->getCategoryInstance()->getCategoryCollection($this);
        return $collection;
    }

    /**
     * Retrieve default attribute set id
     *
     * @access public
     * @return int
     */
    public function getDefaultAttributeSetId()
    {
        return $this->getResource()->getEntityType()->getDefaultAttributeSetId();
    }

    /**
     * get attribute text value
     *
     * @access public
     * @param $attributeCode
     * @return string
     */
    public function getAttributeText($attributeCode)
    {
        $text = $this->getResource()
            ->getAttribute($attributeCode)
            ->getSource()
            ->getOptionText($this->getData($attributeCode));
        if (is_array($text)) {
            return implode(', ', $text);
        }
        return $text;
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        return $values;
    }
    
}
