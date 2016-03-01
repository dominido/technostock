<?php
/**
 * Brander SiteHeart extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        SiteHeart
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_SiteHeart_Block_Js extends Mage_Core_Block_Template
{
	public function getWidgetId()
	{
		return Mage::getStoreConfig('siteheart/options/siteheart_widgetid');
	}

	public function isEnabled()
	{
		return (bool) Mage::getStoreConfig('siteheart/options/siteheart_enabled');
	}

	public function getSiteHeartCode()
	{
		$helper = Mage::helper('siteheart');

		$storeCode = Mage::app()->getStore()->getCode();
		$languages = $helper->getSiteHeartLanguages();

		if (isset($languages[$storeCode])){
			return $storeCode;
		}

		return false;
	}
}