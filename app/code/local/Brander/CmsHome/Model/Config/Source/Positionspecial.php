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
class Brander_CmsHome_Model_Config_Source_Positionspecial
{

	public function toOptionArray($addEmpty = true)
	{
		$options = array(
			array(
				'label' => Mage::helper('brander_cmshome')->__('Left'),
				'value' => 1
			),
			array(
				'label' => Mage::helper('brander_cmshome')->__('Right'),
				'value' => 2
			),
			array(
				'label' => Mage::helper('brander_cmshome')->__('Full Width'),
				'value' => 3
			),
		);

		return $options;
	}

}