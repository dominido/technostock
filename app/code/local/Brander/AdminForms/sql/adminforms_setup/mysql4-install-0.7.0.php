<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE `{$this->getTable('adminforms_block')}` (
  `key` VARCHAR(255)  NOT NULL,
  `entity_type` VARCHAR(255)  NOT NULL,
  `entity_id` INT  NOT NULL,
  PRIMARY KEY (`key`)
)
ENGINE = MyISAM;

ALTER TABLE `{$this->getTable('eav_entity_varchar')}` ADD UNIQUE INDEX `IDX_ATTRIBUTE_VALUE`(`entity_id`, `attribute_id`, `store_id`);
ALTER TABLE `{$this->getTable('eav_entity_text')}` ADD UNIQUE INDEX `IDX_ATTRIBUTE_VALUE`(`entity_id`, `attribute_id`, `store_id`);
");

$installer->endSetup();