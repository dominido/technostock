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
require_once ("Mage/Adminhtml/controllers/Catalog/CategoryController.php");
class Brander_UnitopBlog_Adminhtml_Unitopblog_Post_Catalog_CategoryController extends Mage_Adminhtml_Catalog_CategoryController
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
     * posts grid in the catalog page
     *
     * @access public
     * @return void

     */
    public function postsgridAction()
    {
        $this->_initCategory();
        $this->loadLayout();
        $this->getLayout()->getBlock('category.edit.tab.post')
            ->setCategoryPosts($this->getRequest()->getPost('category_posts', null));
        $this->renderLayout();
    }
}
