<?php
/**
 * {{Brander}}_CategoryBanner extension
 */
/**
 * Adminhtml observer
 *
 * @category    Brander
 * @package     Brander_CategoryBanner
 * @author      Ultimate Module Creator
 */
class Brander_CategoryBanner_Model_Adminhtml_Observer
{
    /**
     * check if tab can be added
     *
     * @access protected
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     * @author Ultimate Module Creator
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
     * add the categorybanner tab to categories
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Brander_CategoryBanner_Model_Adminhtml_Observer
     * @author Ultimate Module Creator
     */
    public function addCategoryCategorybannerBlock($observer)
    {
        $tabs = $observer->getEvent()->getTabs();
        $content = $tabs->getLayout()->createBlock(
            'brander_categorybanner/adminhtml_catalog_category_tab_categorybanner',
            'category.categorybanner.grid'
        )->toHtml();
        $serializer = $tabs->getLayout()->createBlock(
            'adminhtml/widget_grid_serializer',
            'category.categorybanner.grid.serializer'
        );
        $serializer->initSerializerBlock(
            'category.categorybanner.grid',
            'getSelectedCategorybanners',
            'categorybanners',
            'category_categorybanners'
        );
        $serializer->addColumnInputName('position');
        $content .= $serializer->toHtml();
        $tabs->addTab(
            'categorybanner',
            array(
                'label'   => Mage::helper('brander_categorybanner')->__('Category Image'),
                'content' => $content,
            )
        );
        return $this;
    }

    /**
     * save category image - category relation
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Brander_CategoryBanner_Model_Adminhtml_Observer
     * @author Ultimate Module Creator
     */
    public function saveCategoryCategorybannerData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('categorybanners', -1);
        if ($post != '-1') {
            $post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);
            $category = Mage::registry('category');
            $categorybannerCategory = Mage::getResourceSingleton('brander_categorybanner/categorybanner_category')
                ->saveCategoryRelation($category, $post);
        }
        return $this;
    }
}
