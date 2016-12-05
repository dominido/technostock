<?php
/**
 * {{Brander}}_CategoryBanner extension
 */
/**
 * Category helper
 *
 * @category    Brander
 * @package     Brander_CategoryBanner
 * @author      Ultimate Module Creator
 */
class Brander_CategoryBanner_Helper_Category extends Brander_CategoryBanner_Helper_Data
{

    /**
     * get the selected category image for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return array()
     * @author Ultimate Module Creator
     */
    public function getSelectedCategorybanners(Mage_Catalog_Model_Category $category)
    {
        if (!$category->hasSelectedCategorybanners()) {
            $categorybanners = array();
            foreach ($this->getSelectedCategorybannersCollection($category) as $categorybanner) {
                $categorybanners[] = $categorybanner;
            }
            $category->setSelectedCategorybanners($categorybanners);
        }
        return $category->getData('selected_categorybanners');
    }

    /**
     * get category image collection for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return Brander_CategoryBanner_Model_Resource_Categorybanner_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedCategorybannersCollection(Mage_Catalog_Model_Category $category)
    {
        $collection = Mage::getResourceSingleton('brander_categorybanner/categorybanner_collection')
            ->addCategoryFilter($category);
        return $collection;
    }
}
