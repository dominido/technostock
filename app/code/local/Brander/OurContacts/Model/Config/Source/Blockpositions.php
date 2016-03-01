<?php
/**
 * Brander OurContacts extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        OurContacts
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_OurContacts_Model_Config_Source_Blockpositions
{
	const CONTAINER_HEADER_PRIMARY_LEFT_1 		= 1;
	const CONTAINER_HEADER_PRIMARY_CENTRAL_1 	= 2;
	const CONTAINER_HEADER_PRIMARY_RIGHT_1 		= 3;
	const CONTAINER_HEADER_TOP_RIGHT_1 			= 4;
	const CONTAINER_HEADER_TOP_RIGHT_2 			= 5;
	const CONTAINER_HEADER_TOP_LEFT_1 			= 6;
	const CONTAINER_HEADER_TOP_LEFT_2 			= 7;
	const CONTAINER_USER_MENU 					= 9;

	static $positions = array(
		self::CONTAINER_HEADER_PRIMARY_LEFT_1 		=> 'container_header_primary_left_1',
		self::CONTAINER_HEADER_PRIMARY_CENTRAL_1 	=> 'container_header_primary_central_1',
		self::CONTAINER_HEADER_PRIMARY_RIGHT_1 		=> 'container_header_primary_right_1',
		self::CONTAINER_HEADER_TOP_RIGHT_1 			=> 'container_header_top_right_1',
		self::CONTAINER_HEADER_TOP_RIGHT_2 			=> 'container_header_top_right_2',
		self::CONTAINER_HEADER_TOP_LEFT_1 			=> 'container_header_top_left_1',
		self::CONTAINER_HEADER_TOP_LEFT_2 			=> 'container_header_top_left_2',
		self::CONTAINER_USER_MENU 					=> 'user_menu',
	);
	static $positionsCodes = array(
		self::CONTAINER_HEADER_PRIMARY_LEFT_1 		=> 'Primary Left',
		self::CONTAINER_HEADER_PRIMARY_CENTRAL_1 	=> 'Primary Central',
		self::CONTAINER_HEADER_PRIMARY_RIGHT_1 		=> 'Primary Right',
		self::CONTAINER_HEADER_TOP_RIGHT_1 			=> 'Top Right (right align)',
		self::CONTAINER_HEADER_TOP_RIGHT_2 			=> 'Top Right (left align)',
		self::CONTAINER_HEADER_TOP_LEFT_1 			=> 'Top Left (left align)',
		self::CONTAINER_HEADER_TOP_LEFT_2 			=> 'Top Left (right align)',
		/*self::CONTAINER_USER_MENU 					=> 'User Menu ...',*/
	);

	public function toOptionArray($addEmpty = true)
	{
		$positions = array();
		if ($addEmpty) {
			$positions[] = array(
				'label' => Mage::helper('adminhtml')->__('-- Please Select --'),
				'value' => ''
			);
		}

		foreach (self::$positionsCodes as $key => $name) {
			$positions[] = array(
				'label' => $name,
				'value' => $key
			);
		}

		return $positions;
	}

	public function getAllOptions()
	{
		return $this->toOptionArray(false);
	}

	public function getPositionAlias($id)
	{
		if (isset(self::$positions[$id]) && self::$positions[$id]) {
				return self::$positions[$id];
		}
		return '';
	}
}