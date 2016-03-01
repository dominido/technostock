<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('adminforms_block')}` ADD COLUMN `entity_grid_block` VARCHAR(255)  AFTER `entity_id`;
ALTER TABLE `{$this->getTable('adminforms_block')}` MODIFY COLUMN `entity_id` INTEGER  DEFAULT 0;
");

$installer->endSetup();
