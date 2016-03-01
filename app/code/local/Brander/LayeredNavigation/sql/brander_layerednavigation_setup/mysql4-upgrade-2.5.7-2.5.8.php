<?php
/**
 * @author
 * @copyright Copyright (c) 2015
 * @package Brander_LayeredNavigation
 */
$this->startSetup();

/**
 * @Migration field_exist:brander_layerednavigation/value|cms_block_bottom:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('brander_layerednavigation/value')}` ADD `cms_block_bottom` VARCHAR(255);
");

$this->endSetup();