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
class Brander_ProductBanners_Model_Banner_Product extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @access protected
     * @return void

     */
    protected function _construct()
    {
        $this->_init('brander_productbanners/banner_product');
    }

    /**
     * Save data for banner-product relation
     * @access public
     * @param  Brander_ProductBanners_Model_Banner $banner
     * @return Brander_ProductBanners_Model_Banner_Product

     */
    public function saveBannerRelation($banner)
    {
        $data = $banner->getProductsData();
        if (!is_null($data)) {
            $this->_getResource()->saveBannerRelation($banner, $data);
        }
        return $this;
    }

    /**
     * get products for banner
     *
     * @access public
     * @param Brander_ProductBanners_Model_Banner $banner
     * @return Brander_ProductBanners_Model_Resource_Banner_Product_Collection

     */
    public function getProductCollection($banner)
    {
        $collection = Mage::getResourceModel('brander_productbanners/banner_product_collection')
            ->addBannerFilter($banner);
        return $collection;
    }
}
