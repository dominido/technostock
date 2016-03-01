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
class Brander_UnitopBlog_Model_Config_Source_Catdescrpostion
{

	public function toOptionArray($addEmpty = false)
	{
		$options = array(
			array(
				'label' => Mage::helper('catalog')->__('Page Top'),
				'value' => 0
			),
			array(
				'label' => Mage::helper('catalog')->__('Page Bottom'),
				'value' => 1
			)
		);

		return $options;
	}

	public function getAllOptions()
	{
		return $this->toOptionArray(false);
	}
}