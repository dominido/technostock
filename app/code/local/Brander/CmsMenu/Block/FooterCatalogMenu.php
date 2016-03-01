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
class Brander_CmsMenu_Block_FooterCatalogMenu extends Mage_Core_Block_Template

{
	protected function _construct() {
		parent::_construct();
		$this->addData(array(
			'cache_lifetime' => false,
			'cache_tags' => array(
				'FOOTER',
				'FOOTER_CATALOGMENU'
			)
		));
	}

	public function getCacheKeyInfo() {
		$cacheId = array(
			'FOOTER_CATALOGMENU',
			Mage::app()->getStore()->getId(),
			Mage::getDesign()->getPackageName(),
			Mage::getDesign()->getTheme('template'),
			'template' => $this->getTemplate(),
			'name' => $this->getNameInLayout()
		);

		return $cacheId;
	}

	protected function getCategories() {

		$categories = Mage::getModel('catalog/category')
			->getCollection()
			->setStoreId(Mage::app()->getStore()->getId())
			->addAttributeToFilter('include_in_footer_menu', '1')
			->addFieldToFilter('is_active', '1')
			->addAttributeToSelect('*');

		return $categories;
	}
}