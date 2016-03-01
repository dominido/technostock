<?php
/**
 * Brander ProductBanners extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        ProductBanners
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_ProductBanners_Helper_Product extends Brander_ProductBanners_Helper_Data
{

    /**
     * get the selected banners for a product
     *
     * @access public
     * @param Mage_Catalog_Model_Product $product
     * @return array()

     */
    public function getSelectedBanners(Mage_Catalog_Model_Product $product)
    {
        if (!$product->hasSelectedBanners()) {
            $banners = array();
            foreach ($this->getSelectedBannersCollection($product) as $banner) {
                $banners[] = $banner;
            }
            $product->setSelectedBanners($banners);
        }
        return $product->getData('selected_banners');
    }

    /**
     * get banner collection for a product
     *
     * @access public
     * @param Mage_Catalog_Model_Product $product
     * @return Brander_ProductBanners_Model_Resource_Banner_Collection

     */
    public function getSelectedBannersCollection(Mage_Catalog_Model_Product $product)
    {
        $collection = Mage::getResourceSingleton('brander_productbanners/banner_collection')
            ->addProductFilter($product);
        return $collection;
    }
}
