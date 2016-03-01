<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('eav_entity_int')}` ADD UNIQUE INDEX `IDX_ATTRIBUTE_VALUE`(`entity_id`, `attribute_id`, `store_id`);
ALTER TABLE `{$this->getTable('eav_entity_decimal')}` ADD UNIQUE INDEX `IDX_ATTRIBUTE_VALUE`(`entity_id`, `attribute_id`, `store_id`);
ALTER TABLE `{$this->getTable('eav_entity_datetime')}` ADD UNIQUE INDEX `IDX_ATTRIBUTE_VALUE`(`entity_id`, `attribute_id`, `store_id`);
");

$installer->endSetup();
