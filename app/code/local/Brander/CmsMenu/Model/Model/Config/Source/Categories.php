<?php
/**
 * Brander CmsMenu extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        CmsMenu
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_CmsMenu_Model_Model_Config_Source_Categories

{
	protected $_catTree = array();
	protected $_storeCategories = array();

	public function toOptionArray($addEmpty = true)
	{
		$collection = Mage::getModel('catalog/category')->getCollection();
		$collection->addAttributeToSelect('name')
				->addIsActiveFilter()
				->addAttributeToFilter('level', array('eq' => '1'));
		$rootCats  = $collection->getItems();

		if (!empty($this->_catTree)) {
			$addEmpty = false;
		}

		if ($addEmpty) {
			$this->_catTree[] = array(
				'label' => Mage::helper('adminhtml')->__('-- Please Select --'),
				'value' => ''
			);
		}

		foreach ($rootCats as $rootCat) {
			$this->getStoreCategories($rootCat->getId());
		}
		return $this->_catTree;
	}

	public function getStoreCategories($parentId, $sorted=false, $asCollection=false, $toLoad=true)
	{
		$parent     = $parentId;
		$cacheKey   = sprintf('%d-%d-%d-%d', $parent, $sorted, $asCollection, $toLoad);
		        if (isset($this->_storeCategories[$cacheKey])) {
                    return $this->_storeCategories[$cacheKey];
                }

		$category = Mage::getModel('catalog/category');
		/* @var $category Mage_Catalog_Model_Category */
		if (!$category->checkId($parent)) {
			if ($asCollection) {
				return new Varien_Data_Collection();
			}
			return array();
		}

		$recursionLevel  = max(0, (int) Mage::app()->getStore()->getConfig('brander_homepage/products_sliders_special/max_category_lvl'));
		$storeCategories = $category->getCategories($parent, $recursionLevel, $sorted, $asCollection, $toLoad);

		foreach ($storeCategories as $cat) {
			if ($cat->getName() != "") {
				$lvlChar = str_repeat(' ¬', $cat->getLevel() - 2);
				$this->_catTree[] = array(
					//'label' => $category->getName() . $category->getParentCategory()->getName(),
					'label' => $lvlChar. ' '. $cat->getName(),
					'value' => $cat->getId()
				);
				if ($cat->getLevel() <= $recursionLevel) {
					$this->getStoreCategories($cat->getId());
				}
			}
		}
		$this->_storeCategories[$cacheKey] = $storeCategories;
		return $storeCategories;
	}
}