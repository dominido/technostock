<?php
$installer = $this;

$installer->startSetup();

$sql = "
ALTER TABLE `{$installer->getTable('adminforms_block')}`
    ADD `entity_grid_options` TEXT;
";

$installer->run($sql);

$installer->endSetup();
