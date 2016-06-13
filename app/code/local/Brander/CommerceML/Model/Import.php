<?php

class Brander_CommerceML_Model_Import extends Varien_Object
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

    protected $_magmi;
    protected $_mapper;
    protected $_attributes;
    protected $_storesIds;
    protected $_limit           = 0;
    protected $_skus            = array();
    protected $_categories      = array();
    protected $_products        = array();
    protected $_offers          = array();
    protected $_skusInc         = array();
    protected $_configurable    = array();
    protected $_importType      = 'catalog';
    protected $_confInOffers    = false;
    protected $_existsProducts  = array();
    protected $_source          = array(
        'catalog' => 'import.xml',
        'offers'  => 'offers.xml'
    );
    protected $_counter         = array(
        'simple'        => 0,
        'configurable'  => 0
    );
    protected $_superAttr       = array();

    public function getAttribute($id = null)
    {
        if (!$this->_attributes) {
            $this->_attributes = Mage::getModel('brandercml/import_product_attribute');
        }
        if ($id !== null) {
            return $this->_attributes->getAttribute($id);
        }
        return $this->_attributes;
    }

    public function setConfigurableInOffers($state = false)
    {
        $this->_confInOffers = $state;
        return $this;
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

    /**
     * Set import type
     */
    public function setImportType($type)
    {
        if (array_key_exists($type, $this->_source)) {
            $this->_importType = $type;
        }
        return $this;
    }

    /**
     * Get import type
     */
    public function getImportType()
    {
        return $this->_importType;
    }

    /**
     * Set source file
     */
    public function setSourceFile($file, $type = null)
    {
        if ($type === null) {
            $type = $this->getImportType();
        }
        if (array_key_exists($type, $this->_source)) {
            $this->_source[$type] = $file;
        }
        return $this;
    }

    /**
     * Get source file
     */
    public function getSourceFile($type = null)
    {
        $type = $type ? $type : $this->getImportType();
        return $this->_source[$type];
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

    protected function _loadSourceFile($type = null)
    {
        $helper = Mage::helper('brandercml/import');
        try {

            if ($type !== null) {
                $xmlPath = $helper->getImportFilePath($this->getSourceFile($type));
                if (is_file($xmlPath) && is_readable($xmlPath)) {
                    $xmlObj = new Varien_Simplexml_Config($xmlPath);
                    return $xmlObj;
                } else {
                    $helper->log('File "'. $xmlPath .'" does not exist or not readable.');
                    return false;
                }
            }

            $xmlPath = $helper->getImportFilePath($this->getSourceFile());
            if (is_file($xmlPath) && is_readable($xmlPath)) {
                $xmlObj = new Varien_Simplexml_Config($xmlPath);
                $this->setXml($xmlObj)->setXmlPath($xmlPath);
                return true;
            } else {
                $helper->log('File "'. $xmlPath .'" does not exist or not readable.');
            }
        } catch (Exception $e) {
           $helper->log($e->getMessage());
        }
        return false;
    }

    public function run()
    {
        $helper = Mage::helper('brandercml/import');
        Mage::dispatchEvent('brandercml_import_start', array('status' => 'start', 'data' => $this));
        if ($this->_loadSourceFile()) {
            if ($node = $this->getXml()->getNode()) {
                $helper->log("File \"".$this->getXmlPath()."\" loaded.\r\nВерсияСхемы: ".$node->getAttribute('ВерсияСхемы')."\r\nДатаФормирования: ".$node->getAttribute('ДатаФормирования'), false);
                switch ($this->getImportType()) {
                    case 'catalog':
                        if (!Mage::getStoreConfig(self::IMPORT_WITHOUT_ATTRIBUTES)) {
                            $this->processAttributes();
                        }
                        $this->processProducts();
                        break;
                    case 'offers':
                        $this->processOffers();
                        break;
                }
            }
        }
        Mage::dispatchEvent('brandercml_import_end', array('status' => 'start', 'data' => $this));
        return $this;
    }

    public function processAttributes()
    {
        if (!$xml = $this->getXml()) {
            return false;
        }

        if ($this->getClearAttributes()) {
            $removed = $this->getAttribute()->removeAllCreatedAttributes();
        }

        $helper     = Mage::helper('brandercml/import');
        $attr       = $this->getAttribute();
        $mapper     = $this->getAttributeMapper();
        $prefix     = $attr->getPrefix() ? $attr->getPrefix() : '';

        if (Mage::getStoreConfig(self::USE_ATTR_DATA_FROM_PRODUCT)) {
            $attributes = $xml->getXpath('Каталог/Товары/Товар/ХарактеристикиТовара/ХарактеристикаТовара');
        } else {
            $attributes = $xml->getXpath('Классификатор/Свойства/СвойствоНоменклатуры');
            if (!$attributes) {
                $attributes = $xml->getXpath('Классификатор/Свойства/Свойство');
            }
        }

        if ($attributes && count($attributes)) {
            foreach ($attributes as $attribute) {
                $id = (string)$attribute->{'Ид'};
                if (!$id || ($id && $attr->getAttribute($id))) {
                    continue;
                }

                $name       = (string)$attribute->{'Наименование'};
                $code       = (string)$attribute->{'КодСвойства'};
                $type       = (string)$attribute->{'ТипСвойства'};
                $filterable = (string)$attribute->{'ИспользоватьВНавигации'};
                $values     = $attribute->xpath('ВариантыЗначений/Справочник');

                $code = $helper->prepareAttributeCode($name, $code);
                if (!$mapper->map($code)) {
                    $code = $prefix . $code;
                }

                $params = new Varien_Object(array(
                    'label'         => $helper->prepareAttributeName($name),
                    'code'          => $code,
                    'input'         => $attr->isAllowType($type) ? $type : $attr->getDefaultType(),
                    'required'      => (string)$attribute->{'Обязательное'} === 'true' ? 1 : 0,
                    'searchable'    => (string)$attribute->{'ИспользоватьВПоиске'} === 'true' ? 1 : 0,
                    'filterable'    => $helper->prepareAttributeFilterable($filterable)
                ));

                if (count($values)) {
                    $preparedValues = array();
                    foreach ($values as $value) {
                        $valueID = (string)$value->{'ИдЗначения'};
                        $valueVal = (string)$value->{'Значение'};
                        $preparedValues[$valueID] = $valueVal;
                    }
                    $params->setValues($preparedValues);
                }

                $attr->push($id, $params);
                if (!$this->getIsDebugMode()) {
                    $attr->createAttribute($id, $params);
                }
            }
        }

        if ($this->getConsoleLog()) {
            echo '--- Product Attributes ---' . PHP_EOL;
            if (isset($removed)) {
                echo 'Deleted Attr: '. $removed . PHP_EOL;
            }
            echo 'Created Attr: '. count($attr->getData()) . PHP_EOL . PHP_EOL;
        }
    }

    public function processProducts()
    {
        if (!$xml = $this->getXml()) {
            return false;
        }

        $helper     = Mage::helper('brandercml/import');
        $mapper     = $this->getAttributeMapper();

        if (($products = $xml->getNode('Каталог/Товары/Товар')) && $products->count()) {
            $counter = 0;

            /** offers configurable product */
            $offersXml = $this->_loadSourceFile('offers');
            $offersProduct = array();
            if ($offersXml instanceof Varien_Simplexml_Config) {
                $offers = $offersXml->getNode('ПакетПредложений/Предложения/Предложение');
                if ($offers->count()) {
                    foreach ($offers as $offer) {
                        $offerId = (string) $offer->{'Ид'};
                        if ($this->_confInOffers && strpos($offerId, '#')) {
                            list($configurableId, $dataId) = explode('#', $offerId);
                            $offersProduct[$configurableId][] = $offer;
                        } else {
                            if ($this->_confInOffers) {
                                $offersProduct[$offerId][$offerId] = $offer;
                            } else {
                                $offersProduct[$offerId] = $offer;
                            }
                        }
                    }
                }
                $priceTypes = $offersXml->getNode('ПакетПредложений/ТипыЦен/ТипЦены');
                if ($priceTypes->count()) {
                    foreach ($priceTypes as $priceType) {
                        $id = (string) $priceType->{'Ид'};
                        $name = (string) $priceType->{'Наименование'};
                        if ($id && $name) {
                            switch ($name) {
                                case $this->getBasePriceName():
                                    $this->setBasePriceTypeId($id);
                                    break;
                                case $this->getSpecialPriceName():
                                    $this->setSpecialPriceTypeId($id);
                                    break;
                            }
                        }
                    }
                    if (!$this->hasBasePriceTypeId()) {
                        // !!!WARNING - Varien_Simplexml_Element return first array element by default
                        // if you call current() for Varien_Simplexml_Element, will be returned first
                        // element in Varien_Simplexml_Element body
                        $this->setBasePriceTypeId((string) current($priceTypes));
                    }
                }
                unset($offers);
                unset($priceTypes);
            }

            foreach ($products as $key => $product) {
                if (!$sku = (string)$product->{'Артикул'}) {
                    continue;
                }
                if (!$name = (string)$product->{'Наименование'}) {
                    continue;
                }

                $cmlId                  = (string)$product->{'Ид'};
                $description            = (string)$product->{'Описание'};
                $short_description      = (string)$product->{'КраткоеОписание'};
                $sku                    = $this->_skuIncrementId($sku);

                $item = array(
                    'sku'               => $sku,
                    'name'              => $name,
                    'product_name'      => $name,
                    'type'              => 'simple',
                    'product_type_id'   => 'simple',
                    'description'       => $description ? $description : '&nbsp;',
                    'short_description' => $short_description ? $short_description : '&nbsp;',
                    'weight'            => '0',
                    'status'            => '1',
                    'visibility'        => '4',
                    'tax_class_id'      => '0',
                    'commerceml_id'     => $cmlId,
                    'price'             => '0.00',
                    'special_price'     => null,
                    'qty'               => '0',
                    'min_qty'           => '1'
                );

                // set product offers data
                if (isset($offersProduct[$cmlId])) {
                    $offerItem = $offersProduct[$cmlId];
                    if (is_array($offerItem)) {
                        $offerItem = current($offerItem);
                    }
                    //product pricing
                    if ($price = $offerItem->xpath('Цены/Цена')) {
                        foreach ($price as $p) {
                            $priceTypeId = (string) $p->{'ИдТипаЦены'};
                            if ($priceValue = (string) $p->{'ЦенаЗаЕдиницу'}) {
                                $priceValue = $this->_preparePrice($priceValue);
                                if ($priceTypeId == $this->getBasePriceTypeId()) {
                                    $item['price'] = $priceValue;
                                } else if ($priceTypeId == $this->getSpecialPriceTypeId()) {
                                    $item['special_price'] = $priceValue;
                                }
                            }
                        }
                    }
                    //product is_in_stock
                    $item['is_in_stock'] = (string)$offerItem->{'ЕстьВНаличии'} == 'true' || Mage::getStoreConfig(self::IS_IN_STOCK) ? '1' : '0';
                    //product qty
                    if ($qty = (int)$offerItem->{'Количество'}) {
                        $item['qty'] = $qty;
                    }
                }

                if ($this->getIgnoreSimpleProductWithPriceZero() && $item['price'] <= 0) {
                    $counter++;
                    continue;
                }
                
                if (($images = $product->xpath('Картинки/Картинка')) && count($images)) {
                    $image = (string)$images[0] ? '/' . (string)$images[0] : '';
                } else if ($images = $product->xpath('Картинка')) {
                    $image = (string)$images[0] ? '/' . (string)$images[0] : '';
                }

                if (isset($image) && $image) {
                    $image = $helper->prepareProductImage($image);
                    $item['image']          = '+' . $image;
                    $item['small_image']    = $image;
                    $item['thumbnail']      = $image;
                    $item['media_gallery']  = $helper->prepareProductMediaGallery($images);
                }

                if (!Mage::getStoreConfig(self::IMPORT_WITHOUT_ATTRIBUTES)) {
                    $options = $product->xpath("ЗначенияСвойств/ЗначенияСвойства");
                    foreach ($options as $option) {
                        $productAttrId  = (string)$option->{'Ид'};
                        $value = (string)$option->{'Значение'};
                        if ($attribute = $this->getAttribute($productAttrId)) {
                            $attrValue = $this->getAttribute()->getAttributeValueByCode($attribute->getCode(), $value);
                            if ($attrValue) {
                                $value = $attrValue;
                            }
                            if ($value) {
                                $item[$attribute->getCode()] = $value;
                            }
                        }
                    }
                }

                // product categories
                $groups = array_merge($product->xpath("Группы/Группа"), $product->xpath("Группы/Ид"));
                foreach ($groups as $categoryId) {
                    $categories = array();
                    if ($category = $this->getCategory((string)$categoryId)) {
                        $categories[] = $category;
                    }
                    if (count($categories)) {
                        $item['categories'] = implode(';', $categories);
                    }
                }

                // retrieve product attributes
                $props = array_merge($product->xpath('ЗначенияРеквизитов/ЗначениеРеквизита'), $product->xpath('ХарактеристикиТовара/ХарактеристикаТовара'));

                /**
                 * Get all simple products from offers file
                 */
                $offerItems = $offerItemsCount = null;
                if ($this->_confInOffers && array_key_exists($cmlId, $offersProduct)) {
                    $offerItems         = $offersProduct[$cmlId];
                    $offerItemsCount    = count($offerItems);
                    if ($offerItemsCount) {
                        foreach ($offerItems as $offerItem) {
                            $offerProps = array_merge($props, $offerItem->xpath('ХарактеристикиТовара/ХарактеристикаТовара'));
                            $newItem = $item;
                            $this->_prepareProps($offerProps, $newItem);

                            // has configurable attributes
                            if (!$this->_hasConfigurableAttributes($newItem) && $offerItemsCount == 1) {
                                unset($newItem);
                                unset($offerProps);
                                $offerItemsCount = null;
                                break;
                            }

                            $newItem['sku'] = $this->_skuIncrementId($sku);
                            $this->_products[$newItem['sku']] = $newItem;

                            $offerID = (string)$offerItem->{'Ид'};
                            $this->_skus[$offerID] = $newItem['sku'];
                            $this->_configurable[$cmlId][] = $newItem['sku'];
                            $this->_counter['simple']++;
                        }
                    }
                }
                
                if (!$offerItemsCount) {
                    // attach to item attributes
                    $this->_prepareProps($props, $item);
                    $this->_products[$sku] = $item;
                    // increment simple product counter
                    $this->_counter['simple']++;
                    // check if configurable product located in import file
                    if (strpos($cmlId, '#')) {
                        list($configurableId, $dataId) = explode('#', $cmlId);
                        $this->_configurable[$configurableId][] = $sku;
                    }
                }

                // mapping: CommerceML ID => sku
                $this->_skus[$cmlId]    = $sku;

                if ($this->_limit > 0 && ++$counter >= $this->_limit) {
                    break;
                }
            } //end product foreach

            // prepare configurable products
            $this->_prepareConfigurable();

            if (!$this->getIsDebugMode()) {
                if ($ptype = $this->getImportProductByType()) {
                    foreach ($this->_products as $_key => $_product) {
                        if ($_product['type'] != $ptype) {
                            unset($this->_products[$_key]);
                        }
                    }
                }
                $this->getMagmi()->importProducts($this->_products);
                $this->_updateConfigurableAttributesOptionValue();
            } else {
                if ($this->getDumpDebugData()) Zend_Debug::dump($this->_products);
            }
            if ($this->getConsoleLog()) {
                echo '--- Catalog Import ---' . PHP_EOL;
                echo 'Simple Products: '. $this->_counter['simple'] . PHP_EOL;
                echo 'Configurable Products: '. $this->_counter['configurable'] . PHP_EOL;
                echo 'Total Products: '. count($this->_products) . PHP_EOL;
                echo 'Categories: '. count($this->_categories) . PHP_EOL . PHP_EOL;
            }
        }
    }

    protected function _preparePrice($price)
    {
        return str_replace(',', '.', $price);
    }

    protected function _prepareProps($props, &$item)
    {
        if (count($props)) {
            foreach ($props as $prop) {
                $propName = (string) $prop->{'Наименование'};
                $value = (string) $prop->{'Значение'};
                if ($value && $propName) {
                    $code = Mage::helper('brandercml/import')->prepareAttributeCode($propName);
                    $this->getAttributeMapper()->map($code);
                    $item[$code] = $value;
                }
            }
        }
    }

    protected function _hasConfigurableAttributes(&$item)
    {
        if ($confAttr = $this->getConfigurableAttributes()) {
            foreach (explode(',', $confAttr) as $attrCode) {
                if (array_key_exists($attrCode, $item)) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function _existsProduct($sku)
    {
        if (!$this->_existsProducts) {
            $products = Mage::getResourceModel('catalog/product_collection')->addAttributeToFilter('type_id', 'configurable');
            foreach ($products as $product) {
                $this->_existsProducts[] = $product->getSku();
            }
        }
        return in_array($sku, $this->_existsProducts);
    }

    protected function _prepareConfigurable()
    {
        foreach ($this->_configurable as $id => $simples) {
            if (count($simples) >= 1) {
                $simpleSku = current($simples);
                if (isset($this->_products[$simpleSku])) {
                    $simpleData = $this->_products[$simpleSku];
                    $origSku    = $this->getSkuById($id);
                    $sku        = $origSku ? $origSku : $id;
                    $price      = $this->_prepareConfigurablePrice($simples, $sku);

                    $item = array(
                        'sku'                       => $sku,
                        'type'                      => 'configurable',
                        'commerceml_id'             => $id,
                        'is_in_stock'               => $this->getAutodetectConfigurableProductIsInStock() ? $this->_getConfigurableProductIsInStock($simples) : 0,
                        'simples_skus'              => implode(',', $simples),
                        'price'                     => $price->getBasePrice(),
                        'special_price'             => $price->getSpecialPrice()
                    );

                    // replace (in simple products) configurable attributes value
                    if ($this->hasReplaceConfigurableAttributesValue()) {
                        $this->_replaceConfigurableAttributesValue($simples);
                    }

                    // configurable attributes
                    $confAttr = $this->getConfigureByAvailableAttributes() ? $this->_prepareConfigurableAttributes($simples) : $this->getConfigurableAttributes();
                    if ($confAttr) {
                        $item['configurable_attributes'] = $confAttr;
                    }

                    // super attributes pricing
                    if ($pricing = $this->_prepareSuperAttributesPricing($simples, $price)) {
                        $item['super_attribute_pricing'] = $pricing;
                    }

                    $importantFieldsKey = array_keys($item);
                    $item = array_merge($simpleData, $item);

                    if ($this->_existsProduct($sku)) {
                        $allowFields = $this->getConfigurableProductsAllowFields();
                        $allowFields = is_array($allowFields) ? array_merge($importantFieldsKey, $allowFields) : $importantFieldsKey;
                        foreach (array_keys($item) as $key) {
                            if (!in_array($key, $allowFields)) {
                                unset($item[$key]);
                            }
                        }
                    }

                    $this->_products[] = $item;
                    $this->_counter['configurable']++;
                }
            }
        }
    }

    protected function _prepareConfigurablePrice(&$simpleSkus, $sku = null)
    {
        $basePrice          = '0.00';
        $specialPrice       = null;
        $errors             = array();
        $smplBasePrices      = array();
        $smplSpecialPrices   = array();

        if (is_array($simpleSkus)) {
            $simpleProductPrices = array();
            foreach ($simpleSkus as $simpleSku) {
                if (isset($this->_products[$simpleSku])) {
                    $simple = $this->_products[$simpleSku];
                    $smplBasePrices[$simpleSku]    = $simple['price'];
                    $smplSpecialPrices[$simpleSku] = $simple['special_price'];
                }
            }

            if ($type = $this->getConfigurablePriceBySimpleSpecialPrice()) {
                asort($smplSpecialPrices);

                switch ($type) {
                    case 'mid':
                        // min price & special_price
                        $minPkey    = key($smplSpecialPrices);
                        $minP       = $smplBasePrices[$minPkey];
                        $minSP      = $smplSpecialPrices[$minPkey];
                        // max price & special_price
                        end($smplSpecialPrices);
                        $maxPkey    = key($smplSpecialPrices);
                        $maxP       = $smplBasePrices[$maxPkey];
                        $maxSP      = $smplSpecialPrices[$maxPkey];
                        // calculate
                        $basePrice      = round(($maxP - $minP) / 2 + $minP);
                        $specialPrice   = round(($maxSP - $minSP) / 2 + $minSP);
                        break;
                    case 'max':
                        end($smplSpecialPrices);
                        $key            = key($smplSpecialPrices);
                        $basePrice      = $smplBasePrices[$key];
                        $specialPrice   = $smplSpecialPrices[$key];
                        break;
                    case 'min': default:
                        $key            = key($smplSpecialPrices);
                        $basePrice      = $smplBasePrices[$key];
                        $specialPrice   = $smplSpecialPrices[$key];
                        break;
                }
            }
        }

        if (count($errors)) {
            Mage::helper('brandercml/import')->log(implode($errors, "\n"));
        }

        return new Varien_Object(array(
            'base_price'    => $basePrice,
            'special_price' => $specialPrice
        ));
    }

    protected function _arrayUniquePrice(&$prices)
    {
        
    }

    protected function _replaceConfigurableAttributesValue(&$simpleSkus)
    {
        if (is_array($simpleSkus)) {
            if ($this->getConsoleLog()) {echo '[START] Prepare replace configurable attributes value:' . PHP_EOL;}
            $attr = $this->getReplaceConfigurableAttributesValue();
            if (is_array($attr) && count($attr)) {
                $maskAttr = current($attr);
                $attr = key($attr);
            }
            $confAttributes = isset($maskAttr) ? $maskAttr : explode(',', $this->getConfigurableAttributes());
            foreach ($simpleSkus as $simpleSku) {
                if (isset($this->_products[$simpleSku])) {
                    $simple = &$this->_products[$simpleSku];
                    if (isset($simple[$attr])) {
                        $availableAttr = array();
                        foreach ($confAttributes as $confAttr) {
                            if (isset($simple[$confAttr])) {
                                $availableAttr[] = $simple[$confAttr];
                            }
                        }
                        $originValue = $simple[$attr];
                        $simple[$attr] = str_replace(array(',', '  ', ' '), array('_','','_'), implode('_', $availableAttr));
                        $this->_superAttr[$attr][$simple[$attr]] = $originValue;
                    }
                }
            }
            if ($this->getConsoleLog()) {
                printf("\t" . 'Prepared data OK:%s' . PHP_EOL, count($this->_superAttr[$attr]));
                echo '[END] Prepare replace configurable attributes value:' . PHP_EOL . PHP_EOL;
            }
            //Zend_Debug::dump($this->_superAttr);
        }
    }

    protected function _updateConfigurableAttributesOptionValue()
    {
        if ($attrCode = $this->getReplaceConfigurableAttributesValue()) {
            if ($this->getConsoleLog()) {echo '[START] Update configurable attributes option value' . PHP_EOL;}
            if (is_array($attrCode)) {
                $attrCode = key($attrCode);
            }
            $attrResourceModel = Mage::getModel('catalog/resource_eav_attribute');
            $attrId = Mage::getModel('eav/entity_attribute')->load($attrCode, 'attribute_code')->getId();
            if ($attrId) {
                $attrResourceModel->load($attrId);
                //$allOptions = $attrResourceModel->getSource()->getAllOptions(false);
                $allOptions = Mage::getResourceModel('eav/entity_attribute_option_collection')
                    ->setAttributeFilter($attrId)
                    ->setStoreFilter(0, false)
                    ->getData();
                $data = $values = array();
                foreach ($allOptions as $option) {
                    if (isset($this->_superAttr[$attrCode][$option['value']])) {
                        $newLabel = $this->_superAttr[$attrCode][$option['value']];
                        $values[$option['option_id']] = array();
                        $values[$option['option_id']][0] = $option['value'];
                        foreach ($this->_getAllStoresIds() as $storeId) {
                            $values[$option['option_id']][$storeId] = $newLabel;
                        }
                    }
                }

                if (count($values)) {
                    $counter    = 0;
                    $values     = array_chunk($values, 100, true);

                    foreach ($values as $key => $value) {
                        $data['option']['value'] = $value;
                        $attrResourceModel->addData($data);
                        try {
                            $attrResourceModel->save();
                            if ($this->getConsoleLog()) {echo "\t[batch-" . ($key + 1) . '] saved: ' . count($value) . ' option values' . PHP_EOL;}
                            $counter += count($value);
                        } catch (Exception $e) {
                            Mage::helper('brandercml/import')->log($e->getMessage());
                            if ($this->getConsoleLog()) {echo '[ERROR]' . $e->getMessage() . PHP_EOL;}
                        }
                        $attrResourceModel->save();
                    }
                }
            }
            if ($this->getConsoleLog()) {
                if (isset($counter)) printf("\t" . 'Total saved OK:%s' . PHP_EOL, $counter);
                else echo "\t" . 'Attribute options is empty.' . PHP_EOL;
                echo '[END] Update configurable attributes option value' . PHP_EOL . PHP_EOL;
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

    protected function _getConfigurableProductIsInStock(&$simpleSkus)
    {
        if (is_array($simpleSkus)) {
            foreach ($simpleSkus as $simpleSku) {
                if (isset($this->_products[$simpleSku])) {
                    $simple = $this->_products[$simpleSku];
                    if (isset($simple['is_in_stock']) && $simple['is_in_stock']) {
                        return 1;
                    }
                }
            }
        }
        return 0;
    }

    protected function _prepareConfigurableAttributes($simpleSkus)
    {
        $attributes = explode(',', $this->getConfigurableAttributes());
        $cache = array();
        if (is_array($simpleSkus)) {
            foreach ($attributes as $attr) {
                foreach ($simpleSkus as $simpleSku) {
                    if (isset($this->_products[$simpleSku])) {
                        $simple = $this->_products[$simpleSku];
                        if (isset($simple[$attr])) {
                            $cache[] = $attr;
                        }
                    }
                }
            }
        }

        if (count($cache)) {
            return implode(',', array_unique($cache));
        }

        return false;
    }

    protected function _prepareSuperAttributesPricing(&$simples, &$confPrice)
    {
        if (count($simples) && ($options = $this->getSuperAttributePricingOption())) {
            $options = explode(',', $options);
            $resultPricing = array();
            foreach ($options as $option) {
                $option = trim($option);
                $attrPricing = $result = array();
                foreach ($simples as $simpleSku) {
                    $simpleData = $this->_products[$simpleSku];
                    if (isset($simpleData[$option])) {
                        $attribute = $simpleData[$option];
                        if ($simpleData['special_price'] > 0) {
                            $attrPricing[$attribute][] = $simpleData['special_price'];
                        }
                    }
                }
                foreach ($attrPricing as $attribute => $price) {
                    switch ((string) $this->getSuperAttributePricingOptionPriceRule()) {
                        case 'mid':
                            $maxPrice = max($price);
                            $minPrice = min($price);
                            $optionPrice = round(($maxPrice - $minPrice) / 2 + $minPrice);
                            break;
                        case 'max':
                            $optionPrice = max($price);
                            break;
                        case 'min': default:
                            $optionPrice = min($price);
                    }

                    // MIN mode option pricing
                    if ($this->getSuperAttributePricingOptionPriceDelta()
                        && ($this->getConfigurablePriceBySimplePrice() == 'min' || $this->getConfigurablePriceBySimpleSpecialPrice() == 'min')
                        && $confPrice instanceof Varien_Object
                    ) {
                        $currentConfPrice = $this->getConfigurablePriceBySimpleSpecialPrice() ? $confPrice->getSpecialPrice() : $confPrice->getBasePrice();
                        // calculate delta option price
                        $optionPrice = $optionPrice - $currentConfPrice;
                    }

                    // MID mode option pricing
                    if ($this->getSuperAttributePricingOptionPriceDelta()
                        && ($this->getConfigurablePriceBySimplePrice() == 'mid' || $this->getConfigurablePriceBySimpleSpecialPrice() == 'mid')
                        && $confPrice instanceof Varien_Object
                    ) {
                        $currentConfPrice = $this->getConfigurablePriceBySimpleSpecialPrice() ? $confPrice->getSpecialPrice() : $confPrice->getBasePrice();
                        // calculate delta option price
                        $optionPrice = $optionPrice - $currentConfPrice;
                    }

                    $result[] = $attribute . ':' . $optionPrice;
                }
                if (count($result)){
                    $resultPricing[] = $option . '::' . implode($result, ';');
                }
            }
            return count($resultPricing) ? implode($resultPricing, ',') : false;
        }
        return false;
    }

    public function processOffers()
    {
        if (!$xml = $this->getXml()) {
            return false;
        }

        $helper = Mage::helper('brandercml/import');

        if (($offers = $xml->getNode('ПакетПредложений/Предложения/Предложение')) && $offers->count()) {
            $counter = 0;
            foreach ($offers as $offer) {
                if (!$id = (string)$offer->{'Ид'}) {
                    continue;
                }

                if ($this->_confInOffers && strpos($id, '#')) {
                    list($configurableId, $dataId) = explode('#', $id);
                    $sku = $this->getSkuById($configurableId);
                } else {
                    $sku = $this->getSkuById($id);
                }

                if (!$sku) {
                    continue;
                }

                $item = array(
                    'sku'           => $sku,
                    'is_in_stock'   => (string)$offer->{'ЕстьВНаличии'} == 'true' || Mage::getStoreConfig(self::IS_IN_STOCK) ? '1' : '0',
                    'type'          => 'simple',
                );

                if ($qty = (int)$offer->{'Количество'}) {
                    $item['qty'] = $qty;
                }
                if ($price = $offer->xpath('Цены/Цена')) {
                    $price = current($price);
                    if ($price = (string)$price->{'ЦенаЗаЕдиницу'}) {
                        $item['price'] = $price;
                    }
                }

                $this->_offers[] = $item;

                if ($this->_limit > 0 && ++$counter >= $this->_limit) {
                    break;
                }
            }

            if (!$this->getIsDebugMode()) {
                $this->getMagmi()->updateProducts($this->_offers);
            } else {
                if ($this->getConsoleLog()) {
                    Zend_Debug::dump($this->_offers);
                }
            }
            if ($this->getConsoleLog()) {
                echo '--- Catalog Updater ---' . PHP_EOL;
                echo 'Total Offers: '. count($this->_offers) . PHP_EOL;
            }
        }
    }

    public function getCategory($id = null)
    {
        if (array_key_exists($id, $this->_categories)) {
            return $this->_categories[$id];
        }

        if (!$xml = $this->getXml()) {
            return false;
        }

        $categoryNode = $xml->getNode('Классификатор/Группы')->xpath('.//Ид[.="'. $id .'"]');
        if (count($categoryNode)) {
            $name = array();
            $this->_parseCategoryNode(current($categoryNode), $name);
            $this->_categories[$id] = Mage::helper('brandercml/import')->prepareCategoryName($name);
            return $this->_categories[$id];
        }

        return null;
    }

    public function getSkuById($id)
    {
        if (array_key_exists($id, $this->_skus)) {
            return $this->_skus[$id];
        }
        return null;
    }

    protected function _parseCategoryNode($node, &$name)
    {
        $parent = $node->getParent();

        switch ($parent->getName()) {
            case 'Группа':
                if ($_name = (string)$parent->{'Наименование'}) {
                    $name[] = $_name;
                }
                $this->_parseCategoryNode($parent, $name);
                break;
            case 'Группы':
                $this->_parseCategoryNode($parent, $name);
                break;
        }
    }

    public function clearAttributes()
    {
        $this->setClearAttributes(true);
        return $this;
    }

    protected function _getSkuIncrementId($sku)
    {
        if (!isset($this->_skusInc[$sku])) {
            $this->_skusInc[$sku] = 0;
        }
        return $this->_skusInc[$sku]++;
    }

    protected function _skuIncrementId($sku)
    {
        $inc = $this->_getSkuIncrementId($sku);
        return $inc === 0 ? $sku : $sku . '-' . $inc;
    }

    public function clearCatalog($type = null)
    {
        $collection = Mage::getModel('catalog/product')->getCollection();
        $products = array();
        foreach ($collection as $product) {
            if ($sku = $product->getSku()) {
                if ($type && $type != $product->getTypeId()) {
                    continue;
                }
                $products[] = array(
                    'sku'           => $sku,
                    'magmi:delete'  => '1',
                    'type'          => ''
                );
            }
        }
        $_count = count($products);
        if ($this->getConsoleLog()) {
            echo '--- Catalog Cleaner ---' . PHP_EOL;
        }
        if ($_count) {
            $this->getMagmi()->deleteProducts($products);
            if ($this->getConsoleLog()) {
                echo 'Deleted Products: '. $_count . PHP_EOL . PHP_EOL;
            }
            unset($products);
        } else {
            if ($this->getConsoleLog()) {
                echo 'Catalog is empty.' . PHP_EOL . PHP_EOL;
            }
        }
        return $this;
    }

    public function clearAllSimpleProductsInConfigurable()
    {
        $collection = Mage::getModel('catalog/product')->getCollection();
        $products = array();
        $counter = 0;
        foreach ($collection as $product) {
            if ($product->getTypeId() == 'configurable') {
                $simples = $product->getTypeInstance()->getUsedProducts();
                foreach ($simples as $simple) {
                    if ($simple->getSku()) {
                        $products[] = array(
                            'sku'           => $simple->getSku(),
                            'magmi:delete'  => '1',
                            'type'          => 'simple'
                        );
                    }
                }
                $counter++;
            }
        }
        $_count = count($products);
        if ($this->getConsoleLog()) {
            echo '--- Simple Products Cleaner ---' . PHP_EOL;
        }
        if ($_count) {
            $this->getMagmi()->deleteProducts($products);
            if ($this->getConsoleLog()) {
                echo 'Deleted Products: '. $_count . PHP_EOL . PHP_EOL;
            }
            unset($products);
        } else {
            if ($this->getConsoleLog()) {
                echo 'Catalog is empty.' . PHP_EOL . PHP_EOL;
            }
        }
        return $this;
    }

    public function setSuperAttributePricingOptionToZero()
    {
        $collection = Mage::getModel('catalog/product')->getCollection();
        $products = array();
        $counter = 0;
        foreach ($collection as $product) {
            if ($product->getTypeId() == 'configurable') {
                $simples = $product->getTypeInstance()->getUsedProducts();
                foreach ($simples as $simple) {
                    if ($simple->getSku()) {
                        $products[] = array(
                            'sku'           => $simple->getSku(),
                            'type'          => 'simple'
                        );
                    }
                }
                $counter++;
            }
        }
        $_count = count($products);
        if ($this->getConsoleLog()) {
            echo '--- Simple Products Cleaner ---' . PHP_EOL;
        }
        if ($_count) {
            $this->getMagmi()->deleteProducts($products);
            if ($this->getConsoleLog()) {
                echo 'Deleted Products: '. $_count . PHP_EOL . PHP_EOL;
            }
            unset($products);
        } else {
            if ($this->getConsoleLog()) {
                echo 'Catalog is empty.' . PHP_EOL . PHP_EOL;
            }
        }
        return $this;
    }

    public function clearAllSimpleProductsIsNotInConfigurable()
    {
        $collection = Mage::getModel('catalog/product')->getCollection();
        $smplsInConfig = array();
        $smplsToDelete = array();
        $counter = 0;

        foreach ($collection as $product) {
            if ($product->getTypeId() == 'configurable') {
                $simples = $product->getTypeInstance()->getUsedProducts();
                foreach ($simples as $simple) {
                    if ($simple->getSku()) {
                        $smplsInConfig[] = $simple->getSku();
                    }
                }
            }
        }

        foreach ($collection as $product) {
            if ($product->getTypeId() == 'simple') {
                if (($sku = $product->getSku()) && !in_array($sku, $smplsInConfig)) {
                    $smplsToDelete[] = array(
                        'sku'           => $sku,
                        'magmi:delete'  => '1',
                        'type'          => 'simple'
                    );
                    $counter++;
                }
            }
        }

        $_count = count($smplsToDelete);
        if ($this->getConsoleLog()) {
            echo '--- Simple Products Cleaner ---' . PHP_EOL;
        }
        if ($_count) {
            $this->getMagmi()->deleteProducts($smplsToDelete);
            if ($this->getConsoleLog()) {
                echo 'Deleted Products: '. $_count . PHP_EOL . PHP_EOL;
            }
            unset($smplsToDelete);
        } else {
            if ($this->getConsoleLog()) {
                echo 'Catalog is empty.' . PHP_EOL . PHP_EOL;
            }
        }
        return $this;
    }

    public function setZeroPriceByProductType($type = null)
    {
        $collection = Mage::getModel('catalog/product')->getCollection();
        $products = array();
        foreach ($collection as $product) {
            if ($sku = $product->getSku()) {
                if ($type && $type != $product->getTypeId()) {
                    continue;
                }
                if ((int) $product->getPrice() == 0) {
                    $products[] = array(
                        'sku'           => $sku,
                        'type'          => $product->getTypeId(),
                        'min_qty'       => '1',
                        'is_in_stock'   => '0',
                        'price'         => '0'
                    );
                }
            }
        }
        $_count = count($products);
        if ($this->getConsoleLog()) {
            echo '--- Catalog Updater ---' . PHP_EOL;
        }
        if ($_count) {
            $this->getMagmi()->updateProducts($products);
            if ($this->getConsoleLog()) {
                echo 'Update Products Price: '. $_count . PHP_EOL . PHP_EOL;
            }
            unset($products);
        } else {
            if ($this->getConsoleLog()) {
                echo 'Not prepared products for update' . PHP_EOL . PHP_EOL;
            }
        }
        return $this;
    }

    public function clearConfigurableAttributeOptions($attrCodes)
    {
        if (is_array($attrCodes)) {
            if ($this->getConsoleLog()) {echo '[START] Configurable Attribute Options Cleaner' . PHP_EOL;}
            $setup = Mage::getModel('eav/entity_setup', 'core_setup');
            foreach ($attrCodes as $attrCode) {
                $attrId = Mage::getModel('eav/entity_attribute')->load($attrCode, 'attribute_code')->getId();
                $allOptions = Mage::getResourceModel('eav/entity_attribute_option_collection')
                    ->setAttributeFilter($attrId)
                    ->setStoreFilter(0, false)
                    ->getData();
                if (is_array($allOptions) && count($allOptions)) {
                    $options = array();
                    foreach ($allOptions as $option) {
                        $optionId = $option['option_id'];
                        $options['delete'][$optionId] = true;
                        $options['value'][$optionId] = true;
                    }
                    try {
                        $setup->addAttributeOption($options);
                        printf("\t" . 'Options list ('. $attrCode .') deleted OK:%s/%s' . PHP_EOL, count($options['value']), count($allOptions));
                    } catch (Exception $e) {}
                } else {
                    echo "\t" . 'Options list ('. $attrCode .') is empty.' . PHP_EOL;
                }
            }
            if ($this->getConsoleLog()) {echo '[END] Configurable Attribute Options Cleaner' . PHP_EOL . PHP_EOL;}
        }
        return $this;
    }

    public function updateSimpleProductNameByConfigurable()
    {
        $collection = Mage::getModel('catalog/product')->getCollection();
        $products = array();
        foreach ($collection as $product) {
            if ($product->getTypeId() == 'configurable') {
                $product = $product->load($product->getId());
                
                $childProducts = $product->getTypeInstance()->getUsedProducts();
                foreach ($childProducts as $child) {
                    $products[] = array(
                        'sku'   => $child->getSku(),
                        'type'  => $child->getTypeId(),
                        'name'  => $product->getName()
                    );
                }
            }
        }
        $_count = count($products);
        if ($this->getConsoleLog()) {
            echo '--- Simple Product Rename ---' . PHP_EOL;
        }
        if ($_count) {
            $this->getMagmi()->updateProducts($products);
            if ($this->getConsoleLog()) {
                echo 'Update Simple Product Name: '. $_count . PHP_EOL . PHP_EOL;
            }
            unset($products);
        } else {
            if ($this->getConsoleLog()) {
                echo 'Not prepared products for update' . PHP_EOL . PHP_EOL;
            }
        }
        return $this;
    }
}