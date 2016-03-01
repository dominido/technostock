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
class Brander_UnitopBlog_Model_Adminhtml_Source_Labels extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

	public function toOptionArray($addEmpty = true)
	{
		$options = Mage::getStoreConfig('ultramegamenu/category_labels');
		$labels = array();

		if ($addEmpty) {
			$labels[] = array(
				'label' => Mage::helper('adminhtml')->__('-- Select Label --'),
				'value' => ''
			);
		}

		foreach ($options as $option) {
			$labels[] = array(
				'label' => $option,
				'value' => 1);
		}
		return $labels;
	}

	public function getAllOptions()
	{
		return $this->toOptionArray();
	}
}