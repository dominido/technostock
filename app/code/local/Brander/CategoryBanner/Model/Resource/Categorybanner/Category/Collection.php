<?php
/**
 * {{Brander}}_CategoryBanner extension
 */
/**
 * Category Image - Category relation resource model collection
 *
 * @category    Brander
 * @package     Brander_CategoryBanner
 * @author      Ultimate Module Creator
 */
class Brander_CategoryBanner_Model_Resource_Categorybanner_Category_Collection extends Mage_Catalog_Model_Resource_Category_Collection
{
    /**
     * remember if fields have been joined
     *
     * @var bool
     */
    protected $_joinedFields = false;

    /**
     * join the link table
     *
     * @access public
     * @return Brander_CategoryBanner_Model_Resource_Categorybanner_Category_Collection
     * @author Ultimate Module Creator
     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('brander_categorybanner/categorybanner_category')),
                'related.category_id = e.entity_id',
                array('position')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * add category image filter
     *
     * @access public
     * @param Brander_CategoryBanner_Model_Categorybanner | int $categorybanner
     * @return Brander_CategoryBanner_Model_Resource_Categorybanner_Category_Collection
     * @author Ultimate Module Creator
     */
    public function addCategorybannerFilter($categorybanner)
    {
        if ($categorybanner instanceof Brander_CategoryBanner_Model_Categorybanner) {
            $categorybanner = $categorybanner->getId();
        }
        if (!$this->_joinedFields) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.categorybanner_id = ?', $categorybanner);
        return $this;
    }
}
