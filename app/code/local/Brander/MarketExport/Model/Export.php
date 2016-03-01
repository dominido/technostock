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
class Brander_MarketExport_Model_Export extends Mage_Core_Model_Abstract
{
    //max length of name -1
    const MAX_NAME_LENGTH = 19;
    //recommend width and height of image
    const IMAGE_WIDTH_AND_HEIGHT = 200;
    /*const FULL_IMAGE_WIDTH_AND_HEIGHT = 200;*/
    //max product name length - 1
    const MAX_PRODUCT_NAME_LENGTH = 254;
    //max product description length - 1
    const MAX_PRODUCT_DESCRIPTION_LENGTH = 511;
    //yml file dir (must have first & end slashes)
    const FILE_DIR = '/exports/';

    // Export types
    const TYPE_YML = 0;
    const TYPE_YANDEX = 1;

    /**
     * @var array
     */
    protected $_excludeCategories = array();

    /**
     * @var array
     */
    protected $_categoryNames = array();


    /**
     * @var array
     */
    protected $_categoryLevels = array();


    /**
     * UTM bids for categories 
     * 
     * @var array
     */
    protected $_categoryBids = array();

    /**
     * @var XMLWriter
     */
    protected $xml;
    protected $export_path = '/var/exports/';
    protected $collection;

    protected $_logInfoArray = array();

    static $_MARKET_EXPORT_TYPES = array(
        self::TYPE_YML              => 'default',
        self::TYPE_YANDEX           => 'Yandex',
        );
    static $_STATUS_ARRAY = array(1 => 'Enable', 0 => 'Disable');


    protected function _construct()
    {
        $this->_init('marketexport/export');

        $this->collection = $this->getCollection();
    }


    public function prepareData($data)
    {
        unset($data['key']);
        unset($data['form_key']);
        unset($data['brand']);
        unset($data['rating_check']);
        
        return $data;
    }


    public function initPriceFilter($minPrice = null, $maxPrice = null)
    {
        $this->collection->setMaxPrice($maxPrice);
        $this->collection->setMinPrice($minPrice);
    }

    public function exportToYml($data)
    {
        $this->_logInfo('startTime', time());
        $this->_logInfo('name', $data['name']);
        $this->xml = new XMLWriter();
        $this->xml->openMemory();
        $this->xml->setIndentString('  ');
        $this->xml->startDocument('1.0', 'windows-1251');
        //$this->xml->writeDTD('yml_catalog', null, 'shops.dtd');
        $this->xml->text("\n");
        $this->xml->setIndent(true);
        if($data['type']==1){
            $this->xml->startElement('yml_catalog');
            $this->xml->writeAttribute('date',$data['date']);
        }else{
            $this->xml->startElement('price');
            $this->xml->writeElement('date', $data['date']);
        }
        foreach ($data['stores'] as $store) {
            /*$storeModel = Mage::getModel('core/store')->load($store);
            if(!$storeModel->getCode()) {
                $store = Mage::app()->getWebsite()->getDefaultGroup()->getDefaultStoreId();
            }*/
            Mage::app()->setCurrentStore($store);

            $this->xml->startElement('shop');

            if ($data['shopname']) {
                if($data['type'] == 0){
                    $this->xml->writeElement('firmName', $data['shopname']);
                }else{
                    $this->xml->writeElement('name', $data['shopname']);
                }
            }else {
                $this->xml->writeElement('name', Mage::getStoreConfig('exportconfig/general/marketexport_shop_name'));
            }

            if ($data['companyname']) {
                if($data['type'] == 1){
                    $this->xml->writeElement('company', $data['companyname']);
                }else{
                    $this->xml->writeElement('firmId', $data['companyname']);
                }
            }else {
                $this->xml->writeElement('company', Mage::getStoreConfig('exportconfig/general/marketexport_company_name'));
            }

            //
            if($data['type'] == 0){
                $this->xml->writeElement('rate','1');
            }
            if($data['type'] == 1){
                $this->xml->writeElement('url', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
                $this->xml->startElement('currencies');
                $this->xml->startElement('currency');
                $this->xml->writeAttribute('id', 'UAH');
                $this->xml->writeAttribute('rate', '1');
                $this->xml->endElement();
                $this->xml->endElement();
            }
            $this->exportCategoriesToYml($data['type']);

            // If export useing series type than make exportSeriesToYml
            // Else use default export - Export products

            switch( $data['type'] ){
                default:
                    $this->exportProductsToYml($data, $data['type'], $store);
                    break;
            }


            $this->xml->endElement();
        }

        $this->xml->endElement();
        $this->xml->endDocument();
        //Mage::app()->setCurrentStore($store);
        $this->_logRequest();
        return $this->xml->outputMemory();
    }

    public function exportCategoriesToYml($type)
    {
        $categories = Mage::getResourceModel('catalog/category_collection')
                        ->addFieldToFilter('level', array('gt' => 1))
                        ->addAttributeToSelect('is_share')
                        ->addAttributeToSelect('utm_bid')
                        ->addAttributeToSelect('name');

        $categoryIds = $this->getCategories();
        if (!empty($categoryIds)) {
            $categories->addFieldToFilter('entity_id', array('in' => $categoryIds));
        } else {
            $categoryIds = false;
        }

        $this->xml->startElement('categories');
        $rootCategoryId = Mage::app()->getStore()->getRootCategoryId();
        foreach ($categories as $_category) {
            if (!$_category->getEvent() && !$_category->getIsShare()) {
                $this->xml->startElement('category');
                if($type == 0){
                    $this->xml->writeElement('id', $_category->getId());
                    if ($_category->getParentId() != $rootCategoryId) {
                        if (!$categoryIds || in_array($_category->getParentId(), $categoryIds)) {
                            $this->xml->writeElement('parentId', $_category->getParentId());
                        }
                    }
                    $this->xml->text($_category->getName());
                    $this->xml->endElement();
                }else{
                    $this->xml->writeAttribute('id', $_category->getId());
                    if ($_category->getParentId() != $rootCategoryId) {
                        if (!$categoryIds || in_array($_category->getParentId(), $categoryIds)) {
                            $this->xml->writeAttribute('parentId', $_category->getParentId());
                        }
                    }
                    $this->xml->text($_category->getName());
                    $this->xml->endElement();
                }
                $this->_categoryNames[$_category->getId()] = $_category->getName();
                $this->_categoryBids[$_category->getId()] = $_category->getUtmBid();
                $this->_categoryLevels[$_category->getId()] = $_category->getLevel();
            } else {
                $this->_excludeCategories[] = $_category->getId();
            }
        }
        $this->_logInfo('categories', count($this->_categoryNames));
        $this->xml->endElement();
    }

    
    public function exportProductsToYml($data, $type, $store)
    {
        $products = $this->collection->prepareProducts($this->_excludeCategories, $this, $data['include_out_of_stock'], $store);
        $storeCode = Mage::app()->getStore($store)->getCode();

        /** @var $seoHelper Brander_Seo_Helper_Data */
        $seoHelper = Mage::helper('marketexport');
        if($type == 0){
            $this->xml->startElement('items');
        }
        else{
            $this->xml->startElement('offers');
        }

        foreach ($products as $_product) {
            $lastProductCatId = '';

            foreach ($_product->getCategoryIds() as $prodictCatId) {
                if (!empty($this->_categoryLevels[$prodictCatId])) {
                    $productCatId[$this->_categoryLevels[$prodictCatId]] = $prodictCatId;
                }
            }
            if (empty($productCatId)) {
                $lastProductCatId = '';
            }

            ksort($productCatId);
            $lastProductCatId = array_pop($productCatId);

            /** @var $_product Mage_Catalog_Model_Product */
            //$_category = Mage::getModel('catalog/category')->load($lastProductCatId);
            if($type == 0){
                $this->xml->startElement('item');
            }else{
                $this->xml->startElement('offer');
            }
            if($type == 0){
                $this->xml->writeElement('id', $_product->getId());
                $this->xml->writeElement('categoryId', $lastProductCatId);
                $this->xml->writeElement('code', $_product->getSku());
                $this->xml->startElement('vendor');
                $this->xml->text($_product->getResource()->getAttribute('manufacturer')->getFrontend()->getValue($_product));
                $this->xml->endElement();
                $this->xml->writeElement('name', substr($_product->getName(), 0, self::MAX_PRODUCT_NAME_LENGTH));
                $defaultDesc = (isset($data['description_default']) && $data['description_default'] !== '') ? $data['description_default'] : $_product->getShortDescription();
                $desc = Mage::helper('marketexport')->parseDescription($defaultDesc, $_product);
                if ($desc) {
                    $this->xml->writeElement('description', $desc);
                }

                $url = $_product->getUrlInStore(
                    array(
                        '_type'  => Mage_Core_Model_Store::URL_TYPE_WEB,
                        '_nosid' => true,
                        '_store' => $storeCode,
                        '_use_rewrite' => false
                    )
                );
                $storepath = Mage::app()->getStore($store)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
                $sitepath = Mage::app()->getStore($store)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
                if (!strpos($url, $storepath)) {
                    $url = str_replace($sitepath, $storepath, $url);
                }

                $this->xml->writeElement('url', $url);
                if ($_product->getImage()) {
                    $this->xml->writeElement('image',
                         Mage::getBaseUrl('media') . 'catalog/product' . $_product->getImage()
                    );
                }
                if ($_product->getSpecialPrice()) {
                    $price = $_product->getSpecialPrice();
                }
                else {
                    $price = $_product->getPrice();
                }
                                
                $this->xml->writeElement('priceRUAH', floatval($price));


            }
            else {
                $this->xml->writeAttribute('id', $_product->getId());
                if ($type == 0) {
                    $this->xml->writeAttribute('available', $_product->isSalable() ? 'true' : 'false');
                }

                $url = $_product->getUrlInStore(
                    array(
                        '_type'  => Mage_Core_Model_Store::URL_TYPE_WEB,
                        '_nosid' => true,
                        '_store' => $storeCode,
                        '_use_rewrite' => false
                    )
                );
                $storepath = Mage::app()->getStore($store)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
                $sitepath = Mage::app()->getStore($store)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
                if (!strpos($url, $storepath)) {
                    $url = str_replace($sitepath, $storepath, $url);
                }
                if ($_product->getSpecialPrice()) {
                    $price = $_product->getSpecialPrice();
                }
                else {
                    $price = $_product->getPrice();
                }
                $this->xml->writeElement('url', $url);
                $this->xml->writeElement('price', floatval($price));
                $this->xml->writeElement('currencyId','UAH');
                $this->xml->writeElement('categoryId', $lastProductCatId);
                $this->xml->writeAttribute('type','Own');
                if ($_product->getImage()) {
                    $this->xml->writeElement('picture',
                        Mage::getBaseUrl('media') . 'catalog/product' . $_product->getImage()
                    );
                }
                $this->xml->writeElement('delivery','true');
                $this->xml->writeElement('vendor',$_product->getResource()->getAttribute('manufacturer')->getFrontend()->getValue($_product));
                $this->xml->writeElement('vendorCode', $_product->getSku());
                $this->xml->writeElement('model', substr($_product->getName(), 0, self::MAX_PRODUCT_NAME_LENGTH));
                $defaultDesc = (isset($data['description_default']) && $data['description_default'] !== '') ? $data['description_default'] : $_product->getShortDescription();
                $desc = Mage::helper('marketexport')->parseDescription($defaultDesc, $_product);
                if ($desc) {
                    $this->xml->writeElement('description', $desc);
                }
            }
            $this->xml->endElement();//offer
        }
        $this->xml->endElement();//offers
        $this->_logInfo('products', count($products));
    }


    protected $_getOptionCode = array();
    
    protected function _getOptionCode($attribute, $value)
    {
        $cacheCode = $attribute . '-' . $value;
        if (isSet($this->_getOptionCode[$cacheCode])){
            return $this->_getOptionCode[$cacheCode];
        }
        
        $return = null;
        
        foreach ($attribute->getSource()->getAllOptions(true) as $option) {
            if ($option['value'] == $value && isSet($option['option_code'])){
                $return = $option['option_code'];
                break;
            }
        }
        
        $this->_getOptionCode[$cacheCode] = $return;
        
        return $this->_getOptionCode[$cacheCode];
    }

    /**
     * @param $value
     * @return Brander_MarketExport_Model_Export
     */
    public function setCategories($value) {
        $this->setData('categories', serialize($value));
        return $this;
    }

    public function setStores($value) {
        $this->setData('stores', serialize($value));
        return $this;
    }

    public function setCustomAttributesData($value) {
        $this->setData('custom_attributes_data', serialize($value));
        return $this;
    }

    public function getCategories() {
        return unserialize($this->getData('categories'));
    }

    public function getStores() {
        return unserialize($this->getData('stores'));
    }

    public function getCustomAttributesData() {
        return unserialize($this->getData('custom_attributes_data'));
    }

    protected function _logInfo($name, $value)
    {
        $this->_logInfoArray[$name] = $value;
    }

    protected function _logRequest()
    {
        $message = 'MARKET EXPORT';
        $message .= ' | Date: ' . now();
        if (isset($this->_logInfoArray['startTime'])) {
            $exTime = time() - $this->_logInfoArray['startTime'];
            $message .= ' | Execution time: ' . $exTime;
        }
        if (isset($this->_logInfoArray['name'])) {
            $message .= ' | Name: ' . $this->_logInfoArray['name'];
        }
        if (isset($this->_logInfoArray['categories'])) {
            $message .= ' | Categories: ' . $this->_logInfoArray['categories'];
        }
        if (isset($this->_logInfoArray['products'])) {
            $message .= ' | Products: ' . $this->_logInfoArray['products'];
        }
        $message .= ' | From: ' . gethostbyaddr(Mage::app()->getRequest()->getServer('REMOTE_ADDR'));
        Mage::log($message);
    }
}
