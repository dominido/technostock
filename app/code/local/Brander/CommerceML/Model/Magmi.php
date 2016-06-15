<?php

define("MAGMI_BS", BP . DS . "magmi");
define("MAGMI_INC", MAGMI_BS . "/inc");
define("MAGMI_INT", MAGMI_BS . "/integration/inc");
define("MAGMI_ENG", MAGMI_BS . "/engines");
set_include_path(ini_get("include_path") . DS . MAGMI_INC . DS . MAGMI_INT . DS . MAGMI_ENG);
date_default_timezone_set('Europe/Kiev');

require_once(MAGMI_BS . DS . "inc" . DS . "magmi_defs.php");
require_once(MAGMI_INT . DS . "magmi_datapump.php");

class Brander_CommerceML_Model_Magmi extends Magmi_DataPumpFactory
{
    public function importProducts($products, $type = 'create')
    {
        $memory_limit = ini_get('memory_limit');
        ini_set('memory_limit', '-1');

        $logger = Mage::getModel('brandercml/logger');
        if (count($products)) {
            $dp         = $this->getDataPumpInstance("productimport");
            //$counter    = 1;
            //$total      = count($products);
            $dp->beginImportSession("default", $type, $logger);

            //$date = date('Y/m/d H:i:s');
            /*$this->getHelper()->logMessage('--- ' . $total . ' items import starts at: ' . $date);*/

            foreach ($products as $product) {
                $dp->ingest($product);
                //$counter++;



                //Mage::log('sku: '.$product['sku'].' price: '.$product['price'].' sp_price: '.$product['special_price'].PHP_EOL, null, 'items2.log');



/*                if (is_int($counter/1000)) {

                    $date = date('Y/m/d H:i:s');
                    $this->getHelper()->logMessage($counter . '/' . $total . ' time: ' . $date);
                }*/


                //echo $counter++ . '/' . $total . ' (sku: ' . $product['sku'] . ', name: ' . @$product['name'] . ')' . PHP_EOL;
            }
            $dp->endImportSession();
            return true;
        }

        ini_set('memory_limit', $memory_limit);
        return false;
    }

    public function updateProducts($products)
    {
        $logger = Mage::getModel('brandercml/logger');
        if (count($products)) {
            $dp = $this->getDataPumpInstance("productimport");
            $dp->beginImportSession("default", "update", $logger);
            foreach ($products as $product) {
                $dp->ingest($product);
            }
            $dp->endImportSession();
            return true;
        }

        return false;
    }

    public function deleteProducts($products)
    {
        $logger = Mage::getModel('brandercml/logger');
        if (count($products)) {
            $dp = $this->getDataPumpInstance("productimport");
            $dp->beginImportSession("default", "delete", $logger);
            $i = 0;
            foreach ($products as $product) {
                $dp->ingest($product);
                $i++;
                if (is_int($i/1000)) {
                    echo 'deleted ' . $i . ' products';
                }
            }
            $dp->endImportSession();
            return true;
        }

        return false;
    }

    protected function getHelper()
    {
        return Mage::helper('autoimport/data');
    }
}