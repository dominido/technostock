<?php
/**
 * @author
 * @copyright Copyright (c) 2015
 * @package Brander_LayeredNavigation
 */
$this->startSetup();

/**
 * @Migration field_exist:brander_layerednavigation/filter|include_in:1
 */
$this->run("
ALTER TABLE `{$this->getTable('brander_layerednavigation/filter')}` ADD COLUMN `include_in` VARCHAR(256) NOT NULL;
");

$this->endSetup();