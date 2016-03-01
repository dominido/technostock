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
class Brander_Benefits_Block_Catalog_Category_List_Benefit extends Mage_Core_Block_Template
{
    /**
     * get the list of benefits
     *
     * @access protected
     * @return Brander_Benefits_Model_Resource_Benefit_Collection
     */
    public function getBenefitCollection()
    {
        if (!$this->hasData('benefit_collection')) {
            $category = Mage::registry('current_category');
            $collection = Mage::getResourceSingleton('brander_benefits/benefit_collection')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', 1)
                ->addCategoryFilter($category);
            $collection->getSelect()->order('related_category.position', 'ASC');
            $this->setData('benefit_collection', $collection);
        }
        return $this->getData('benefit_collection');
    }
}
