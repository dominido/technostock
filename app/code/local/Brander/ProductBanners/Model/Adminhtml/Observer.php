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
class Brander_ProductBanners_Model_Adminhtml_Observer
{
    /**
     * check if tab can be added
     *
     * @access protected
     * @param Mage_Catalog_Model_Product $product
     * @return bool

     */
    protected function _canAddTab($product)
    {
        if ($product->getId()) {
            return true;
        }
        if (!$product->getAttributeSetId()) {
            return false;
        }
        $request = Mage::app()->getRequest();
        if ($request->getParam('type') == 'configurable') {
            if ($request->getParam('attributes')) {
                return true;
            }
        }
        return false;
    }

    /**
     * add the banner tab to products
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Brander_ProductBanners_Model_Adminhtml_Observer

     */
    public function addProductBannerBlock($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $product = Mage::registry('product');
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs && $this->_canAddTab($product)) {
            $block->addTab(
                'banners',
                array(
                    'label' => Mage::helper('brander_productbanners')->__('Banners'),
                    'url'   => Mage::helper('adminhtml')->getUrl(
                        'adminhtml/productbanners_banner_catalog_product/banners',
                        array('_current' => true)
                    ),
                    'class' => 'ajax',
                )
            );
        }
        return $this;
    }

    /**
     * save banner - product relation
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Brander_ProductBanners_Model_Adminhtml_Observer

     */
    public function saveProductBannerData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('banners', -1);
        if ($post != '-1') {
            $post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);
            $product = Mage::registry('product');
            $bannerProduct = Mage::getResourceSingleton('brander_productbanners/banner_product')
                ->saveProductRelation($product, $post);
        }
        return $this;
    }}
