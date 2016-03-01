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
class Brander_UnitopBlog_Model_Config_Source_Homepagepostlimit
{

	public function toOptionArray($addEmpty = true)
	{

		$options[] = array(
				'label' => 3,
				'value' => 3
			);
		$options[] = array(
			'label' => 4,
			'value' => 4
		);
		return $options;
	}

	public function getAllOptions()
	{
		return $this->toOptionArray(false);
	}
}