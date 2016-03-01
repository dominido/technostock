<?php
/**
 * Brander Benefits extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        Benefits
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
require_once ("Mage/Adminhtml/controllers/Catalog/CategoryController.php");
class Brander_Benefits_Adminhtml_Benefits_Benefit_Catalog_CategoryController extends Mage_Adminhtml_Catalog_CategoryController
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
        $this->setUsedModuleName('Brander_Benefits');
    }

    /**
     * benefits grid in the catalog page
     *
     * @access public
     * @return void
     */
    public function benefitsgridAction()
    {
        $this->_initCategory();
        $this->loadLayout();
        $this->getLayout()->getBlock('category.edit.tab.benefit')
            ->setCategoryBenefits($this->getRequest()->getPost('category_benefits', null));
        $this->renderLayout();
    }
}
