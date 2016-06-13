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

class Brander_AutoImport_Model_Source_Stages_StageOne extends Varien_Object
{
	public function getCollection()
	{
		$stageFromDate = Mage::helper('autoimport/stages')->getStagePeriod(2);
		$stageToDate = Mage::helper('autoimport/stages')->getStagePeriod(1);

		$collection = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToSelect('*')
			->addAttributeToFilter('status', 1)
			->addAttributeToFilter('updated_at',  array(array('date' => true, 'to' => $stageToDate)))
			->addAttributeToFilter('updated_at', array(array('date' => true, 'from' => $stageFromDate)))
			->joinField('is_in_stock',
				'cataloginventory/stock_item',
				'is_in_stock',
				'product_id=entity_id',
				'is_in_stock=0',
				'{{table}}.stock_id=1',
				'left');
		;

		return $collection;
	}

}