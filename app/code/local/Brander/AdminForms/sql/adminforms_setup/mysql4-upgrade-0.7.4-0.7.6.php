<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$installer->getTable('adminforms/eav_attribute')}` (
  `attribute_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `is_wysiwyg_enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL,
  PRIMARY KEY (`attribute_id`),
  CONSTRAINT `FK_ADMINFORMS_EAV_ATTRIBUTE_ID` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();
