<?php
$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('brander_layerednavigation/value')}`
    ADD COLUMN `featured_order` TINYINT UNSIGNED DEFAULT 0
"); 
$this->endSetup();