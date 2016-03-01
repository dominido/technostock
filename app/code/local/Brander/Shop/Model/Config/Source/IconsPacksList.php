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
 * @copyright      Copyright (c) 2015-2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_Shop_Model_Config_Source_IconsPacksList
{

	public function toOptionArray($addEmpty = true)
	{
		$options = array(
			array(
				'label' => Mage::helper('brander_shop')->__('Icon Pack #%s %s', 1, '"Ultimo"'),
				'value' => '0'
			),
			array(
				'label' => Mage::helper('brander_shop')->__('Icon Pack #%s %s', 1, '"Jewerly"'),
				'value' => '1'
			),
			array(
				'label' => Mage::helper('brander_shop')->__('Icon Pack #%s %s', 2, '"Grandway"'),
				'value' => '2'
			),
			array(
				'label' => Mage::helper('brander_shop')->__('Icon Pack #%s %s', 3, '"Aromat"'),
				'value' => '3'
			),
		);

		return $options;
	}

}