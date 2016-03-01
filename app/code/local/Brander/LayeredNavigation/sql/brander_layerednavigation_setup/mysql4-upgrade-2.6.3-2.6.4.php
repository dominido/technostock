<?php
/**
 * @author
 * @copyright Copyright (c) 2015
 * @package Brander_LayeredNavigation
 */
$this->startSetup();

/**
 * @Migration field_exist:brander_layerednavigation/page|cms_block_id:1
 */
$this->run("
ALTER TABLE `{$this->getTable('brander_layerednavigation/page')}`
ADD `cms_block_id` int(11) DEFAULT NULL");

$this->run("
UPDATE `{$this->getTable('brander_layerednavigation/page')}` v,`{$this->getTable('cms/block')}` b
SET v.`cms_block_id` = b.`block_id`
WHERE b.`identifier` = v.`cms_block`
");

/**
 * @Migration field_exist:brander_layerednavigation/page|cms_block:0
 */
$this->run("
ALTER TABLE `{$this->getTable('brander_layerednavigation/page')}`
DROP `cms_block`
");


$this->endSetup();
