<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('brander_newpost/locale'),
'is_active', 'TINYINT(1) UNSIGNED DEFAULT 1 AFTER magento_store_id');

$installer->endSetup();