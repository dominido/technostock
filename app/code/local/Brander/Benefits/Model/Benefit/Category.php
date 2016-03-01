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
class Brander_Benefits_Model_Benefit_Category extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @access protected
     * @return void
     */
    protected function _construct()
    {
        $this->_init('brander_benefits/benefit_category');
    }

    /**
     * Save data for benefit-category relation
     *
     * @access public
     * @param  Brander_Benefits_Model_Benefit $benefit
     * @return Brander_Benefits_Model_Benefit_Category
     */
    public function saveBenefitRelation($benefit)
    {
        $data = $benefit->getCategoriesData();
        if (!is_null($data)) {
            $this->_getResource()->saveBenefitRelation($benefit, $data);
        }
        return $this;
    }

    /**
     * get categories for benefit
     *
     * @access public
     * @param Brander_Benefits_Model_Benefit $benefit
     * @return Brander_Benefits_Model_Resource_Benefit_Category_Collection
     */
    public function getCategoryCollection($benefit)
    {
        $collection = Mage::getResourceModel('brander_benefits/benefit_category_collection')
            ->addBenefitFilter($benefit);
        return $collection;
    }
}
