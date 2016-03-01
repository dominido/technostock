<?php
/**
 * Brander ProductBanners extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        ProductBanners
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_ProductBanners_Model_Banner extends Mage_Catalog_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'brander_productbanners_banner';
    const CACHE_TAG = 'brander_productbanners_banner';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'brander_productbanners_banner';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'banner';
    protected $_productInstance = null;

    /**
     * constructor
     *
     * @access public
     * @return void

     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('brander_productbanners/banner');
    }

    /**
     * before save banner
     *
     * @access protected
     * @return Brander_ProductBanners_Model_Banner

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
     * get the url to the banner details page
     *
     * @access public
     * @return string

     */
    public function getBannerUrl()
    {
        return Mage::getUrl('brander_productbanners/banner/view', array('id'=>$this->getId()));
    }

    /**
     * save banner relation
     *
     * @access public
     * @return Brander_ProductBanners_Model_Banner

     */
    protected function _afterSave()
    {
        $this->getProductInstance()->saveBannerRelation($this);
        return parent::_afterSave();
    }

    /**
     * get product relation model
     *
     * @access public
     * @return Brander_ProductBanners_Model_Banner_Product

     */
    public function getProductInstance()
    {
        if (!$this->_productInstance) {
            $this->_productInstance = Mage::getSingleton('brander_productbanners/banner_product');
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
     * @return Brander_ProductBanners_Resource_Banner_Product_Collection

     */
    public function getSelectedProductsCollection()
    {
        $collection = $this->getProductInstance()->getProductCollection($this);
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
