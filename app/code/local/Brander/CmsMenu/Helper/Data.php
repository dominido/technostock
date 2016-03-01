<?php
/**
 * Brander CmsMenu extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        CmsMenu
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_CmsMenu_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getContainerClass()
    {
        if (!$this->getIsHomePage()) {
            return '';
        }

        $menuType = Mage::helper('ultraslideshow')->getCfg('banners/banners_or_menu');
        if ($menuType == Brander_CmsMenu_Model_Model_Config_Source_Slidertype::SLIDER_TYPE_VIEW_SLIDER_AND_CATEGORIES) {
            return 'leftsidemenu';
        }

        //TODO: if no slider padding
        return 'slipslider';
    }

    public function isSuperCategory($category) {
        if (($this->getContainerClass()) && $this->getContainerClass() == 'leftsidemenu') {
            $currentCategoryId = $category->getId();
            $superCategoryId = Mage::helper('ultraslideshow')->getCfg('banners/category_select');

            if ($currentCategoryId == $superCategoryId && $this->getIsHomePage()) {
                return true;
            }
        }
        return false;
    }

    protected function getIsHomePage()
    {
        if($handles = Mage::getSingleton('core/layout')->getUpdate()->getHandles()) {
            if (in_array("PAGE_TYPE_UNIPAGETYPE", $handles)) {
                return true;
            }
        }
        return false;
    }

    public function isShowFooterTitle()
    {
        if ($this->getSiteCfg('ultimo/footer/show_titles')) {
            return true;
        }
        return false;
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
}