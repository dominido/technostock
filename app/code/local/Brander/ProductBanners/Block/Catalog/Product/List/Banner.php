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
class Brander_ProductBanners_Block_Catalog_Product_List_Banner extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * get the list of banners
     *
     * @access protected
     * @return Brander_ProductBanners_Model_Resource_Banner_Collection
     */
    public function getBannerCollection()
    {
        if (!$this->hasData('banner_collection')) {
            $product = Mage::registry('product');
            $collection = Mage::getResourceSingleton('brander_productbanners/banner_collection')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->addAttributeToSelect('*')
                ->addAttributeToSelect('title', 1)
                ->addAttributeToFilter('status', 1)
                ->addProductFilter($product);
            $collection->getSelect()->order('related_product.position', 'ASC');
            $this->setData('banner_collection', $collection);
        }
        return $this->getData('banner_collection');
    }
}
