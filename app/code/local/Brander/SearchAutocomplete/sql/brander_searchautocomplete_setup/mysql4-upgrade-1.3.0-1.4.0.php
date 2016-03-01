<?php

$installer = $this;

$installer->startSetup();
$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('searchautocomplete/cmspro_fulltext')}`;
CREATE TABLE `{$installer->getTable('searchautocomplete/cmspro_fulltext')}` (
 `news_id` int(10) unsigned NOT NULL,
 `store_id` smallint(5) unsigned NOT NULL,
 `data_index` longtext NOT NULL,
 PRIMARY KEY (`news_id`,`store_id`),
 FULLTEXT KEY `data_index` (`data_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{$installer->getTable('searchautocomplete/cmspro_result')}`;
CREATE TABLE `{$installer->getTable('searchautocomplete/cmspro_result')}` (
  `query_id` int(10) unsigned NOT NULL,
  `news_id` smallint(6) NOT NULL,
  `relevance` decimal(6,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`query_id`,`news_id`),
  KEY `IDX_CMSPRO_QUERY` (`query_id`),
  KEY `IDX_CMSPRO_PAGE` (`news_id`),
  KEY `IDX_CMSPRO_RELEVANCE` (`query_id`, `relevance`),
  CONSTRAINT `FK_CMSPRO_RESULT_QUERY` FOREIGN KEY (`query_id`) REFERENCES `{$installer->getTable('catalogsearch_query')}` (`query_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
  
$installer->endSetup();

$installer->regenerateFullIndex();