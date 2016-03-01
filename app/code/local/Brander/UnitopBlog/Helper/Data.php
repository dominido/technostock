<?php
/**
 * Brander UnitopBlog extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        UnitopBlog
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_UnitopBlog_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * convert array to options
     *
     * @access public
     * @param $options
     * @return array

     */
    public function convertOptions($options)
    {
        $converted = array();
        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value']) &&
                isset($option['label']) && !is_array($option['label'])) {
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }

    public function getDescriptionTab()
    {
        $category = Mage::registry('current_category');

        $settings = Mage::helper('brander_shop')->getCfg('brander_unitopblog/settings/show_category_description_in');
        $params = Mage::app()->getRequest()->getParams();

        unset($params['id']);
        $description = $category->getDescription();

        if (count($params) || !$settings || empty($description)) {
            return false;
        }
        return $description;
    }

    public function isBlogPage($url)
    {
        $check = array();
        $requestUri = trim($url, '/');
        $check['postscategory'] = new Varien_Object(
            array(
                'prefix'        => Mage::getStoreConfig('brander_unitopblog/postscategory/url_prefix'),
                'suffix'        => Mage::getStoreConfig('brander_unitopblog/postscategory/url_suffix'),
                'list_key'      => Mage::getStoreConfig('brander_unitopblog/postscategory/url_rewrite_list'),
                'list_action'   => 'index',
                'model'         =>'brander_unitopblog/postscategory',
                'controller'    => 'postscategory',
                'action'        => 'view',
                'param'         => 'id',
                'check_path'    => 1
            )
        );
        $check['post'] = new Varien_Object(
            array(
                'prefix'        => Mage::getStoreConfig('brander_unitopblog/post/url_prefix'),
                'suffix'        => Mage::getStoreConfig('brander_unitopblog/post/url_suffix'),
                'list_key'      => Mage::getStoreConfig('brander_unitopblog/post/url_rewrite_list'),
                'list_action'   => 'index',
                'model'         =>'brander_unitopblog/post',
                'controller'    => 'post',
                'action'        => 'view',
                'param'         => 'id',
                'check_path'    => 0
            )
        );

        $baseUrl = Mage::getBaseUrl(); // this actually calls setBaseUrl() & setRequestUri()

        if (null === ($requestUri)) {
            return false;
        }

        // Remove the query string from REQUEST_URI
        if ($pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        if (!empty($baseUrl) || !empty($baseUrlRaw)) {
            if (strpos($requestUri, $baseUrl) === 0) {
                $pathInfo = substr($requestUri, strlen($baseUrl));
            } else {
                $pathInfo = $requestUri;
            }
        } else {
            $pathInfo = $requestUri;
        }

        foreach ($check as $key=>$settings) {
            if ($settings->getListKey()) {
                if ($pathInfo == $settings->getListKey()) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getCfg($paramName, $storeId = null)
    {
        $configOption = Mage::getStoreConfig($paramName, $storeId);
        if (is_array($configOption)) {
            $config = new Varien_Object($configOption);
            return $config;
        }
        return $configOption;
    }
}
