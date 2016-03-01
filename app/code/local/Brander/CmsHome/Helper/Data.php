<?php
/**
 * Brander CmsHome extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        CmsHome
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_CmsHome_Helper_Data extends Mage_Core_Helper_Abstract
{

    protected $_storeCategories = array();

    public function getStoreCategories($sorted=false, $asCollection=false, $toLoad=true)
    {
        $parent     = Mage::getStoreConfig('ultraslideshow/banners/category_select');
        $cacheKey   = sprintf('%d-%d-%d-%d', $parent, $sorted, $asCollection, $toLoad);
        if (isset($this->_storeCategories[$cacheKey])) {
            return $this->_storeCategories[$cacheKey];
        }

        /**
         * Check if parent node of the store still exists
         */
        $category = Mage::getModel('catalog/category')->load($parent);

        /* @var $category Mage_Catalog_Model_Category */
        if (!$category->checkId($parent)) {
            if ($asCollection) {
                return new Varien_Data_Collection();
            }
            return array();
        }

        $recursionLevel  = max(0, (int) Mage::app()->getStore()->getConfig('catalog/navigation/max_depth'));
        $storeCategories = $category->getChildrenCategories($category);

        $this->_storeCategories[$cacheKey] = $storeCategories;
        return $storeCategories;
    }

    public function getSliderCategoryList()
    {
        return Mage::getStoreConfig('brander_homepage/products_sliders_special/category_select');
    }

    public function getSliderParams($type, $field)
    {
        $configPath = 'brander_homepage/products_sliders_'.$type.'/'.$field.'';
        return Mage::getStoreConfig($configPath);
    }

    public function getHomepageSlidersConfiguration($type)
    {
        $config = $this->getCfg('products_sliders_'.$type);
        if (!$config->getCustomConfig()) {
            $sliderSettings = $this->getSiteCfg('unitop_settings/products_sliders');
            $sliderSettings->setBlockName($config->getBlockName());
            $sliderSettings->setCategorySelect($config->getCategorySelect());
            return $sliderSettings;
        } else {
            return $config;
        }

    }

    public function getCfg($paramName)
    {
        $configOption = Mage::getStoreConfig('brander_homepage/'.$paramName);
        if (is_array($configOption)) {
            $config = new Varien_Object($configOption);
            return $config;
        }
        return $configOption;
    }

    public function getSiteCfg($paramName)
    {
        $configOption = Mage::getStoreConfig($paramName);
        if (is_array($configOption)) {
            $config = new Varien_Object($configOption);
            return $config;
        }
        return $configOption;
    }

    public function getHomePageBannerCssMode()
    {
        if ($this->getCfg('banner_config/enable')) {
            return 'slim-homepage-banners';
        }
        return '';
    }
}