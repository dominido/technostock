<?php

$installer = $this;
$connection = $installer->getConnection();

$installer->startSetup();

$installer->getConnection()
    ->changeColumn($installer->getTable('brander_shopreview/shopreview'), 'create_at', 'create_at', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
    ));
$installer->endSetup();
