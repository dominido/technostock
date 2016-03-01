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
 */class Brander_MarketExport_Model_Observer
{
    /**
     * @param Brander_MarketExport_Model_Mysql4_Export_Collection $export
     */
    protected function _generateXml($export)
    {
        $date = date("Y-m-d H:i", Mage::getModel('core/date')->timestamp(time()));
        //$date = $date . ':00';
        
        if ( defined('MARKET_EXPORT_CRON_EXPORT') ) Mage_Cron_Model_Observer::debug("Export: '" . $export->getName() . "'");

        $storeValidator = $export->getStores();
        if ($storeValidator[0] == '0') {
            $allStores = Mage::app()->getStores();
            foreach ($allStores as $store) {
                $stores[] = $store-> getStoreId();
            }
            $export->setStores($stores);
        }

        $export_info = array(
                'name'                 => $export->getName(),
                'path'                 => $export->getPath(),
                'stores'               => $export->getStores(),
                'include_out_of_stock' => $export->getIncludeOutOfStock(),
                'date'                 => $date,
                'description'          => $export->getDescription(),
                'type'                 => $export->getType(),
                'use_utm'              => $export->getUseUtm(),
                'utm_medium'           => $export->getUtmMedium(),
                'utm_source'           => $export->getUtmSource(),
                'utm_term'             => $export->getUtmTerm(),
                'utm_campaign'         => $export->getUtmCampaign(),
                'shopname'             => $export->getShopname(),
                'companyname'          => $export->getCompanyname(),
        );

        if ($export->getCustomAttributes() == "1"){
            $export_info['custom_attributes'] = "1";
            $export_info['custom_attributes_data'] = $export->getCustomAttributesData();
        }
        else {
            $export_info['custom_attributes'] = "0";
        }

        $content = $export->exportToYml($export_info);
        
        $exportFileTmp = Mage::helper('marketexport')->getFullExportDir() . '/' . $export->getData('entity_id') . '.tmp';
        $exportFile = Mage::helper('marketexport')->getFullExportDir() . '/' . $export->getData('entity_id') . '.xml';
        
        file_put_contents($exportFileTmp, $content);
        
        if (is_file($exportFile)){
            unLink($exportFile);
        }
        
        rename($exportFileTmp, $exportFile);
    }
    
    public function exportAll(){
        $exports = Mage::getResourceModel('marketexport/export_collection')
                 ->addFieldToFilter('is_active', 1);

        $_exportDir = Mage::helper('marketexport')->getFullExportDir();
        if (!is_dir($_exportDir)){
            mkDir($_exportDir);
        }
        
        foreach($exports as $export){
            $this->_generateXml($export);
        }
    }

    public function exportCurrent($export)
    {
        $_exportDir = Mage::helper('marketexport')->getFullExportDir();
        if (!is_dir($_exportDir)){
            mkDir($_exportDir);
        }
        $this->_generateXml($export);
    }

}
