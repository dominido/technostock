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
require_once ("Mage/Adminhtml/controllers/Catalog/ProductController.php");
class Brander_ProductBanners_Adminhtml_Productbanners_Banner_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    /**
     * construct
     *
     * @access protected
     * @return void

     */
    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('Brander_ProductBanners');
    }

    /**
     * banners in the catalog page
     *
     * @access public
     * @return void

     */
    public function bannersAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('product.edit.tab.banner')
            ->setProductBanners($this->getRequest()->getPost('product_banners', null));
        $this->renderLayout();
    }

    /**
     * banners grid in the catalog page
     *
     * @access public
     * @return void

     */
    public function bannersGridAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('product.edit.tab.banner')
            ->setProductBanners($this->getRequest()->getPost('product_banners', null));
        $this->renderLayout();
    }
}
