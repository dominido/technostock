<?php
/**
 * Brander Shop extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        Shop
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_Shop_Helper_Labels extends Brander_Shop_Helper_Data
{
	/**
	 * Get product labels (HTML)
	 *
	 * @return string
	 */
	public function getLabels($product)
	{
		$html = '';
		$labelCfg = $this->getCfg('ultimo/product_labels');
		$isHit = false;
		if ($labelCfg->getBestsellers()) {
			$isHit = $this->isHit($product);
		}

		$isNew = false;
		if ($labelCfg->getNew()) {
			$isNew = $this->isNew($product);
		}

		$isSale = false;
		$saleText = $labelCfg->getSaleText();
		if (Mage::getStoreConfig('ultimo/product_labels/sale'))
		{
			$isSale = $this->isOnSale($product);
			if ($labelCfg->getSaleDiscount()) {
				if ($discount = Mage::helper('brander_productdiscount')->getProductDiscount($product)) {
					$saleText = $discount;
				}
			}
		}

		if ($isHit == true) {
			$html .= '<span class="sticker-wrapper top-left"><span class="sticker bestseller">' . $labelCfg->getBestsellersText() . '</span></span>';
		}

		if ($isSale == true) {
			$html .= '<span class="sticker-wrapper top-right"><span class="sticker sale">' . $saleText . '</span></span>';
		} elseif ($isNew) {
			$html .= '<span class="sticker-wrapper top-right"><span class="sticker new">' . $labelCfg->getNewText() . '</span></span>';
		}

		return $html;
	}

	public function isHit($product) {
		$collection = Mage::getModel('brander_cmshome/hitsOfSales')->getHitOfSalesCollection();
		$collection->addStoreFilter();

/*		$sortBy = 'position';
		$sortDirection = 'DESC';
		$collection->addAttributeToSort($sortBy, $sortDirection);*/

		$hits = $collection->getItems();
		if (count($hits) && isset($hits[$product->getId()])) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check if "sale" label is enabled and if product has special price
	 *
	 * @return  bool
	 */
	public function isOnSale($product)
	{
		$collection = Mage::getModel('brander_cmshome/specialOffers')->getSpecialOffersCollection();
		$collection->addStoreFilter();

/*		$sortBy = 'position';
		$sortDirection = 'DESC';
		$collection->addAttributeToSort($sortBy, $sortDirection);*/
		$spOffers = $collection->getItems();

		if (count($spOffers) && isset($spOffers[$product->getId()])) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check if "new" label is enabled and if product is marked as "new"
	 *
	 * @return  bool
	 */
	public function isNew($product)
	{
		return $this->_nowIsBetween($product->getData('news_from_date'), $product->getData('news_to_date'));
	}

	protected function _nowIsBetween($fromDate, $toDate)
	{
		if ($fromDate)
		{
			$fromDate = strtotime($fromDate);
			$toDate = strtotime($toDate);
			$now = strtotime(Mage::app()->getLocale()->date()->setTime('00:00:00')->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));

			if ($toDate)
			{
				if ($fromDate <= $now && $now <= $toDate)
					return true;
			}
			else
			{
				if ($fromDate <= $now)
					return true;
			}
		}

		return false;
	}
}
