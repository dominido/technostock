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
require_once ("Mage/Adminhtml/controllers/Catalog/ProductController.php");
class Brander_UnitopBlog_Adminhtml_Unitopblog_Post_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
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
        $this->setUsedModuleName('Brander_UnitopBlog');
    }

    /**
     * posts in the catalog page
     *
     * @access public
     * @return void

     */
    public function postsAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('product.edit.tab.post')
            ->setProductPosts($this->getRequest()->getPost('product_posts', null));
        $this->renderLayout();
    }

    /**
     * posts grid in the catalog page
     *
     * @access public
     * @return void

     */
    public function postsGridAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('product.edit.tab.post')
            ->setProductPosts($this->getRequest()->getPost('product_posts', null));
        $this->renderLayout();
    }
}
