<?php
/**
 * Brander MarketExport extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        MarketExport
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_MarketExport_Model_Mysql4_Export_Collection
extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    private $minPrice;

    private $maxPrice;

    private $exportId;
    
    private $rating;

    protected function _construct()
    {
        $this->_init('marketexport/export');
    }

    public function setMaxPrice($maxPrice)
    {
        $this->maxPrice = $maxPrice;
    }
    
    public function setMinPrice($minPrice)
    {
        $this->minPrice = $minPrice;
    }
    
    public function setExportId($exportId)
    {
        $this->exportId = $exportId;
    }
    
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    public function _joinProductCategory($products, $excludeCategories, $model = null)
    {
        $url_key = Mage::getModel('catalog/category')->getResource()->getAttribute('url_key');
        $categoriesFilter = '';
        if ($excludeCategories) {
            $categoriesFilter = ' AND _categories.category_id NOT IN (' . implode($excludeCategories, ',') . ')';
        }
        if ($model) {
            $categories = $model->getCategories();
            if (!empty($categories)) {
                $categories = implode($categories, ',');
                $categoriesFilter .= ' AND _categories.category_id IN (' . $categories . ')';
            }
        }
        $products->getSelect()
            ->joinInner(array('_categories'=> 'catalog_category_product'),
                'e.entity_id = _categories.product_id' . $categoriesFilter,
                array('category'=>'_categories.category_id'))
            ->joinLeft(array('categories'=>$url_key->getBackend()->getTable()),
                    "_categories.category_id=categories.entity_id and categories.attribute_id = {$url_key->getId()}",
                    array('category_url_key'=>'categories.value'))
            ->group('_categories.product_id');
    }

    // ME6
    public function prepareProducts($excludeCategories = array(), $model = null, $includeOutOfStock = false, $store)
    {
        /** @var $products Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
        $products = Mage::getResourceModel('catalog/product_collection')
            ->addFieldToFilter('visibility', 4)
            ->addFieldToFilter('status', 1)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('special_price')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('manufacturer')
            ->addAttributeToSelect('short_description')
            ->addPriceData()
            ->addAttributeToSelect('small_image')
            ->addAttributeToSelect('image');


        $customAttributes = $model->getCustomAttributesData();
        $customAttributes = $customAttributes['db'];
        if ($customAttributes) {
            foreach ($customAttributes as $customAttributeKey => $customAttributeValue) {
                if ($customAttributeKey > 0) {
                    $products->addAttributeToSelect($customAttributeValue);
                }
            }
        }

        $products->setStoreId($store);
        $products->addUrlRewrite();


        if ((int)$includeOutOfStock == 0) {
            /** @var $stockModel Mage_Cataloginventory_Model_Mysql4_Stock */
            $stockModel = Mage::getResourceModel('cataloginventory/stock');
            $stockModel->setInStockFilterToCollection($products);
        }

        $this->_joinProductCategory($products, $excludeCategories, $model);

        //$this->_joinProductBrands($products);

        if($this->maxPrice!=null || $this->minPrice!=null){
            $this->_priceProductFilter($products, $this->minPrice, $this->maxPrice);
        }
        
        return $products;
    }
    
    public function _priceProductFilter($products, $min = null, $max = null)
    {
        $priceAttribute = Mage::getModel('catalog/product')->getResource()->getAttribute('price');
        $specialPriceAttribute = Mage::getModel('catalog/product')->getResource()->getAttribute('special_price');
       
        $products->getSelect()->joinInner(
                array('_price'=>$priceAttribute->getBackend()->getTable()),
                    "e.entity_id = _price.entity_id and _price.attribute_id = {$priceAttribute->getId()}",
                    array()
                )->joinLeft(
                array('special_price'=>$specialPriceAttribute->getBackend()->getTable()),
                        "e.entity_id = special_price.entity_id and special_price.attribute_id = {$specialPriceAttribute->getId()}",
                        array()
                );
        if ($min) {
            $products->getSelect()->where('IFNULL(special_price.value, _price.value) >=?', $min);
        }
        if ($max) {
            $products->getSelect()->where('IFNULL(special_price.value, _price.value) <= ?', $max);
        }
    }

    public function _brandProductFilter($products, $exportId=null)
    {
        $brands = Mage::getModel('marketexport/brand')
                ->getCollection()
                ->getBrandForExport($exportId)
                ->getFirstItem()
                ->getData();
        
        $brands = $brands['brands'];
        $brandAttribute = Mage::getModel('catalog/product')->getResource()->getAttribute('brand');

        $products->getSelect()->joinInner(array('_brand'=>$brandAttribute->getBackend()->getTable()),
                "e.entity_id = _brand.entity_id and _brand.attribute_id = {$brandAttribute->getId()} and _brand.value in ({$brands})");
    }
    
    public function _ratingProductFilter($products, $rating = null)
    {
        if($rating){
            $products->getSelect()
                    ->where('erp_products.rating > ?', $rating);
        }
    }

    public function _joinProductBrands(Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $products) {
        $products->getSelect()->join(
            array('brand_int' => 'catalog_product_entity_int'),
            'e.entity_id = brand_int.entity_id',
            array()
        );
        $products->getSelect()->join(
            array('brand_attribute' => $this->getTable('eav/attribute')),
            'brand_int.attribute_id = brand_attribute.attribute_id AND brand_attribute.frontend_input = \'composite\'',
            array()
        );
        $products->getSelect()->join(
            array('composite_option' => $this->getTable('compositeattribute/attribute_composite_option')),
            'brand_int.value = composite_option.option_id',
            array()
        );
        $products->getSelect()->join(
            array('composite_option_value' => $this->getTable('compositeattribute/attribute_composite_option_value')),
            'composite_option.option_id = composite_option_value.option_id',
            array('brand' => 'composite_option_value.name')
        );
    }

}
