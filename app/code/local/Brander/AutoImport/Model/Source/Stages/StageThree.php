<?php

/**
 * Brander AutoImport extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        AutoImport
 * @copyright      Copyright (c) 2014-2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */

class Brander_AutoImport_Model_Source_Stages_StageThree extends Varien_Object
{
	public function getCollection()
	{
		$stageDates = Mage::helper('autoimport/stages')->getStageThreeDates();
		$collection = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToSelect('*')
			->addAttributeToFilter('status', 2)
			->addAttributeToFilter('updated_at', array(array('date' => true, 'to' => $stageDates->getStageToDate())))
		;
		return $collection;
	}

	public function getGridCollection()
	{
		$stageDates = Mage::helper('autoimport/stages')->getStageThreeDates();
		$collection = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToSelect('*')
			->addAttributeToFilter('status', 2)
			->addAttributeToFilter('updated_at', array(array('date' => true, 'to' => $stageDates->getStageGridToDate())))
		;
		return $collection;
	}
}