
<?php
/**
 * Brander UnitopBlog extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        UnitopBlog
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_UnitopBlog_Model_Config_Source_Postscategories
{

	public function toOptionArray($addEmpty = true)
	{

		$collection = Mage::getResourceModel('brander_unitopblog/postscategory_collection')
			->setStoreId(Mage::app()->getStore()->getId())
			->addAttributeToSelect('*');

		$options = array();

		if ($addEmpty) {
			$options[] = array(
				'label' => Mage::helper('adminhtml')->__('-- Please Select --'),
				'value' => ''
			);
		}

		foreach ($collection as $category) {
			if ($category->getTitle() == 'ROOT') continue;
			$options[] = array(
				'label' => $category->getTitle() . ' ('.$this->getStatusText($category->getStatus()).')',
				'value' => $category->getId());
		}
		return $options;
	}

	public function getAllOptions()
	{
		return $this->toOptionArray(false);
	}

	protected function getStatusText($status = null)
	{
		if ($status == '1') {
			return Mage::helper('catalog')->__('Enabled');
		} else {
			return Mage::helper('catalog')->__('Disabled');
		}
	}
}