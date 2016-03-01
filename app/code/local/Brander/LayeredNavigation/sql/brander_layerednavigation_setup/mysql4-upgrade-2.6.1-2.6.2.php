<?php
/**
 * @author
 * @copyright Copyright (c) 2015
 * @package Brander_LayeredNavigation
 */
$this->startSetup();

/**
 * @Migration field_exist:brander_layerednavigation/filter|use_and_logic:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('brander_layerednavigation/filter')}` ADD `use_and_logic` TINYINT(1) NOT NULL DEFAULT 0;
");

$this->endSetup();
