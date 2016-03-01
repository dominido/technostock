<?php
$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('brander_layerednavigation/filter')}` ADD COLUMN `range` int NOT NULL;
"); 

$this->endSetup();