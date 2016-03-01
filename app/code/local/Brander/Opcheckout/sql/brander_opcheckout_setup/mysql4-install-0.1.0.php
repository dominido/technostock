<?php

$installer = $this;
$installer->startSetup();

$table = $this->getTable('sales_flat_order');

// $query = 'ALTER TABLE `' . $table . '` ADD COLUMN IF NOT EXIST `order_comment` TEXT CHARACTER SET utf8 DEFAULT NULL';
$query = 'ALTER TABLE `' . $table . '` ADD COLUMN `order_comment` TEXT CHARACTER SET utf8 DEFAULT NULL';
$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
$connection->query($query);

$installer->endSetup();

