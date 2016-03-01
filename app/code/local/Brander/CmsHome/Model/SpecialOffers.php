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
class Brander_CmsHome_Model_SpecialOffers extends Mage_Catalog_Model_Abstract
{

	public function getSpecialOffersCollection()
	{

		$visibility = array(
			Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
			Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
		);

		$params = Mage::helper('brander_shop')->getCfg('brander_homepage/products_sliders_special');
		$sliderMode = $params->getFilterMode();

		/** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
		$collection = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToFilter('visibility', $visibility)
			->addMinimalPrice()
			->addFinalPrice()
			->addTaxPercents()
			//->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
			->addAttributeToSelect('*')
			->addUrlRewrite()
		;


		if ($params->getSpPriceFilter()) {
			$todayStartOfDayDate  = Mage::app()->getLocale()->date()
				->setTime('00:00:00')
				->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

			$todayEndOfDayDate  = Mage::app()->getLocale()->date()
				->setTime('23:59:59')
				->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);


			$collection
				->addAttributeToFilter('special_from_date', array('or'=> array(
					0 => array('date' => true, 'to' => $todayEndOfDayDate),
					1 => array('is' => new Zend_Db_Expr('null')))
				), 'left')
				->addAttributeToFilter('special_to_date', array('or'=> array(
					0 => array('date' => true, 'from' => $todayStartOfDayDate),
					1 => array('is' => new Zend_Db_Expr('null')))
				), 'left')
				->addAttributeToFilter(
					array(
						array('attribute' => 'special_from_date', 'is'=>new Zend_Db_Expr('not null')),
						//array('attribute' => 'special_to_date', 'is'=>new Zend_Db_Expr('not null'))
					)
				)
				->addAttributeToFilter('special_price', array('notnull' => true))
			;
		}

		if ($sliderMode == Brander_CmsHome_Model_Config_Source_AttributeOrCategory::BRANDER_BY_ATTRIBUTE ||
			$sliderMode == Brander_CmsHome_Model_Config_Source_AttributeOrCategory::BRANDER_BY_ATTRIBUTE_AND_CATEGORY) {

			$collection->addAttributeToFilter('special_offers', 1);
		}

		//$collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());

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

		return $collection;
	}

	protected function getSliderMode()
	{

	}
}
