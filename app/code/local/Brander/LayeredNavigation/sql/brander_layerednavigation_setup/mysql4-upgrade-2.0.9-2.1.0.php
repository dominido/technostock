<?php
$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('brander_layerednavigation/page')}` ADD COLUMN `cats` TEXT NOT NULL;
"); 

$this->endSetup();