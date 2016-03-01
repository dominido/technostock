<?php
/**
 * Brander CmsHome extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        CmsHome
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_CmsHome_Model_HitsOfSales extends Mage_Catalog_Model_Abstract
{
    public function getHitOfSalesCollection()
    {
        $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );

        $params = Mage::helper('brander_shop')->getCfg('brander_homepage/products_sliders_hits');
        $sliderMode = $params->getFilterMode();

        if ($sliderMode == Brander_CmsHome_Model_Config_Source_AttributeOrCategoryHits::BRANDER_HIT_BY_SALES_REPORTS) {
            $collection = Mage::getResourceModel('reports/product_collection')
                ->addAttributeToSelect('*')
                ->addOrderedQty();
            //TODO: add filter - show available products only


        } else {
            $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('visibility', $visibility);
        }

        if ($sliderMode == Brander_CmsHome_Model_Config_Source_AttributeOrCategoryHits::BRANDER_HIT_BY_ATTRIBUTE ||
            $sliderMode == Brander_CmsHome_Model_Config_Source_AttributeOrCategoryHits::BRANDER_HIT_BY_ATTRIBUTE_AND_CATEGORY) {

            $collection->addAttributeToFilter('hit_of_sales', 1);
        }

        if ($sliderMode == Brander_CmsHome_Model_Config_Source_AttributeOrCategoryHits::BRANDER_HIT_BY_CATEGORY ||
            $sliderMode == Brander_CmsHome_Model_Config_Source_AttributeOrCategoryHits::BRANDER_HIT_BY_ATTRIBUTE_AND_CATEGORY) {

            $categoryIds = $params->getCategorySelect();
            if(!empty($categoryIds))
            {
                $categoriesFilter = ' AND _categories.category_id IN (' . $categoryIds . ')';
                $select = $collection->getSelect();
                        $select->joinInner(array('_categories'=> 'catalog_category_product'),
                            'e.entity_id = _categories.product_id' . $categoriesFilter,
                            array('category'=>'_categories.category_id'))
                            ->group('_categories.product_id');
                ;
                $select->distinct();
            }
        }

        return $collection;
    }
}