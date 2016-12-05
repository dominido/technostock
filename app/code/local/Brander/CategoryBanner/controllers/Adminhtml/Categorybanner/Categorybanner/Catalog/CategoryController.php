<?php
/**
 * {{Brander}}_CategoryBanner extension
 */
/**
 * Category Image - category controller
 * @category    Brander
 * @package     Brander_CategoryBanner
 * @author      Ultimate Module Creator
 */
require_once ("Mage/Adminhtml/controllers/Catalog/CategoryController.php");
class Brander_CategoryBanner_Adminhtml_Categorybanner_Categorybanner_Catalog_CategoryController extends Mage_Adminhtml_Catalog_CategoryController
{
    /**
     * construct
     *
     * @access protected
     * @return void
     * @author Ultimate Module Creator
     */
    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('Brander_CategoryBanner');
    }

    /**
     * categorybanners grid in the catalog page
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function categorybannersgridAction()
    {
        $this->_initCategory();
        $this->loadLayout();
        $this->getLayout()->getBlock('category.edit.tab.categorybanner')
            ->setCategoryCategorybanners($this->getRequest()->getPost('category_categorybanners', null));
        $this->renderLayout();
    }
}
