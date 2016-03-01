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
class Brander_Benefits_Helper_Category extends Brander_Benefits_Helper_Data
{

    /**
     * get the selected benefits for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return array()
     */
    public function getSelectedBenefits(Mage_Catalog_Model_Category $category)
    {
        if (!$category->hasSelectedBenefits()) {
            $benefits = array();
            foreach ($this->getSelectedBenefitsCollection($category) as $benefit) {
                $benefits[] = $benefit;
            }
            $category->setSelectedBenefits($benefits);
        }
        return $category->getData('selected_benefits');
    }

    /**
     * get benefit collection for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return Brander_Benefits_Model_Resource_Benefit_Collection
     */
    public function getSelectedBenefitsCollection(Mage_Catalog_Model_Category $category)
    {
        $collection = Mage::getResourceSingleton('brander_benefits/benefit_collection')
            ->addCategoryFilter($category);
        return $collection;
    }
}
