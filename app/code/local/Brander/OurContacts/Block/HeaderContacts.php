<?php
/**
 * Brander OurContacts extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        OurContacts
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_OurContacts_Block_HeaderContacts extends Mage_Core_Block_Template

{
	protected function _construct() {
		parent::_construct();
		$this->addData(array(
			'cache_lifetime' => false,
			'cache_tags' => array(
				'HEADER',
				'HEADER_CONTACTS'
			)
		));
	}

	public function getCacheKeyInfo() {
		$cacheId = array(
			'HEADER_CONTACTS',
			Mage::app()->getStore()->getId(),
			Mage::getDesign()->getPackageName(),
			Mage::getDesign()->getTheme('template'),
			'template' => $this->getTemplate(),
			'name' => $this->getNameInLayout()
		);

		return $cacheId;
	}

	protected function getCollection() {
		$collection = Mage::getModel('brander_ourcontacts/contact')->getCollection()
			->addAttributeToSelect('*')
			->setStoreId(Mage::app()->getStore()->getId())
			->addAttributeToFilter('show_in_header', '1')
			->addAttributeToFilter('status', '1')
			->addAttributeToSort('position', 'ASC');

		return $collection;
	}
}