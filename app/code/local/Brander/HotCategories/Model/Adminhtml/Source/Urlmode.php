<?php
/**
 * Brander HotCategories extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        HotCategories
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_HotCategories_Model_Adminhtml_Source_Urlmode extends Mage_Eav_Model_Entity_Attribute_Source_Abstract

{

	public function toOptionArray($addEmpty = true)
	{

		$options = array();

		if ($addEmpty) {
			$options[] = array(
				'label' => Mage::helper('adminhtml')->__('-- Please Select --'),
				'value' => ''
			);
		}

		$options[1] = array(
			'label' =>  Mage::helper('brander_hotcategories')->__('URL by Category Id'),
			'value' => 1,
		);
		$options[2] = array(
			'label' => Mage::helper('brander_hotcategories')->__('Site URL (without site name and language)'),
			'value' => 2
		);

		return $options;
	}

	public function getAllOptions($addEmpty = true)
	{
		return $this->toOptionArray($addEmpty);
	}
}