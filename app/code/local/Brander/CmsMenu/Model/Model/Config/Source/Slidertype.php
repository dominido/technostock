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
class Brander_CmsMenu_Model_Model_Config_Source_Slidertype
{

	const SLIDER_TYPE_VIEW_JUST_SLIDER 				= 0;
	const SLIDER_TYPE_VIEW_SLIDER_AND_BANNERS 		= 1;
	const SLIDER_TYPE_VIEW_SLIDER_AND_CATEGORIES 	= 2;

	public function toOptionArray($addEmpty = true)
	{
		$options = array();

		$options[] = array(
			'label' => Mage::helper('brander_cmsmenu')->__('Just slider'),
			'value' => '0'
		);
		$options[] = array(
			'label' => Mage::helper('brander_cmsmenu')->__('Slider and Banners'),
			'value' => '1');

		$options[] = array(
			'label' => Mage::helper('brander_cmsmenu')->__('Slider and Fixed categories on HomePage'),
			'value' => '2');


		return $options;
	}

	public function getAllOptions($addEmpty = true)
	{
		return $this->toOptionArray($addEmpty);
	}
}