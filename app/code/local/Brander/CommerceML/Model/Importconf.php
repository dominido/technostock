<?php

class Brander_CommerceML_Model_Importconf extends Varien_Object
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

// MY CODE
    protected function _loadImportSourceFile($delimiter = ';', $enclosure = '*')
    {
        $helper = Mage::helper('brandercml/import');
        try {

            $filePath = $helper->getImportFilePath($this->getSourceFile());

            if (is_file($filePath) && is_readable($filePath)) {

                return Mage::getModel('brandercml/file_csv')->setDelimiter($delimiter)
                    ->setEnclosure($enclosure)
                    ->getAssocData($filePath);
            } else {
                $helper->log('File "'. $filePath .'" does not exist or not readable.');
                return false;
            }


        } catch (Exception $e) {
            $helper->log($e->getMessage());
        }
        return false;

    }


    public function run()
    {
        date_default_timezone_set('Europe/Kiev');
        $date = date('Y/m/d H:i:s');
        echo 'import start, ' . ' time: ' . $date . PHP_EOL;

        $helper = Mage::helper('brandercml/import');
        Mage::dispatchEvent('brandercml_import_start', array('status' => 'start', 'data' => $this));




        if ($this->setImportData($this->_loadImportSourceFile())) {

            if ($this->getConsoleLog()) {
                echo '--- prepare data from import file  ---' . PHP_EOL;
            }
            // получчаем данные из CSV фала со всеми симплами и параметрами

            // получаем список симл товаров, которые должны быть удалены, т.к. они не попали в выборку для импорта
/*            if ($this->getConsoleLog()) {
                echo '--- Prepare Skus for delete' . PHP_EOL;
            }
            $skusProductForDelete = $this->_getSkusForDelete($allSimpleProductBeforeUpdateSkus);
            $_count = count($skusProductForDelete);
            if ($this->getConsoleLog()) {
                echo '--- Simple Products Prepare for delete: ' . $_count . ' ---' . PHP_EOL;
            }*/

            // строим структуру конфигурируемых товаров и создаем новые настраиваемые товары. они будут в статусе отключено
            // не формировать не желательно, т.к. при разброске симплов может возникнуть ошибка
            if ($this->getConsoleLog()) {
                $date = date('Y/m/d H:i:s');
                echo '--- Start create configurable structure, time: ' . $date . ' ---' . PHP_EOL;
            }
            $this->_getConfigurableItemsData();

            if ($this->getConsoleLog()) {
                echo '--- New configurable products created, check log file new_conf.log ---' . PHP_EOL;
                echo '--- This products needs for edit ---' . PHP_EOL;
            }

            //создаем новые симплы или обновляем существующие
            $_count = $this->_processProductsSCV();
            if ($this->getConsoleLog()) {
                $date = date('Y/m/d H:i:s');
                echo '--- simples create or update COMPLETE:' . $_count . ' , time: ' . $date . ' ---' . PHP_EOL;
            }

            //удаляем симпл товары, список которые мы сформировали ранее
/*            $this->_precessMagmiDelete($skusProductForDelete);
            if ($this->getConsoleLog()) {
                $date = date('Y/m/d H:i:s');
                echo '--- Unused simples delete COMPLETE at ' . $date . ' ---' . PHP_EOL;
            }*/


            // настраиваем конфигурируемые товары по параметрам симплов
            if ($this->getConsoleLog()) {
                $date = date('Y/m/d H:i:s');
                echo '--- starts adding simples to configurables options at ' . $date . ' ---' . PHP_EOL;
            }
            $this->_processConfigurable();
            if ($this->getConsoleLog()) {
                echo '--- adding simples to configurables options COMPLETE ---' . PHP_EOL;
            }

        }

        Mage::dispatchEvent('brandercml_import_end', array('status' => 'start', 'data' => $this));
        return $this;
    }



    protected function _getAvaibleProductsBeforeImport(){

        // создаем колекцию симпл товаров, до обновления ассортимента id => sku
        $products_c = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('type_id', array('eq' => 'simple'));

        $beforeUpdateProducts = Array();
        foreach ($products_c as $product) {
            $beforeUpdateProducts[$product->getId()] = $product->getSku();
        }

        return $beforeUpdateProducts;
    }

    protected function _getConfigurableItemsData($configurable = array(), $allConfigurable = array()) {

        $confAttrib = explode(',', $this->getConfigurableAttributes());

        foreach ($this->getImportData() as $product) {
            $confSku = explode('#', $product['article_1c']);

            $configurable[$confSku[0]]['items'][] = array (
                'sku'               => $product['sku'],
                'price'             => $this->_prepareAttributeValue($product['price']),
                'special_price'     => $this->_prepareAttributeValue($product['special_price']),
                'name'              => $product['name'],
            );

            foreach ($confAttrib as $atr) {
                if ($product[$atr]) {
                    $configurable[$confSku[0]]['confparams'][$atr] = $atr;
                }
            }

            $allConfigurable[$confSku[0]]['sku'] = $confSku[0];
            $allConfigurable[$confSku[0]]['name'] = $product['name'];

            $uniqAllSkus[$confSku[0]] = $confSku[0];
        }

        $this->setNewConfigarableSkus($uniqAllSkus);
        unset($uniqAllSkus);

        foreach ($configurable as $sku => $confItem) {
            if (count($confItem['confparams']) > $this->getMaxQtyConfigurableAttributes()) {
                $params = $confItem['confparams'];
                unset($configurable[$sku]['confparams']);
                $i = 0;

                foreach ($confAttrib as $thisAtr) {
                    if (isset($params[$thisAtr]) && ($i < $this->getMaxQtyConfigurableAttributes())) {
                        $configurable[$sku]['confparams'][$thisAtr] = $thisAtr;
                        $i++;
                    }
                }

            }
        }

        //$allConfigurable = array_unique($allConfigurable);

        $idsToCreateConf = $this->_getSkusConfigurableNeedToCreate($allConfigurable);

        if ($idsToCreateConf) {
            $this->_processCreateNewConf($idsToCreateConf);

            $newConfSkusString =  implode (','.PHP_EOL, $this->getNewConfigarableSkus());
            Mage::log(PHP_EOL . 'new configurable products skus:' . PHP_EOL . $newConfSkusString, null, 'new_conf.log');
            Mage::log('need to edit every product for enabled', null, 'new_conf.log');
        }

        $this->setConfigurableStructure($configurable);
        return true;
    }

    protected function _getSkusConfigurableNeedToCreate($allConfigurable, $newConfigurableProducts = array()) {
        $confCollection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('type_id', array('eq' => 'configurable'));



        $configurableProducts = null;
        foreach ($confCollection as $product) {
            $configurableProducts[$product->getId()] = $product->getSku();
            if ($product->getSku() == $product->getProductName()) {
                $disabledConfItems[] = $product->getSku();
            }
        }

        $this->setCurrentConfigurableIds($configurableProducts);
        $this->setDisabledConfItems($disabledConfItems);

        foreach ($allConfigurable as $confSku) {
            if (!in_array($confSku['sku'], $configurableProducts)) {
                $newConfigurableProducts[] = $confSku;
            }
        }

        return $newConfigurableProducts;
    }

    private function _processCreateNewConf ($idsToCreateConf) {

        $date = date('Y/m/d H:i:s');
        echo 'create configurable items start ' . ' at: ' . $date . PHP_EOL;
        $counter = 0;
        foreach ($idsToCreateConf as $product) {
            $confCreation = array();
            $confCreation[] = array (
                'product_name'              => 'Новый товар. Артикул ' . $product['name'],
                'name'                      => 'Новый товар. Артикул ' . $product['name'],
                // т.к. магми не удаляет конф атрибуты а только добавляет, то задавать их придется при распределении товаров
                'configurable_attributes'   => 'gorod',//$this->getConfigurableAttributes(),
                'is_in_stock'               => '1',
                'sku'                       => $product['sku'],
                'type'                      => 'configurable',
                'type_id'                   => 'configurable',
                'commerceml_id'             => $product['sku'],
                'price'                     => '0',
                'special_price'             => null
            );

            $counter++;
            $this->getMagmi()->importProducts($confCreation);

            unset($newIdsConf);
            $date = date('Y/m/d H:i:s');
            echo 'was create' . $product . ' configurable item,  time: ' . $date . PHP_EOL;
        }

        echo 'create COMPLETE,  time: ' . $date . PHP_EOL;

        return;
    }

    protected  function _processConfigurable() {

        $type = $this->getConfigurablePriceBySimpleSpecialPrice();
        $newConfSkus = $this->getNewConfigarableSkus(); //here ids ))
        $oldConfSkus = $this->getCurrentConfigurableIds();
        $disabledConfItems = $this->getDisabledConfItems();

        $total = count($this->getConfigurableStructure());
        $counter = 0;

        foreach ($this->getConfigurableStructure() as $confSku => $productsInfo) {
            $_thisProductSimplesSkus = array();
            $_thisProductSimplesPrice = array();
            $_thisProductSimplesSpecialPrice = array();
            $products = array();

            foreach ($productsInfo['items'] as $_items) {

                $_thisProductSimplesSkus[] = $_items['sku'];
                if ($_items['price']) {
                    $_thisProductSimplesPrice[] = $_items['price'];
                }
                if ($_items['special_price']) {
                    $_thisProductSimplesSpecialPrice[] = $_items['special_price'];
                }
            }

            $confAttrThisConfItem = implode(',', $productsInfo['confparams']);

            switch ($type) {
                case 'mid':
                    $_confPrice = intval(array_sum($_thisProductSimplesPrice)/count($_thisProductSimplesSkus));
                    $_confSpecPrice = intval(array_sum($_thisProductSimplesSpecialPrice)/count($_thisProductSimplesSpecialPrice));
                    break;
                case 'max':
                    $_confPrice = intval(max($_thisProductSimplesPrice));
                    $_confSpecPrice = intval(max($_thisProductSimplesSpecialPrice));
                    break;
                case 'min': default:
                $_confPrice = intval(min($_thisProductSimplesPrice));
                $_confSpecPrice = intval(min($_thisProductSimplesSpecialPrice));
                break;
            }


                $simplesAttach = implode(',', $_thisProductSimplesSkus);
                $confId = array_search($confSku, $oldConfSkus);

                if ($confId) {

                    $resource = Mage::getSingleton('core/resource');
                    $writeConnection = $resource->getConnection('core_write');
                    $table = $resource->getTableName('catalog_product_super_attribute');
                    $query = "DELETE FROM {$table} WHERE product_id = {$confId}";

                    $writeConnection->query($query);

            }

            $products[] = array (
                'sku'                       => $confSku,
                'configurable_attributes'   => $confAttrThisConfItem,
                'is_in_stock'               => '1',
                'type'                      => 'configurable',
                'type_id'                   => 'configurable',
                'simples_skus'              => $simplesAttach,
                'price'                     => $_confPrice,
                'special_price'             => $_confSpecPrice,
            );

            //if ($confSku == 'test SKU') {
            $this->getMagmi()->updateProducts($products);
            $counter++;
            echo ($counter . '/' . $total . '  final configure ' . $confSku . ' configurable products COMPLETE' . PHP_EOL);
            //}

        }

        $date = date('Y/m/d H:i:s');
        echo ('configure ' . $counter . ' configurable products COMPLETE, time: ' . $date . PHP_EOL);

        return;
    }

    protected function _getSkusForDelete($csvProductsSkus = array(), $productsIdForDisable = array()) {

        echo 'need to optimize process';
        foreach ($this->getImportData() as $inUpdateProduct)
        {
            $csvProductsSkus[] =  $inUpdateProduct['sku'];
        }

        foreach ($this->_getAvaibleProductsBeforeImport() as $id => $sku) {

            if (!in_array($sku, $csvProductsSkus)) {
                $productsIdForDisable[] = array('sku' => $sku, 'magmi:delete' => '1');
            }
        }

        return $productsIdForDisable;
    }

    public function _processProductsSCV(){


        //$helper     = Mage::helper('brandercml/import');
        //$mapper     = $this->getAttributeMapper();
        //$this->getMagmi()->importProducts($this->getImportData());

        return count($this->getImportData());
    }


    protected function _precessMagmiDelete($deleteProducts) {

        $this->getMagmi()->deleteProducts($deleteProducts);

        $_count = count($deleteProducts);
        if ($this->getConsoleLog()) {
            echo '--- unused simples delete COMPLETE: ' . $_count . ' ---' . PHP_EOL;
        }

        return true;
    }

    protected function _prepareAttributeValue($value)
    {
        return str_replace(array(' ', ' ', ','), array('', '', '.'), $value);
    }

    protected function _arrayUniqueArrayValues(&$arrayValue)
    {
        array_unique($arrayValue);

        return $arrayValue;
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

    public function getSkuById($id)
    {
        if (array_key_exists($id, $this->_skus)) {
            return $this->_skus[$id];
        }
        return null;
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