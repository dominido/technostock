<?php

$installer = $this;
$installer->startSetup();

$table = $this->getTable('sales_flat_order');

$this->_conn->addColumn($this->getTable('sales_flat_quote'), 'shipping_arrival_date', 'datetime');
$this->_conn->addColumn($this->getTable($table), 'shipping_arrival_date', 'datetime');
$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
$installer->endSetup();

