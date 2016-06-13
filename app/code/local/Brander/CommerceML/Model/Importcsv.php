<?php

class Brander_CommerceML_Model_Importcsv extends Varien_Object
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

    protected $_importItems;
    protected $_confCollection;

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
                    ->getAssocData($filePath, $this->getData(), $this->getConfigurableAttributes());
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
        $this->getHelper()->logMessage('');
        $this->getHelper()->logMessage('--- IMPORT STARTS, ' . ' time: ' . date('Y/m/d H:i:s') . ' ---');
        $this->getHelper()->logMessage('');


        $helper = Mage::helper('brandercml/import');
        Mage::dispatchEvent('brandercml_import_start', array('status' => 'start', 'data' => $this));

        $this->_importItems = $this->_loadImportSourceFile();
        if ($this->_importItems !== false)
        {
            //$this->setImportData($importData);
            $this->getHelper()->logMessage('');
            $confirmationDelete = 'N';

            if ($this->getDeleteOldSimples()) {
                $message = "Are you sure you want to delete simples [Y / N]";
                print $message;
                flush();
                ob_flush();
                $confirmationDelete = trim( fgets( STDIN ) );
                if ($confirmationDelete == 'Y' || $confirmationDelete == 'y') {
                    // получаем список симл товаров, которые должны быть удалены, т.к. они не попали в выборку для импорта
                    if ($this->getConsoleLog()) {
                        $this->getHelper()->logMessage('--- Prepare SKUs for delete');
                    }
                    $skusProductForDelete = $this->_getSkusForDelete();
                }
            }

            // строим структуру конфигурируемых товаров и создаем новые настраиваемые товары. они будут в статусе отключено
            // не формировать не желательно, т.к. при разброске симплов может возникнуть ошибка
            if ($this->getConsoleLog()) {
                $this->getHelper()->logMessage('--- Start create configurable structure, time: ' . date('Y/m/d H:i:s') . ' ---');
            }
            $this->_getConfigurableItemsData();

            if ($this->getConsoleLog()) {
                $this->getHelper()->logMessage('--- New configurables products creation COMPLETE, time: '. date('Y/m/d H:i:s') . ' ---');
                $this->getHelper()->logMessage('');
            }

            //создаем новые симплы или обновляем существующие
            if ($this->getConsoleLog()) {
                $this->getHelper()->logMessage('--- Start create/update simples products, time: ' . date('Y/m/d H:i:s') . ' ---');
            }

            $_count = $this->_processProductsSCV();

            if ($this->getConsoleLog() && $this->getImportSimpleItems()) {
                $date = date('Y/m/d H:i:s');
                $this->getHelper()->logMessage('--- simples create or update COMPLETE: ' . $_count . ' items, time: ' . date('Y/m/d H:i:s') . ' ---');
                $this->getHelper()->logMessage('');
            }
            elseif ($this->getImportSimpleItems()) {
                $this->getHelper()->logMessage('--- simples create DISABLED by run options ---');
            }

            if ($this->getDeleteOldSimples() && ($confirmationDelete == 'Y' || $confirmationDelete == 'y')) {
                $_count = count($skusProductForDelete);
                if ($this->getConsoleLog()) {
                    $this->getHelper()->logMessage('--- Simple Products Prepare for delete: ' . $_count . ' ---');
                }
                //удаляем симпл товары, список которые мы сформировали ранее
                $this->_precessMagmiDelete($skusProductForDelete);
                if ($this->getConsoleLog()) {
                    $this->getHelper()->logMessage('--- Unused simples delete COMPLETE at ' . date('Y/m/d H:i:s') . ' ---');
                }
            }

            // настраиваем конфигурируемые товары по параметрам симплов
            if ($this->getProceedUpdateConfigurable()) {
                if ($this->getConsoleLog()) {
                    $this->getHelper()->logMessage('--- starts adding simples to configurables options at ' . date('Y/m/d H:i:s') . ' ---');

                }
                $this->_processConfigurable();
                if ($this->getConsoleLog()) {
                    $this->getHelper()->logMessage('--- adding simples to configurables options COMPLETE ---');
                    $this->getHelper()->logMessage('');
                }
            }

            $this->setVesPositions();

            $this->getHelper()->logMessage('');
            $this->getHelper()->logMessage('--- IMPORT COMPLETE, time ' . date('Y/m/d H:i:s') . ' ---');
            $this->getHelper()->logMessage('');
            $status = 'complete';
        }
        else {
            $this->getHelper()->logMessage('--- please, check Import file, can`t read data ---');
            $status = '!!! check import file !!!';
        }

        Mage::dispatchEvent('brandercml_import_end', array('status' => 'start', 'data' => $this));
        return $status;
    }



    protected function _getAvaibleProductsBeforeImport()
    {

        // создаем колекцию симпл товаров, до обновления ассортимента id => sku
        // удаление товаров
        $simpleCollection = Mage::getResourceModel('catalog/product_collection')
                            ->addAttributeToFilter('type_id', 'simple');

        $beforeUpdateProducts = array();
        foreach ($simpleCollection as $product) {
            $beforeUpdateProducts[$product->getSku()] = 1;
        }

        return $beforeUpdateProducts;
    }

    protected function _getConfigurableItemsData($configurable = array(), $allImportedConfigurable = array(), $extremalItemCount = '0')
    {

        $confAttrib = explode(',', $this->getConfigurableAttributes());
/*
 * This block moved to File/Csv.php fo better speed
 *
 */

/*        foreach ($this->_importItems['simples'] as $product) {

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

            $allImportedConfigurable[$confSku[0]]['sku'] = $confSku[0];
            $allImportedConfigurable[$confSku[0]]['name'] = $product['name'];


        }*/

//$uniqAllSkus[$confSku[0]] = $confSku[0];

        if ($this->getConfStructureStat()) {
            foreach ($configurable as $confSku => $confProduct) {
                $raitingConfs[$confSku] = count($confProduct['items']);
            }

            arsort($raitingConfs);
            if ($this->getConfStructureStatItemNumber()) {
                $raitingConfs = array_slice($raitingConfs, 0, $this->getConfStructureStatItemNumber());
            }
            $i=1;
            foreach ($raitingConfs as $raitingConfName => $raitingConfCount) {
                $str = $i . '. simples number: ' . $raitingConfCount . ' SKU:' . $raitingConfName . PHP_EOL;
                mage::log($str, null, 'AI-configurable-stat.log');
                $i++;
            }
        }


/*        $this->setAllImportedConfigarableSkus($uniqAllSkus);
        unset($uniqAllSkus);*/

        if ($this->getLimitSimplesMaxParam() || $this->getLimitConfigurableWithMaxParam()) {
            foreach ($configurable as $sku => $confItem) {

                $thisConfMaxParamsQty = $this->getMaxQtyConfigurableAttributes();
                $extremalConfigurableParams = $this->getMaxQtyFastConfigurableAttributes();

                /*
                 * adding maximum params for limited items
                 * NumberSimplesWithMaxParam - limit number of simples in conf, when use maximum Configurable Attributes
                 * NumberConfigurableWithMaxParam - limit of confs with use maximum Configurable Attributes
                 */

                if ($this->getLimitSimplesMaxParam()
                    && ($this->getLimitConfigurableWithMaxParam() == false
                        || ($this->getLimitConfigurableWithMaxParam() == true
                            && $extremalItemCount < $this->getNumberConfigurableWithMaxParam()))
                ) {

                    if (count($confItem['items']) <= $this->getNumberSimplesWithMaxParam()) {
                        $confAttribCount = count($confItem['confparams']);
                        if ($confAttribCount > $thisConfMaxParamsQty
                            || $confAttribCount == $extremalConfigurableParams
                        ) {
                            if ($this->getExtremeItemsNameLog()) {
                                Mage::log($confItem['items'][0]['name'], null, 'extreme_items_register.log');
                            }
                            $extremalItemCount++;
                        }
                        $thisConfMaxParamsQty = $extremalConfigurableParams;
                    }
                }


                if (count($confItem['confparams'])) {
                    $params = $confItem['confparams'];
                    unset($configurable[$sku]['confparams']);
                    $i = 0;

                    foreach ($confAttrib as $thisAtr) {
                        if (isset($params[$thisAtr]) && ($i < $thisConfMaxParamsQty)) {
                            $configurable[$sku]['confparams'][$thisAtr] = $thisAtr;
                            $i++;
                        }
                    }
                }
            }
            echo('4P = ' . $extremalItemCount);

            //$allConfigurable = array_unique($allConfigurable);
        }

        $this->_getSkusConfigurableNeedToCreate();

        if ($this->_importItems['newConfigurableProducts'] && $this->getProceedCreateConfigurable()) {
            $this->_processCreateNewConf();

            //$newConfSkusString =  implode (','.PHP_EOL, $this->getAllImportedConfigarableSkus());
            //Mage::log(PHP_EOL . 'new configurable products skus:' . PHP_EOL . $newConfSkusString, null, 'new_conf.log');
            //Mage::log('need to edit every product for enabled', null, 'new_conf.log');
        }

        //$this->setConfigurableStructure($configurable);
        return true;
    }

    protected function _getSkusConfigurableNeedToCreate()
    {

        $confCollection = Mage::getResourceModel('catalog/product_collection')
	        ->addAttributeToFilter('type_id', 'configurable');

        $configurableProducts = null;
        foreach ($confCollection as $product) {
            $configurableProducts[$product->getId()] = $product->getSku();
        }

        $this->setCurrentConfigurableIds($configurableProducts);

        foreach ($this->_importItems['allImportedConfigurable'] as $confSku) {
            if (!in_array($confSku['sku'], $configurableProducts)) {
                $this->_importItems['newConfigurableProducts'][] = $confSku;
            }
        }
        return ;
    }

    private function _processCreateNewConf()
    {

        $date = date('Y/m/d H:i:s');
        $this->getHelper()->logMessage('--- create configurable items start ' . ' at: ' . $date . ' ---');
        $counter = 0;

        foreach ($this->_importItems['newConfigurableProducts'] as $product) {
            $confCreation = array();
            $confCreation[] = array (
                'product_name'              => 'Новый товар. Артикул ' . $product['name'],
                'name'                      => 'Новый товар. Артикул ' . $product['name'],
                // т.к. магми не удаляет конф атрибуты а только добавляет, то задавать их придется при распределении товаров
                'configurable_attributes'   => 'gorod',//$this->getConfigurableAttributes(),
                'status'                    => '2',
                'is_in_stock'               => '1',
                'sku'                       => $product['sku'],
                'type'                      => 'configurable',
                'type_id'                   => 'configurable',
                'commerceml_id'             => $product['sku'],
                'price'                     => '0',
                'tax_class_id'              => '0'
            );

            $counter++;

        	$this->getMagmi()->importProducts($confCreation);

            unset($confCreation);
            $this->getHelper()->logMessage('--- created ' . $counter . ' configurable item(s),  time: ' . $date . ' ---');
        }


            $date = date('Y/m/d H:i:s');
        $this->getHelper()->logMessage('--- create COMPLETE,  time: ' . $date . ' ---');
        $this->getHelper()->logMessage('');

        return;
    }

    protected  function _processConfigurable($disabledConfItems = array())
    {

        $type = $this->getConfigurablePriceBySimpleSpecialPrice();
        $oldConfSkus = $this->getCurrentConfigurableIds();

        $total = count($this->_importItems['configurable']);

        $counter = 0;

        $confDisCollection = Mage::getResourceModel('catalog/product_collection')
	        ->addAttributeToFilter('type_id', 'configurable')
	        ->addAttributeToFilter('status', '2');

        foreach ($confDisCollection as $confDisItem) {
            $disabledConfItems[] = $confDisItem->getSku();
        }

        $disabledConfItemsLog = implode(','. PHP_EOL, $disabledConfItems);
        Mage::log('disabled configurable SKUs at: ' . date('Y/m/d H:i:s'), null, 'AI-disabled-configurable.log');
        Mage::log($disabledConfItemsLog, null, 'AI-disabled-configurable.log');
        $this->getHelper()->logMessage('--- disabled configurable SKUs saved to AI-disabled-configurable.log ---');

        foreach ($this->_importItems['configurable'] as $confSku => $productsInfo) {
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

            if (in_array($confSku, $disabledConfItems)) {
                $simplesAttach = '';
            }

            // удалять конф аттрибуты, т.к. их можно через magmi только добавлять, а удалять - нет
                if ($confId) {
                    $resource = Mage::getSingleton('core/resource');
                    $writeConnection = $resource->getConnection('core_write');
                    $table = $resource->getTableName('catalog_product_super_attribute');
                    $query = "DELETE FROM {$table} WHERE product_id = {$confId}";

                    $writeConnection->query($query);  // временно отключить если не нужно удалять параметры
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
                'category_reset'            => '0',
            );

            $counter++;

            $this->getMagmi()->updateProducts($products);

            unset($products);
            $this->getHelper()->logMessage('--- ' . $counter . '/' . $total . '  configure SKU: ' . $confSku . ' params: (' . $confAttrThisConfItem . ') COMPLETE TIME: ' . date('Y/m/d H:i:s') . ' ---');

        }

        $this->getHelper()->logMessage('--- configure ' . $counter . ' configurable products COMPLETE, time: ' . date('Y/m/d H:i:s') . ' ---');
        $this->getHelper()->logMessage('');
        return;
    }

    protected function _getSkusForDelete($csvProductsSkus = array(), $productsIdForDisable = array())
    {

        // TODO:: need to optimize process
        foreach ($this->_importItems['simples'] as $inUpdateProduct)
        {
            $csvProductsSkus[] =  $inUpdateProduct['sku'];
        }

        $avibleSimples = $this->_getAvaibleProductsBeforeImport();
        foreach ($avibleSimples as $item) {

            if (isset($csvProductsSkus[$item])) {
                unset($csvProductsSkus[$item]);
            }
            else {
                $productsIdForDisable[] = array('sku' => $item, 'magmi:delete' => '1');
            }
        }

        return $productsIdForDisable;
    }

    public function _processProductsSCV()
    {
        if ($this->getImportSimpleItems()) {
            $i = 0; $k = 0; $_importProducts = array();
            $count = count($this->_importItems['simples']);

            foreach ($this->_importItems['simples'] as $_product) {
                $_importProducts[] =  $_product;
                $i++; $k++;
                if ($i == 1000) {
                    $this->getMagmi()->importProducts($_importProducts);
                    $i = 0; $_importProducts = array();
                    $date = date('Y/m/d H:i:s');
                    $this->getHelper()->logMessage($k . '/' . $count . ' time: ' . $date);
                }
            }

            //$this->getMagmi()->importProducts($this->_importItems['simples']);
            $this->getMagmi()->importProducts($_importProducts);

            $_importProducts = null;
            $this->_importItems['simples'] = null;

            $date = date('Y/m/d H:i:s');
            $this->getHelper()->logMessage($k . '/' . $count . ' time: ' . $date);
            return $count;
        }

        return 0;
    }


    protected function _precessMagmiDelete($deleteProducts)
    {

        //$this->getMagmi()->deleteProducts($deleteProducts);

        $_count = count($deleteProducts);
        if ($this->getConsoleLog()) {
            $this->getHelper()->logMessage('--- unused simples delete COMPLETE: ' . $_count . ' ---');
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

    protected function getHelper()
    {
        return Mage::helper('autoimport/data');
    }

    protected function setVesPositions()
    {
        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'ves');

        /** @var $attribute Mage_Eav_Model_Entity_Attribute */
        $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attribute->getId())
            ->addFieldToFilter('sort_order', 0)
            ->setStoreFilter(0, false)
        ;

        foreach ($valuesCollection as $valueAttr) {
            $val = $valueAttr->getValue();
            $option = $valueAttr->getOptionId();

            if ($val > 0) {
                $valueAttr->setSortOrder($val * 100);
                $valueAttr->save();


                $resource = Mage::getSingleton('core/resource');
                $writeConnection = $resource->getConnection('core_write');
                $table = $resource->getTableName('eav_attribute_option_value');

                $query = "DELETE FROM {$table} WHERE (`option_id` = {$option}) AND (`store_id` = 11);";
                $writeConnection->query($query);
                $query = "DELETE FROM {$table} WHERE (`option_id` = {$option}) AND (`store_id` = 12);";
                $writeConnection->query($query);

                $query
                    = "INSERT INTO {$table} (`value_id`, `option_id`, `store_id`, `value`) VALUES (NULL, {$option}, '11', {$val});";
                $writeConnection->query($query);
                $query
                    = "INSERT INTO {$table} (`value_id`, `option_id`, `store_id`, `value`) VALUES (NULL, {$option}, '12', {$val});";
                $writeConnection->query($query);

            }
        }
    }


}
