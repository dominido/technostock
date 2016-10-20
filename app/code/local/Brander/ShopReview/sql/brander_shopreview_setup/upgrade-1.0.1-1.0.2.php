<?php

$installer = $this;
$connection = $installer->getConnection();

$installer->startSetup();

$installer->getConnection()
    ->changeColumn($installer->getTable('brander_shopreview/shopreview'), 'updated_at', 'updated_at', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
    ));
$installer->endSetup();
