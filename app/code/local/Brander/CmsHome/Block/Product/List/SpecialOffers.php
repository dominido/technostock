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
class Brander_CmsHome_Block_Product_List_SpecialOffers extends Mage_Catalog_Block_Product_List
{
	protected $_collectionCount = NULL;
	protected $_productCollectionId = NULL;
	protected $_cacheKeyArray = NULL;
	public $_active = false;
	
	/**
	 * Initialize block's cache
	 */
	protected function _construct()
	{
		parent::_construct();

		$this->addData(array(
			'cache_lifetime'    => 72000,
			'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG),
		));
	}
	
	/**
	 * Get Key pieces for caching block content
	 *
	 * @return array
	 */
	public function getCacheKeyInfo()
	{
		if (NULL === $this->_cacheKeyArray)
		{
			$this->_cacheKeyArray = array(
				'HOME_HOMEPAGE_SPECIALOFFER_SLIDER',
				Mage::app()->getStore()->getCurrentCurrency()->getCode(),
				//Mage::app()->getStore()->getCurrentCurrencyCode(),
				Mage::app()->getStore()->getId(),
				Mage::getDesign()->getPackageName(), ///
				Mage::getDesign()->getTheme('template'), 
				Mage::getSingleton('customer/session')->getCustomerGroupId(),
				'template' => $this->getTemplate(),
				
				$this->getBlockName(),
				$this->getCategoryId(),
				$this->getShowItems(),
				$this->getIsResponsive(),
				$this->getBreakpoints(),
				$this->getHideButton(),
				$this->getTimeout(),
				$this->getSortBy(),
				$this->getSortDirection(),
				
				(int)Mage::app()->getStore()->isCurrentlySecure(),
				$this->getUniqueCollectionId(),
			);
		}
		return $this->_cacheKeyArray;
	}
	
	/**
	 * Get collection id
	 *
	 * @return string
	 */
	public function getUniqueCollectionId()
	{
		if (NULL === $this->_productCollectionId)
		{
			$this->_prepareCollectionAndCache();
		}
		return $this->_productCollectionId;
	}
	
	/**
	 * Get number of products in the collection
	 *
	 * @return int
	 */
	public function getCollectionCount()
	{
		if (NULL === $this->_collectionCount)
		{
			$this->_prepareCollectionAndCache();
		}
		return $this->_collectionCount;
	}
	
	/**
	 * Prepare collection id, count collection
	 */
	protected function _prepareCollectionAndCache()
	{
		$ids = array();
		$i = 0;
		foreach ($this->_getProductCollection() as $product)
		{
			$ids[] = $product->getId();
			$i++;
		}
		
		$this->_productCollectionId = implode("+", $ids);
		$this->_collectionCount = $i;
	}
	
	/**
	 * Retrieve loaded category collection.
	 * Variables collected from CMS markup: category_id, product_count, is_random
	 */
	protected function _getProductCollection()
	{
		if (is_null($this->_productCollection))
		{


			$collection = Mage::getModel('brander_cmshome/specialOffers')->getSpecialOffersCollection();
			$collection->addStoreFilter();

			$sortBy = 'position';
			$sortDirection = 'DESC';
			$collection->addAttributeToSort($sortBy, $sortDirection);

			//Mage::getModel('catalog/layer')->prepareProductCollection($collection);
			
			if ($this->getIsRandom())
			{
				$collection->getSelect()->order('rand()');
			}
			$collection->addStoreFilter();
			$productCount = $this->getProductCount() ? $this->getProductCount() : 6;
			$collection->setPage(1, $productCount)
				->load();
			
			$this->_productCollection = $collection;
			//$this->setItemsSize($collection->getSize());
		}
		return $this->_productCollection;
	}
	
	/**
	 * Create unique block id for frontend
	 *
	 * @return string
	 */
	public function getFrontendHash()
	{
		return md5(implode("+", $this->getCacheKeyInfo()));
	}
}
