<?php

class Brander_CommerceML_Model_Importgpd extends Varien_Object
{
    // Use Attributes Data from Product
    const USE_ATTR_DATA_FROM_PRODUCT = 'brandercml/options/advanced_attr_data';
    
    // Product Images Path - Preg Filter
    const PROD_IMAGE_PATH_PREGFILTER = 'brandercml/images/pregfilter';
    
    // Product Images Path - Replace With
    const PROD_IMAGE_PATH_REPLACEWITH = 'brandercml/images/replacewith';
    
    // Exclude Base Image From Gallery
    const EXCLUDE_BASE_IMAGE = 'brandercml/images/exclude_base_image';

    // Set Product Is In Stock If Qty More Than Zero
    const IS_IN_STOCK = 'brandercml/options/is_in_stock';

    // Import Products Without Attributes
    const IMPORT_WITHOUT_ATTRIBUTES = 'brandercml/options/without_attr';

    const TYPE_MESSAGE_NEW      = 'new';
    const TYPE_MESSAGE_UPDATE   = 'update';
    const TYPE_MESSAGE_OLD      = 'out_of_stock';
    const TYPE_MESSAGE_REMOVE   = 'remove';

    protected $_statuses = array(
        self::TYPE_MESSAGE_NEW      => '0',
        self::TYPE_MESSAGE_UPDATE   => '0',
        self::TYPE_MESSAGE_OLD      => '0',
        self::TYPE_MESSAGE_REMOVE   => '0',

    );

    protected $_magmi;
    protected $_mapper;
    protected $_attributes      = array(
        'select'        => array(),
        'text'          => array()
    );
    protected $_storesIds;
    protected $_limit           = 0;
    protected $_skus            = array();
    protected $_categories      = array();
    protected $_products        = array(
        self::TYPE_MESSAGE_NEW => array(),
        self::TYPE_MESSAGE_UPDATE => array()
    );
    protected $_attributeSets   = array();
    protected $_offers          = array();
    protected $_allSku          = array();
    protected $_configurable    = array();
    protected $_importType      = 'catalog';
    protected $_confInOffers    = false;
    protected $_existsProducts  = array();
    protected $_counter         = array(
        'simple'        => 0,
        'configurable'  => 0
    );

    protected $_attributeBlackList = array(
        'Фото №3',
        'Фото №4'
    );

    protected $_superAttr       = array();

    const NODE_BASE_NODE        = 'Классификатор';
    //additional XML config
    // product node
    const NODE_CATALOG_PRODUCTS  = 'Товары/Товар';
    const NODE_PRODUCT_SKU      = 'Ид';
    const NODE_PRODUCT_CATEGORY = 'ГруппаИд';
    const NODE_PRODUCT_NAME     = 'Наименование';
    const NODE_PRODUCT_QTY      = 'Количество';
    const NODE_PRODUCT_PRICE    = 'ЦенаЗаЕдиницу';
    const NODE_NEW_FROM         = 'НовинкаС';
    const NODE_NEW_TO           = 'НовинкаПо';
    const NODE_PRODUCT_IN_SALE              = 'ВПродаже';
    const NODE_PRODUCT_SPECIAL_PRICE        = 'ЦенаЗаЕдиницуАкция';
    const NODE_PRODUCT_CURRENCY             = 'Валюта';
    const NODE_PRODUCT_WARRANTY             = 'Гарантия';
    const NODE_PRODUCT_DESCRIPTION          = 'Описание';
    const NODE_PRODUCT_SHORT_DESCRIPTION    = 'КраткоеОписание';
    const NODE_PRODUCT_MANUFACTURER         = 'Производитель';
    const NODE_PRODUCT_MANUFACTURER_SKU     = 'Артикул';
    const NODE_PRODUCT_ATTRIBUTES_LIST_SELECT       = 'ЗначенияАтрибутов/ЗначениеАтрибутов';
    const NODE_PRODUCT_ATTRIBUTES_LIST_TEXT         = 'ЗначенияСвойств/ЗначениеСвойства';

    // product images node
    const NODE_PRODUCT_IMAGES   = 'Картинки/Картинка';

    // category node
    const NODE_CATALOG_CATEGORIES       = 'Группы/Группа';
    const NODE_CATALOG_CATEGORY_ID      = 'Ид';
    const NODE_CATALOG_CATEGORY_NAME    = 'Наименование';
    const HOME_CATALOG_CATEGORY         = 'Каталог';

    // attributes node
    const NODE_ATTRIBUTE_ID      = 'Ид';
    const NODE_ATTRIBUTE_VALUE   = 'Значение';
    
    protected function getHelper()
    {
        return Mage::helper('brandercml/import');
    }

    protected function getLogHelper()
    {
        return Mage::helper('autoimport/log');
    }
    
    public function run()
    {
        Mage::dispatchEvent('brandercml_import_start', array('status' => 'start', 'data' => $this));
        if ($this->_loadSourceFile()) {
            if ($node = $this->getXml()->getNode()) {
                //$this->getLogHelper()->logMessage("File \"".$this->getXmlPath()."\" loaded.\r\nВерсияСхемы: ".$node->getAttribute('ВерсияСхемы')."\r\nДатаФормирования: ".$node->getAttribute('ДатаФормирования'), false);

                $this->getCategoryList();
                $this->getAllSkus();
                if (!Mage::getStoreConfig(self::IMPORT_WITHOUT_ATTRIBUTES)) {
                    //$this->processAttributes();
                }
                $this->processProducts();
                //$this->processOffers();
            }
        }


        // run stage three
        //$this->stageThereeProcess();

        Mage::register('item_statuses', json_encode($this->_statuses));
        return Brander_AutoImport_Model_Import::TASK_STATUS_IMPORT__PRODUCTS_COMPLETE;
    }

    protected function _loadSourceFile()
    {
        try {
            $xmlPath = $this->getSourceFilePath() . $this->getSourceFileName();
            if (is_file($xmlPath) && is_readable($xmlPath)) {
                $xmlObj = new Varien_Simplexml_Config($xmlPath);
                $this->setXml($xmlObj)->setXmlPath($xmlPath);
                return true;
            } else {
                //$this->getLogHelper()->logMessage('File "'. $this->getSourceFileName() .'" does not exist or not readable.');
                return false;
            }
        } catch (Exception $e) {
            $this->getHelper()->log($e->getMessage());
        }
        return false;
    }

    /**
     * Get magmi
     */
    public function getMagmi()
    {
        if (!$this->_magmi) {
            $this->_magmi = Mage::getModel('brandercml/magmi');
        }
        return $this->_magmi;
    }

    public function getCategoryList()
    {
        if (!$xml = $this->getXml()) {
            return false;
        }
        $mainCategoriesNodes = $xml->getXpath(self::NODE_BASE_NODE . DS . self::NODE_CATALOG_CATEGORIES);
        foreach ($mainCategoriesNodes as $_categoryNode) {
            $this->getCategory($_categoryNode);
        }

        return $this->_categories;
    }

    public function getCategory($categoryNode, $parentId = null)
    {
        $id = (string)$categoryNode->{self::NODE_CATALOG_CATEGORY_ID};
        
        if (!array_key_exists($id, $this->_categories) && $id) {
            
            $category = array();
            $category['id']         = $id;
            $category['parent_id']  = $parentId;
            $category['name']       = trim((string)$categoryNode->{self::NODE_CATALOG_CATEGORY_NAME});
            
            if ($parentId) {
                $category['path'] = trim($this->_categories[$parentId]['path'] . DS . $category['name']);
            } else {
                $category['path'] =  trim(self::HOME_CATALOG_CATEGORY . DS . $category['name']);
            }

            $this->_categories[$id] = $category;
            
            if ($childCategories = $categoryNode->xpath(self::NODE_CATALOG_CATEGORIES)) {
                foreach ($childCategories as $_childCategory) {
                    $this->getCategory($_childCategory, $id);
                }    
            }
        }
    }

    public function processProducts()
    {
        if (!$xml = $this->getXml()) {
            return false;
        }

        $helper     = $this->getHelper(); $attributeSetList = array();
        //$mapper     = $this->getAttributeMapper();

        if (($products = $xml->getXpath(self::NODE_BASE_NODE . DS . self::NODE_CATALOG_PRODUCTS)) && $xml->getNode(self::NODE_BASE_NODE . DS . self::NODE_CATALOG_PRODUCTS)->count()) {
            $counter = 0;

            foreach ($products as $key => $product) {
                $item = array();
                if (!(string)$product->{self::NODE_PRODUCT_SKU} || !(string)$product->{self::NODE_PRODUCT_NAME}) {
                    continue;
                }

                $description            = (string)$product->{self::NODE_PRODUCT_DESCRIPTION};
                $short_description      = (string)$product->{self::NODE_PRODUCT_SHORT_DESCRIPTION};
                $sku                    = (string)$product->{self::NODE_PRODUCT_SKU};
                $name                   = (string)$product->{self::NODE_PRODUCT_NAME};
                $manufacturerSku        = (string)$product->{self::NODE_PRODUCT_MANUFACTURER_SKU};
                $manufacturer           = (string)$product->{self::NODE_PRODUCT_MANUFACTURER};
                $newItemFrom            = (string)$product->{self::NODE_NEW_FROM};
                $newItemTo              = (string)$product->{self::NODE_NEW_TO};
                $warrantyTerm           = (string)$product->{self::NODE_PRODUCT_WARRANTY};

                if (!isset($this->_categories[(string)$product->{self::NODE_PRODUCT_CATEGORY}])) {
                    continue;
                }
                $attributeSet           = $this->_categories[(string)$product->{self::NODE_PRODUCT_CATEGORY}];
                $attributeSetList[$attributeSet['id']]     = $attributeSet['name'];
                $this->_attributeSets[$attributeSet['name']] = array();
                
                $item = array(
                    'sku'               => (string)$product->{self::NODE_PRODUCT_SKU},
                    'name'              => $name,
                    'product_name'      => $name,
                    'url'               => $this->getUrlFrontName($name),
                    'type'              => 'simple',
                    'product_type_id'   => 'simple',
                    'description'       => $description ? $description : '&nbsp;',
                    'short_description' => $short_description ? $short_description : '&nbsp;',
                    'weight'            => '0',
                    'status'            => '1',
                    'visibility'        => '4',
                    'tax_class_id'      => '0',
                    'price'             => '0.00',
                    'special_price'     => null,
                    'qty'               => '0',
                    'min_qty'           => '1',
                    'attribute_set'     => $attributeSet['name'],
                    'categories'        => $attributeSet['path']
                );

                if ($manufacturerSku) {
                    $item['manufacturer_sku'] = $manufacturerSku;
                }
                if ($manufacturer) {
                    $item['manufacturer'] = $manufacturer;
                }

                if ($warrantyTerm) {
                    $item['warranty'] = $warrantyTerm;
                }

                if ((string)$product->{self::NODE_PRODUCT_PRICE}) {
                    $item['price'] = (string)$product->{self::NODE_PRODUCT_PRICE};
                }

                if ((string)$product->{self::NODE_PRODUCT_SPECIAL_PRICE}) {
                    $item['special_price'] = (string)$product->{self::NODE_PRODUCT_SPECIAL_PRICE};
                }

                if ((int)$product->{self::NODE_PRODUCT_QTY}) {
                    $item['qty'] = (int)$product->{self::NODE_PRODUCT_QTY};
                } else {
                    $item['qty'] = 0;
                }
                
                if ((int)$product->{self::NODE_PRODUCT_IN_SALE}) {
                    $item['is_in_stock'] = (int)$product->{self::NODE_PRODUCT_IN_SALE};
                }

                if ($newItemFrom) {
                    $item['news_from_date'] = $newItemFrom;
                }
                if ($newItemTo) {
                    $item['news_to_date'] = $newItemTo;
                }


                // IMAGES
                $image = null;
                if (($images = $product->xpath(self::NODE_PRODUCT_IMAGES)) && count($images)) {
                    $image = (string)$images[0] ? '/' . (string)$images[0] : '';
                }

                if (isset($image) && $image) {
                    $image = $helper->prepareProductImage($image);
                    $item['image']          = '+' . $image;
                    $item['small_image']    = $image;
                    $item['thumbnail']      = $image;
                    $item['media_gallery']  = $helper->prepareProductMediaGallery($images);
                }

                // add attribute values
                $options = $product->xpath(self::NODE_PRODUCT_ATTRIBUTES_LIST_SELECT);
                if ((!empty($options)) && count($options)) {
                    foreach ($options as $option) {
                        $productAttrId  = (string)$option->{self::NODE_ATTRIBUTE_ID};
                        $value = (string)$option->{self::NODE_ATTRIBUTE_VALUE};
                        if (in_array($productAttrId, $this->_attributeBlackList)) {
                            continue;
                        }
                        if (!empty($value)) {
                            $this->_attributes['select'][$productAttrId]['attributeSets'][$attributeSet['name']] = $attributeSet['name'];
                            $option['value'] = $value;
                            //$this->_attributes['select'][$productAttrId]['values'][$value] = $value;
                            $item[$this->getAttributeCodeFromName($productAttrId)] = $value;
                            //$this->_attributeSets[$attributeSet['name']][$productAttrId] = $productAttrId;
                        }
                    }
                }

                $options = $product->xpath(self::NODE_PRODUCT_ATTRIBUTES_LIST_TEXT);
                if ((!empty($options)) && count($options)) {
                    foreach ($options as $option) {
                        $productAttrId  = (string)$option->{self::NODE_ATTRIBUTE_ID};
                        $value = (string)$option->{self::NODE_ATTRIBUTE_VALUE};
                        if (in_array($productAttrId, $this->_attributeBlackList)) {
                            continue;
                        }
                        if (!empty($value)) {
                            $this->_attributes['text'][$productAttrId]['attributeSets'][$attributeSet['name']] = $attributeSet['name'];
                            //$this->_attributes['text'][$productAttrId]['values'] = array();
                            $item[$this->getAttributeCodeFromName($productAttrId)] = $value;
                            //$this->_attributeSets[$attributeSet['name']][$productAttrId] = $productAttrId;
                        }
                    }
                }


                if (isset($this->_allSku[$item['sku']])) {
                    $type = self::TYPE_MESSAGE_UPDATE;
                } else {
                    $type = self::TYPE_MESSAGE_NEW;
                }
                $this->_products[$type][] = $item;

            }

            // create attribute sets
            if (count($attributeSetList)) {
                $attributeSetList = array_unique($attributeSetList);
                foreach ($attributeSetList as $_attributeSet) {
                    $this->createAttributeSet($_attributeSet);
                }
            }

            $this->getLogHelper()->logMessage('== START PRODUCTS IMPORT PROCESS ==');

            //process attributes
            $this->processAttributes();
            
            // update layered navigation
            if (Mage::helper('brander_layerednavigation')) {
                Mage::app()->getCacheInstance()->invalidateType('brander_layerednavigation');
            }

            if ($newProductsCount = count($this->_products[self::TYPE_MESSAGE_NEW])) {
                $this->getLogHelper()->logMessage('start create new products');
                $this->getMagmi()->importProducts($this->_products[self::TYPE_MESSAGE_NEW]);
                $this->_statuses[self::TYPE_MESSAGE_NEW] = $newProductsCount;
                $this->getLogHelper()->logMessage('create new ' . $newProductsCount . ' products complete');
            }

            if ($updateProductsCount = count($this->_products[self::TYPE_MESSAGE_UPDATE])) {
                $this->getLogHelper()->logMessage('start update products');
                $this->getMagmi()->importProducts($this->_products[self::TYPE_MESSAGE_UPDATE]);
                $this->_statuses[self::TYPE_MESSAGE_UPDATE] = $updateProductsCount;
                $this->getLogHelper()->logMessage('update ' . $updateProductsCount . ' products complete');
            }
        }
        return true;
    }

    /*protected function stageThereeProcess()
    {
        $oldProducts = array();
        if (Mage::helper('autoimport/stages')->getEnabled()) {
            $collection = Mage::getModel('autoimport/source_stages_stageThree')->getCollection();
            if ($collection->getSize()) {
                foreach ($collection as $product) {
                    $oldProducts[] = array(
                            'sku' => $product->getSku(),
                            'name' => $product->getName(),
                        );
                    }
                Mage::helper('autoimport/log')->logMessage('STAGE 3-RD START: prepare to remove products');
                $this->getMagmi()->deleteProducts($oldProducts);
                Mage::helper('autoimport/report')->saveProductsReport($oldProducts, self::TYPE_MESSAGE_REMOVE);
                $this->_statuses[self::TYPE_MESSAGE_REMOVE] = count($oldProducts);
                Mage::helper('autoimport/log')->logMessage('STAGE 3-RD COMPLETE: removed ' . count($oldProducts) . ' product(s)');

            } else {
                Mage::helper('autoimport/log')->logMessage('NO 3-RD STAGE PRODUCTS: skip stage');
            }
        }

    }*/

    protected function _preparePrice($price)
    {
        return str_replace(',', '.', $price);
    }

    public function getAttributes()
    {
        return $this->_attributes;
    }
    
    public function getAttributeMapper()
    {
        if (!$this->_mapper) {
            $this->_mapper = Mage::getModel('brandercml/import_mapper');
        }
        return $this->_mapper;
    }

    public function setLimit($limit = 0)
    {
        $this->_limit = $limit;
        return $this;
    }
    
    public function processAttributes()
    {
        if (!count($this->_attributes)) {
            return false;
        }

        $helper     = $this->getHelper();
        $mapper     = $this->getAttributeMapper();

        // start add select attributes
        if (count($this->_attributes['select'])) {
            //foreach attribute set
            foreach ($this->_attributes['select'] as $_attributeName => $_attributeInfo) {
                $_attribute = array();
                $attributeModel = Mage::getModel('brandercml/import_product_attribute');
                $_attributeCode = $this->getAttributeCodeFromName($_attributeName);
                $_attribute['label']        = $_attributeName;
                $_attribute['code']         = $_attributeCode;
                $_attribute['input']        = 'select';
                $_attribute['required']     = 0;
                $_attribute['filterable']   = true;
                $_attribute['global']       = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE;
                $_attribute['visible']      = true;
                $_attribute['visible_on_front'] = true;
                $_attribute['groups']       = $_attributeInfo['attributeSets'];
                //$_attribute['option']       = $_attributeInfo['values'];
                
                
                $params = new Varien_Object($_attribute);
                $attributeModel->push($_attributeCode, $params);
                $attributeModel->createAttribute($_attributeCode, $params);
                
                /*foreach ($_attributeInfo['values'] as $option) {
                    $id = Mage::getModel('eav/entity_attribute')->load($params->getCode(), 'attribute_code')->getId();
                    $setup = Mage::getModel('eav/entity_setup', 'core_setup');
                    $option['attribute_id'] = $id;
                    $setup->addAttributeOption($option);
                }*/
                
            }
        }

        if (count($this->_attributes['text'])) {
            //foreach attribute set
            foreach ($this->_attributes['text'] as $_attributeName => $_attributeInfo) {
                $_attribute = array();
                $attributeModel = Mage::getModel('brandercml/import_product_attribute');
                $_attributeCode = $this->getAttributeCodeFromName($_attributeName);
                $_attribute['label']        = $_attributeName;
                $_attribute['code']         = $_attributeCode;
                $_attribute['input']        = 'text';
                $_attribute['required']     = 0;
                $_attribute['filterable']   = false;
                $_attribute['global']       = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE;
                $_attribute['visible']      = true;
                $_attribute['visible_on_front'] = true;
                $_attribute['groups']       = $_attributeInfo['attributeSets'];

                $params = new Varien_Object($_attribute);

                $attributeModel->push($_attributeCode, $params);
                $attributeModel->createAttribute($_attributeCode, $params);
            }
        }

        if ($this->getConsoleLog()) {
            Mage::helper('autoimport/log')->logMessage('--- Product Attributes ---' . PHP_EOL);
            if (isset($removed)) {
                Mage::helper('autoimport/log')->logMessage('Deleted Attr: '. $removed . PHP_EOL);
            }
            //Mage::helper('autoimport/log')->logMessage('Created Attr: '. count($attr->getData()) . PHP_EOL . PHP_EOL);
        }
    }


    protected function getAttributeCodeFromName($name) 
    {
        $name =  Mage::helper('catalog/product_url')->format($name);
        $code = preg_replace('#[^0-9a-z]+#i', '', $name);
        $code = strtolower($code);
        $code = trim($code, '_');
        return $code;
    }

    protected function getUrlFrontName($name) {
        $name =  Mage::helper('catalog/product_url')->format($name);
        $url = preg_replace('#[^0-9a-z]+#i', '-', $name);
        $url = strtolower($url);
        $url = trim($url, '_');
        return $url;
    }
    protected function _prepareProps($props, &$item)
    {
        if (count($props)) {
            foreach ($props as $prop) {
                $propName = (string) $prop->{'Наименование'};
                $value = (string) $prop->{'Значение'};
                if ($value && $propName) {
                    $code = $this->getHelper()->prepareAttributeCode($propName);
                    $this->getAttributeMapper()->map($code);
                    $item[$code] = $value;
                }
            }
        }
    }

    protected function _getAllStoresIds()
    {
        if (!$this->_storesIds) {
            $this->_storesIds = array();
            $stores = Mage::app()->getStores();
            foreach ($stores as $eachStoreId => $eachStoreData) {
                $this->_storesIds[] = Mage::app()->getStore($eachStoreId)->getStoreId();
            }
        }
        return $this->_storesIds;
    }

    public function clearAttributes()
    {
        $this->setClearAttributes(true);
        return $this;
    }

    function createAttributeSet($setName, $copyGroupsFromID = -1)
    {
        $entityTypeId = Mage::getModel('eav/entity')
            ->setType('catalog_product')
            ->getTypeId(); // 4 - Default

        $attributeSetId     = Mage::getModel('eav/entity_attribute_set')
            ->getCollection()
            ->setEntityTypeFilter($entityTypeId)
            ->addFieldToFilter('attribute_set_name', $setName)
            ->getFirstItem()
            ->getAttributeSetId();

        if ($attributeSetId) {
            return false;
        }
        
        $newSet = Mage::getModel('eav/entity_attribute_set');
        $newSet->setEntityTypeId($entityTypeId);
        $newSet->setAttributeSetName($setName);
        try {
            $newSet->validate();
            $newSet->save();
        } catch(Exception $ex) {
            return;
        }

        try {
            $newSet->initFromSkeleton($entityTypeId);
            $newSet->save();
        } catch(Exception $ex) {
            return;
        }
    }

    public function getAllSkus()
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $table = $resource->getTableName('catalog_product_entity');

        $query = "SELECT `sku` FROM {$table}";
        $this->_allSku = array_flip($writeConnection->fetchCol($query));

        return true;
    }

}