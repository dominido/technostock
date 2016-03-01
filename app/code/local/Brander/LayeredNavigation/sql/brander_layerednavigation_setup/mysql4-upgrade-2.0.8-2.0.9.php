<?php
$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('brander_layerednavigation/page')}` ADD COLUMN `meta_kw` varchar(255) NOT NULL;
    ALTER TABLE `{$this->getTable('brander_layerednavigation/value')}` ADD COLUMN `meta_kw` varchar(255) NOT NULL;
");

$this->endSetup();