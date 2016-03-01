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
class Brander_MarketExport_Helper_Data extends Mage_Core_Helper_Data
{
    protected $template = '{{name}}';
    protected $templatecat = '{{category}}';
    
    public function parseDescription($description, $product)
    {
        if ($description) {
            return str_replace($this->template, $product->getName(), $description);
        }
        return $product->getAdditionalInfo();
    }

    public function parseUtm($utm, $product)
    {
        if ($utm) {
            $utm = str_replace($this->template, $product->getName(), $utm);
            $utm = str_replace($this->templatecat, $product->getCategoryUrlKey(), $utm);
            //$utm = $this->generateOptionCode($utm);
            return $this->generateOptionCode($utm);
        }
        return '';
    }

    public function prepareProductLink($url_path, $export_name, $brand, $model, $category_url)
    {
        $google_analitics_param = '?utm_source='. str_replace(' ', '_',$export_name). '&utm_medium=cpc&utm_content='.$brand. '&utm_campaign='. $category_url .'&utm_term=' .$brand.'_'. $model;
        return Mage::getBaseUrl(). $url_path. str_replace(' ', '_',$google_analitics_param);
    }

    public function prepareBrands()
    {
        $brands = Mage::getModel('catalog/product')->getResource()->getAttribute('brand')->getSource()->getAllOptions(false);
        return $brands;
    }

    public function getExportUrl($path, $store = array()) {
        $pathUrl = Mage::getUrl(
            'export/get/file',
            array('id' => $path, '_type' => Mage_Core_Model_Store::URL_TYPE_WEB)
        );
        if (Mage::getStoreConfig('web/url/use_store')) {
            if (count($store) == 0 || $store[0] == '0') {
                $storeId = Mage::app()
                    ->getWebsite(true)
                    ->getDefaultGroup()
                    ->getDefaultStoreId();
            }
            else {
                $storeId = $store[0];
            }
            $storePath = Mage::getModel('core/store')->load($storeId);
            $pathUrl = str_replace('export/get', $storePath->getCode() . DS . 'export/get', $pathUrl);
        }

        return $pathUrl;
    }

    public function encodeFullUrl($url)
    {
        $url = urlencode($url);
        $chars = array(
            '%2F'   =>  '/',
            '%3A'   =>  ':',
            '%28'   =>  '(',
            '%29'   =>  ')'
        );
        foreach ($chars as $_from => $_to) {
            $url = str_replace($_from, $_to, $url);
        }
        return $url;
    }

    public function generateOptionCode($string) {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '_', Mage::helper('catalog/product_url')->format($string));
        $urlKey = strtolower($urlKey);
        return trim($urlKey, '_');
    }

    public function getExportDir() {
        $_exportDir = '/var/exports';
        return $_exportDir;
    }

    public function getFullExportDir() {
        $_exportDir = Mage::getBaseDir().'/var/exports';
        return $_exportDir;
    }

}
