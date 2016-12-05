<?php
/**
 * {{Brander}}_CategoryBanner extension
 */
/**
 * Category Image list on category page block
 *
 * @category    Brander
 * @package     Brander_CategoryBanner
 * @author      Ultimate Module Creator
 */
class Brander_CategoryBanner_Block_Catalog_Category_List_Categorybanner extends Mage_Core_Block_Template
{
    /**
     * get the list of category image
     *
     * @access protected
     * @return Brander_CategoryBanner_Model_Resource_Categorybanner_Collection
     * @author Ultimate Module Creator
     */
    public function getCategorybannerCollection()
    {
        if (!$this->hasData('categorybanner_collection')) {
            $category = Mage::registry('current_category');
            $collection = Mage::getResourceSingleton('brander_categorybanner/categorybanner_collection')
                ->addStoreFilter(Mage::app()->getStore())
                ->addFieldToFilter('status', 1)
                ->addCategoryFilter($category);
            $collection->getSelect()->order('related_category.position', 'ASC');
            $this->setData('categorybanner_collection', $collection);
        }
        return $this->getData('categorybanner_collection');
    }
}
