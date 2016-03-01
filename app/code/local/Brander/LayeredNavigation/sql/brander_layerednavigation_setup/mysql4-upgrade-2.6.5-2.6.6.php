<?php
/**
 * @author
 * @copyright Copyright (c) 2015
 * @package Brander_LayeredNavigation
 */
$this->startSetup();

$table = $this->getTable('core/config_data');

$this->run("
UPDATE `{$table}` n
INNER JOIN `{$table}` o ON o.`path` = 'brander_layerednavigation/general/title_separator'
SET n.`value` = o.`value`
WHERE n.`path` = 'brander_layerednavigation/meta/title_separator'
");

$this->run("
UPDATE `{$table}` n
INNER JOIN `{$table}` o ON o.`path` = 'brander_layerednavigation/general/description_separator'
SET n.`value` = o.`value`
WHERE n.`path` = 'brander_layerednavigation/meta/description_separator'
");

$this->endSetup();
