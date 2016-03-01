<?php
$installer = $this;
$installer->startSetup();

/* Create table customer_callbacks */
$table = $installer->getConnection()->newTable($installer->getTable('brander_customercallbacks/callbacks'));

$table->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'primary'  => true,
    'unsigned' => true,
    'nullable' => false,
), 'Callback ID');

$table->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Customer Name');
$table->addColumn('phone', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false), 'Customer Phone');

$installer->getConnection()->createTable($table);
$installer->endSetup();