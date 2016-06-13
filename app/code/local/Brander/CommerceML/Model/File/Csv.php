<?php

class Brander_CommerceML_Model_File_Csv extends Varien_File_Csv
{
    protected $_allImportedConfigurable = array();
    protected $_configurable = array();

    public function getAssocData($file, $config, $confParams)
    {

        if (!file_exists($file)) {
            throw new Exception('File "'.$file.'" do not exists');
        }
        if (!is_readable($file)) {
            throw new Exception('File "'.$file.'" do not readable');
        }

        $data   = array();
        $header = null;
       	$i=0;
        $this->getHelper()->logMessage('--- Start prepare products data for import, Plz wait ...');
        setlocale(LC_ALL, "ru_RU.UTF-8");
        if (($handle = fopen($file, 'r')) !== false) {
            while (($row = fgetcsv($handle, $this->_lineLength, $this->_delimiter, $this->_enclosure, chr(167))) !== false) {
                $i++;
                $tmp = null;
                if(!$header) {
                    $header = $row;
                }
                else {
                    if (count($header) != count($row)) {
                        $row = $this->manualParseItemData($row);
                        if (count($header) != count($row)) {
                            $err_msg = 'строка ' . $i . ' содержит ошибки и не будет импортирована,' . PHP_EOL . implode (' | ', $row);
                            Mage::log($err_msg, null, 'import_exclude.log');
                            $this->getHelper()->logMessage($err_msg);
                        }
                    }

                    if (count($header) == count($row)) {

                        $newPreRow = array_combine($header, $row);
                        $newRowAllData = $this->proceedLastRow($newPreRow);

                        if ($this->testFiler($newRowAllData, $config) && $this->checkforZeroPrice($newRowAllData, $i)) {
                            //Mage::log('sku: '.$newRowAllData['sku'].' price: '.$newRowAllData['price'].' sp_price: '.$newRowAllData['special_price'].PHP_EOL, null, 'items.log');
                            $data['simples'][] = $newRowAllData;
                            $this->_getConfigurableItemsData($newRowAllData, $confParams);
                        }
                    }
                }

/* proceed log:
                if (is_int($i/10000)) {
                    echo $i . ' successful prepared items' . PHP_EOL;
                }
*/
            }

            fclose($handle);
        }

        $_count = count($data['simples']);
        $this->getHelper()->logMessage('--- Simple Products Prepare: ' . $_count . ' ---');
        $data['configurable'] = $this->_configurable;
        $data['allImportedConfigurable'] = $this->_allImportedConfigurable;
        return $data;
    }

    public function getData($file, $handleRow = false)
    {
        $data = array();
        if (!file_exists($file)) {
            throw new Exception('File "'.$file.'" do not exists');
        }

        setlocale(LC_ALL, "ru_RU.UTF-8");
        $fh = fopen($file, 'r');
        while ($rowData = fgetcsv($fh, $this->_lineLength, $this->_delimiter, $this->_enclosure, chr(167))) {
            $data[] = $rowData;
            if ($handleRow) {
                break;
            }
        }
        fclose($fh);
        return $data;
    }

    private function proceedLastRow($lastElement)
    {
        $helper = Mage::helper('brandercml/data');


        $lastElement['product_name']       = $lastElement['name'];

        $lastElement['type']               = 'simple';
        $lastElement['product_type_id']    = 'simple';
        $lastElement['type_id']            = 'simple';

        $lastElement['sku'] = preg_replace('/[^0-9a-z]/i', '', $lastElement['sku']);

        if (!$lastElement['description']) {
            $lastElement['description'] = '&nbsp;';
        }
        if (!$lastElement['short_description']) {
            $lastElement['short_description'] = '&nbsp;';
        }


        if ($lastElement['weight']) {
            $lastElement['weight'] = $helper->prepareAttributeValue($lastElement['weight']);
        }
        else {
            $lastElement['weight'] = null;
        }
        $lastElement['ves'] = $lastElement['weight'];
        $lastElement['cml_ves'] = $lastElement['weight'];


        if ($lastElement['proba']) {
            $lastElement['cml_proba'] = intval($lastElement['proba']);
        }
        if ($lastElement['metall']) {
            $lastElement['cml_metal'] = $lastElement['metall'];
        }


        if (!$lastElement['status']) {$lastElement['status'] = '1';}
        if (!$lastElement['visibility']) {$lastElement['visibility'] = '4';}
        //if (!$importProduct['tax_class_id']) {
        $lastElement['tax_class_id'] = '0';
        //}

        if ($lastElement['article_1c']) {
            $lastElement['commerceml_id'] = $lastElement['article_1c'];
        }

        /*if (!$lastElement['qty'] || $lastElement['qty'] !== '') {
            $lastElement['qty'] = '0';
        }*/

        $lastElement['qty'] = '1'; // ZVEK ONLY

        //if (!$importProduct['min_qty'] || $importProduct['min_qty'] !== '') {
        $lastElement['min_qty'] = '1'; // ZVEK ONLY
        //}

        $lastElement['price'] = $helper->prepareAttributeValue($lastElement['price']);

        if ($lastElement['special_price'] && $lastElement['special_price'] !== '0') {
            $lastElement['special_price'] = $helper->prepareAttributeValue($lastElement['special_price']);
        }
        elseif ($lastElement['special_price'] == '0') {
            $lastElement['special_price'] = null;
        }

        if ($lastElement['categories']) {
            $lastElement['cml_subgroup'] = $lastElement['categories'];
            unset ($lastElement['categories']);
        }

        /*            if ($importProduct['size'] == '00') {
                        Mage::log('this product has 00 size: ' . $importProduct['sku'] . PHP_EOL, null, 'import-00-size.log');
                    }*/

        if ($lastElement['size'] && $lastElement['size'] !== '00') {
            $lastElement['size'] = $helper->prepareAttributeValue($lastElement['size']);
        }
        else {
            $lastElement['size'] = null;
        }
        $lastElement['cml_size'] = $lastElement['size'];


        $lastElement['is_in_stock'] = '1';
        
        return $lastElement;
    }

    private function checkforZeroPrice($lastElement, $i)
    {
        if ($lastElement['price'] == 0 || $lastElement['price'] == "") {
            $err_msg = 'строка ' . $i . ' не включена в импорт т.к. у товара неверный параметр цены !' . PHP_EOL . implode (' | ', $lastElement);
            Mage::log($err_msg, null, 'price_exclude.log');
            //if ($this->getConsoleLog()) {
            echo $err_msg . PHP_EOL;
            //}
            return false;
        }
        return true;
    }

    private function testFiler($lastElement, $config)
    {
        if ($config['proceed_custom_sku_list']) {

            $filter = explode('#', $lastElement['article_1c']);
            $filterSku = $filter[0];
            $skuList = explode(',', $config['conf_sku_import_list']);

            if (!in_array($filterSku, $skuList)) {
                return false;
            }
        }

        return true;
    }

    private function manualParseItemData($row, $newrow = array() )
    {

        for ($j=0; $j<count($row); $j++) {
            if (!strpos($row[$j], $this->_enclosure)) {
                $newrow[] = $row[$j];
            }
            else {
                $preRow = str_replace($this->_enclosure, "", $row[$j]);
                $rowArr  = explode($this->_delimiter, $preRow);
                $newrow = array_merge($newrow, $rowArr);
            }
        }
        return $newrow;
    }

    protected function _getConfigurableItemsData($product, $confParams, $configurable = array())
    {
        $confAttrib = explode(',', $confParams);


            $confSku = explode('#', $product['article_1c']);

            $this->_configurable[$confSku[0]]['items'][] = array(
                'sku'           => $product['sku'],
                'price'         => $product['price'],
                'special_price' => $product['special_price'],
                'name'          => $product['name'],
            );

            foreach ($confAttrib as $atr) {
                if ($product[$atr]) {
                    $this->_configurable[$confSku[0]]['confparams'][$atr] = $atr;
                }
            }

        $this->_allImportedConfigurable[$confSku[0]]['sku'] = $confSku[0];
        $this->_allImportedConfigurable[$confSku[0]]['name'] = $product['name'];

        return $configurable;
    }

    protected function getHelper()
    {
        return Mage::helper('autoimport/data');
    }
}