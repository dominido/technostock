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
class Brander_CmsMenu_Block_TopMenu extends Mage_Core_Block_Template

{
	protected function getCollection() {
		$collection = Mage::getModel('cmsadvanced/page')->getCollection()
			->addAttributeToSelect('*')
			->addAttributeToFilter('page_type', 'redirect')
			->setStoreId(Mage::app()->getStore()->getId())
			->addAttributeToFilter('top_menu', '1')
			->setOrder('position', 'asc')
			->addActiveFilter()
		;

		return $collection;
	}
}