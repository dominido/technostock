<?php

$installer = $this;
$installer->startSetup();

$installer->run("DELETE FROM `catalog_category_entity_varchar` WHERE `attribute_id` = 136");
$installer->run("DELETE FROM `catalog_category_entity_text`WHERE `attribute_id` = 135");
$installer->run("DELETE FROM `catalog_category_entity_text`WHERE `attribute_id` = 137");
$installer->run("DELETE FROM `catalog_category_entity_text`WHERE `attribute_id` = 138");

$installer->endSetup();


