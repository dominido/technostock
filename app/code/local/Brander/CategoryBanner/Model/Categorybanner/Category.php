<?php
/**
 * {{Brander}}_CategoryBanner extension
 */
/**
 * Category Image category model
 *
 * @category    Brander
 * @package     Brander_CategoryBanner
 * @author      Ultimate Module Creator
 */
class Brander_CategoryBanner_Model_Categorybanner_Category extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @access protected
     * @return void
     * @author Ultimate Module Creator
     */
    protected function _construct()
    {
        $this->_init('brander_categorybanner/categorybanner_category');
    }

    /**
     * Save data for category image-category relation
     *
     * @access public
     * @param  Brander_CategoryBanner_Model_Categorybanner $categorybanner
     * @return Brander_CategoryBanner_Model_Categorybanner_Category
     * @author Ultimate Module Creator
     */
    public function saveCategorybannerRelation($categorybanner)
    {
        $data = $categorybanner->getCategoriesData();
        if (!is_null($data)) {
            $this->_getResource()->saveCategorybannerRelation($categorybanner, $data);
        }
        return $this;
    }

    /**
     * get categories for category image
     *
     * @access public
     * @param Brander_CategoryBanner_Model_Categorybanner $categorybanner
     * @return Brander_CategoryBanner_Model_Resource_Categorybanner_Category_Collection
     * @author Ultimate Module Creator
     */
    public function getCategoryCollection($categorybanner)
    {
        $collection = Mage::getResourceModel('brander_categorybanner/categorybanner_category_collection')
            ->addCategorybannerFilter($categorybanner);
        return $collection;
    }
}
