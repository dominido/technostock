<?php
$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('brander_layerednavigation/filter')}` ADD `single_choice` TINYINT(1) NOT NULL;
    ALTER TABLE `{$this->getTable('brander_layerednavigation/filter')}` ADD `collapsed` TINYINT(1) NOT NULL;
"); 

$this->endSetup();