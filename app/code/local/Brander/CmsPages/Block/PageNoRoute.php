<?php
/**
 * Brander CmsPages extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        CmsPages
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_CmsPages_Block_PageNoRoute extends Mage_Core_Block_Template

{
	protected function getCMSEmptyCategory($id) {
		$page = Mage::getModel('cmsadvanced/page')->getCollection()
			->addAttributeToSelect('*')
			->addAttributeToFilter('page_type', 'page404')
			->setStoreId(Mage::app()->getStore()->getId())
			->addFieldToFilter('entity_id', $id)
			->getFirstItem()
		;

		return $page;
	}

	public function getEmptyCategoryText()
	{
		$page = $this->getCMSEmptyCategory($this->getPageId());
		if ($page->getEntityId()) {
			return $page->getContent();
		} else {
			return '';
		}
	}

}