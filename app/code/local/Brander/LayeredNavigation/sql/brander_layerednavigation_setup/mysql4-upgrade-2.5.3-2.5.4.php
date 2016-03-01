<?php
/**
 * @author
 * @copyright Copyright (c) 2015
 * @package Brander_LayeredNavigation
 */
$this->startSetup();

/**
 * @Migration field_exist:brander_layerednavigation/value|url_alias:1
 */
$this->run("
ALTER TABLE `{$this->getTable('brander_layerednavigation/value')}` ADD  `url_alias` VARCHAR( 255 ) NULL DEFAULT NULL ,
ADD INDEX (  `url_alias` )
");
 
$this->endSetup();