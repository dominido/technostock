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
class Brander_Benefits_Model_Resource_Benefit_Category extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * initialize resource model
     *
     * @access protected
     * @return void
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     */
    protected function  _construct()
    {
        $this->_init('brander_benefits/benefit_category', 'rel_id');
    }

    /**
     * Save benefit - category relations
     *
     * @access public
     * @param Brander_Benefits_Model_Benefit $benefit
     * @param array $data
     * @return Brander_Benefits_Model_Resource_Benefit_Category
     */
    public function saveBenefitRelation($benefit, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('benefit_id=?', $benefit->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $categoryId) {
            if (!empty($categoryId)) {
                $insert = array(
                    'benefit_id' => $benefit->getId(),
                    'category_id'   => $categoryId,
                    'position'      => 1
                );
                $this->_getWriteAdapter()->insertOnDuplicate($this->getMainTable(), $insert, array_keys($insert));
            }
        }
        return $this;
    }

    /**
     * Save  category - benefit relations
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @param array $data
     * @return Brander_Benefits_Model_Resource_Benefit_Category
     */
    public function saveCategoryRelation($category, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('category_id=?', $category->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $benefitId => $info) {
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'benefit_id' => $benefitId,
                    'category_id'   => $category->getId(),
                    'position'      => @$info['position']
                )
            );
        }
        return $this;
    }
}
