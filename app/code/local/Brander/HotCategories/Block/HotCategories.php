<?php
/**
 * Brander HotCategories extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        HotCategories
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_HotCategories_Block_HotCategories extends Mage_Core_Block_Template
{
    protected $_collection = null;

    protected function _construct() {
        parent::_construct();
        $this->addData(array(
            'cache_lifetime' => false,
            'cache_tags' => array(
                'HOME',
                'HOMEPAGE_HOTCATEGORIES'
            )
        ));
    }

    public function getCacheKeyInfo() {
        $cacheId = array(
            'HOME_HOMEPAGE_HOTCATEGORIES',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout()
        );

        return $cacheId;
    }

    public function getCollection()
    {
        if(!$this->_collection){
            $this->_collection = Mage::getModel('brander_hotcategories/hotcategory')
                ->setStoreId(Mage::app()->getStore())
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', 1)
                ->addAttributeToSort('position', 'ASC')
                ->addAttributeToSort('entity_id', 'ASC')

            ;
        }
        return $this->_collection;
    }

    public function getElementUrl($hot)
    {
        $hotMode = $hot->getMode();
        if ($hotMode == '1') {
            $id = $hot->getCategoryId();
            $category = Mage::getModel('catalog/category')->load($id);
            $url = $category->getUrl();
            return $url;
        } elseif ($hotMode == '2') {
            return Mage::getBaseUrl().$hot->getCategoryUrl();
        }

        return '#';
    }

    protected function checkFile($imageFile)
    {
        $_helper = Mage::helper('brander_hotcategories/hotcategory_image');
        if (is_file($_helper->getImageBaseDir().$imageFile)) {
            return true;
        }
        return false;
    }

    public function getShowTitle()
    {
        return Mage::helper('brander_shop')->getCfg('brander_homepage/homepage_settings/hotcat_show_titles');
    }
}