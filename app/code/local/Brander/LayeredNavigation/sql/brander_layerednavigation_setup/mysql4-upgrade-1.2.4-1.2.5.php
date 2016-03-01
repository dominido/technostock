<?php
$this->startSetup();

$this->run("

CREATE TABLE `{$this->getTable('brander_layerednavigation/range')}` (
  `range_id` mediumint(8) unsigned NOT NULL auto_increment,
  `price_frm` int  unsigned NOT NULL,
  `price_to`  int  unsigned NOT NULL,
  PRIMARY KEY  (`range_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 

");

$this->endSetup();