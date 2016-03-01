<?php
/**
 * Brander CmsHome extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        CmsHome
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_CmsHome_Model_Config_Source_AttributeOrCategoryHits
{

	const BRANDER_HIT_BY_ATTRIBUTE 	= 0;
	const BRANDER_HIT_BY_CATEGORY 	= 1;
	const BRANDER_HIT_BY_ATTRIBUTE_AND_CATEGORY 	= 2;
	const BRANDER_HIT_BY_SALES_REPORTS 	= 3;

	public function toOptionArray($addEmpty = true)
	{
		$options = array(
			array(
				'label' => Mage::helper('brander_cmshome')->__('by Attribute'),
				'value' => 0
			),
			array(
				'label' => Mage::helper('brander_cmshome')->__('in Categories'),
				'value' => 1
			),
			array(
				'label' => Mage::helper('brander_cmshome')->__('by Attribute in Categories'),
				'value' => 2
			),
			array(
				'label' => Mage::helper('brander_cmshome')->__('from Sales Report History'),
				'value' => 3
			),
		);

		return $options;
	}

}