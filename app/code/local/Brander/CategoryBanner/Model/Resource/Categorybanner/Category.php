<?php
/**
 * {{Brander}}_CategoryBanner extension
 */
/**
 * Category Image - Categories relation model
 *
 * @category    Brander
 * @package     Brander_CategoryBanner
 * @author      Ultimate Module Creator
 */
class Brander_CategoryBanner_Model_Resource_Categorybanner_Category extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * initialize resource model
     *
     * @access protected
     * @return void
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     * @author Ultimate Module Creator
     */
    protected function  _construct()
    {
        $this->_init('brander_categorybanner/categorybanner_category', 'rel_id');
    }

    /**
     * Save category image - category relations
     *
     * @access public
     * @param Brander_CategoryBanner_Model_Categorybanner $categorybanner
     * @param array $data
     * @return Brander_CategoryBanner_Model_Resource_Categorybanner_Category
     * @author Ultimate Module Creator
     */
    public function saveCategorybannerRelation($categorybanner, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('categorybanner_id=?', $categorybanner->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $categoryId) {
            if (!empty($categoryId)) {
                $insert = array(
                    'categorybanner_id' => $categorybanner->getId(),
                    'category_id'   => $categoryId,
                    'position'      => 1
                );
                $this->_getWriteAdapter()->insertOnDuplicate($this->getMainTable(), $insert, array_keys($insert));
            }
        }
        return $this;
    }

    /**
     * Save  category - category image relations
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @param array $data
     * @return Brander_CategoryBanner_Model_Resource_Categorybanner_Category
     * @author Ultimate Module Creator
     */
    public function saveCategoryRelation($category, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('category_id=?', $category->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $categorybannerId => $info) {
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'categorybanner_id' => $categorybannerId,
                    'category_id'   => $category->getId(),
                    'position'      => @$info['position']
                )
            );
        }
        return $this;
    }
}
