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
class Brander_Shop_Model_Config_Source_SocialIconsPosition
{

	const SOCIAL_ICONS_IN_NEWSLETTER_BLOCK = 1;
	const SOCIAL_ICONS_IN_INFORMATION_PAGES_BLOCK = 2;
	const SOCIAL_ICONS_IN_ABOUT_US_BLOCK = 3;
	const SOCIAL_ICONS_IN_OUR_CONTACTS_BLOCK = 4;

	public function toOptionArray($addEmpty = true)
	{
		$options = array(
			array(
				'label' => Mage::helper('brander_shop')->__('bottom Footer Left side'),
				'value' => self::SOCIAL_ICONS_IN_NEWSLETTER_BLOCK
			),
			array(
				'label' => Mage::helper('brander_shop')->__('middle Footer Right Block'),
				'value' => self::SOCIAL_ICONS_IN_INFORMATION_PAGES_BLOCK
			),
/*			array(
				'label' => Mage::helper('brander_shop')->__('in About Us Block'),
				'value' => self::SOCIAL_ICONS_IN_ABOUT_US_BLOCK
			),
			array(
				'label' => Mage::helper('brander_shop')->__('in Our Contacts Block'),
				'value' => self::SOCIAL_ICONS_IN_OUR_CONTACTS_BLOCK
			),*/
		);

		return $options;
	}

}