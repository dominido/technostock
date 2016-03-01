<?php
$installer = $this;

$installer->startSetup();

$sql = "
ALTER TABLE {$this->getTable('eav_entity_datetime')}
    MODIFY COLUMN `value` DATETIME  DEFAULT '0000-00-00 00:00:00';
";

$installer->run($sql);

$installer->endSetup();