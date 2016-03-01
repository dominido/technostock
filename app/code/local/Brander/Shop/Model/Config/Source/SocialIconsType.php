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
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_Shop_Model_Config_Source_SocialIconsType
{

	public function toOptionArray($addEmpty = true)
	{
		$options = array(
			array(
				'label' => Mage::helper('brander_shop')->__('round'),
				'value' => 'round'
			),
			array(
				'label' => Mage::helper('brander_shop')->__('square'),
				'value' => 'square'
			),
		);

		return $options;
	}

}