
<?php

require_once 'app/Mage.php';
Mage::app('admin');

$resource = Mage::getSingleton('core/resource');
$writeConnection = $resource->getConnection('core_write');

$query = "DELETE FROM `catalog_category_entity_varchar` WHERE `attribute_id` =136;";
$writeConnection->query($query);

$query = "DELETE FROM `catalog_category_entity_text` WHERE `attribute_id` =135;";
$writeConnection->query($query);

$query = "DELETE FROM `catalog_category_entity_text` WHERE `attribute_id` =137;";
$writeConnection->query($query);

$query = "DELETE FROM `catalog_category_entity_text` WHERE `attribute_id` =138;";
$writeConnection->query($query);

