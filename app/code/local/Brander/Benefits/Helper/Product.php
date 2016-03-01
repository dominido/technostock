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
class Brander_Benefits_Helper_Product extends Brander_Benefits_Helper_Data
{

    /**
     * get the selected benefits for a product
     *
     * @access public
     * @param Mage_Catalog_Model_Product $product
     * @return array()
     */
    public function getSelectedBenefits(Mage_Catalog_Model_Product $product)
    {
        if (!$product->hasSelectedBenefits()) {
            $benefits = array();
            foreach ($this->getSelectedBenefitsCollection($product) as $benefit) {
                $benefits[] = $benefit;
            }
            $product->setSelectedBenefits($benefits);
        }
        return $product->getData('selected_benefits');
    }

    /**
     * get benefit collection for a product
     *
     * @access public
     * @param Mage_Catalog_Model_Product $product
     * @return Brander_Benefits_Model_Resource_Benefit_Collection
     */
    public function getSelectedBenefitsCollection(Mage_Catalog_Model_Product $product)
    {
        $collection = Mage::getResourceSingleton('brander_benefits/benefit_collection')
            ->addProductFilter($product);
        return $collection;
    }
}
