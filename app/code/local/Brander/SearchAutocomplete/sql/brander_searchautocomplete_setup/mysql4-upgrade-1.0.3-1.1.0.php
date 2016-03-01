<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('searchautocomplete/blog_fulltext')}`;
CREATE TABLE `{$installer->getTable('searchautocomplete/blog_fulltext')}` (
 `post_id` int(10) unsigned NOT NULL,
 `store_id` smallint(5) unsigned NOT NULL,
 `data_index` longtext NOT NULL,
 PRIMARY KEY (`post_id`,`store_id`),
 FULLTEXT KEY `data_index` (`data_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{$installer->getTable('searchautocomplete/blog_result')}`;
CREATE TABLE `{$installer->getTable('searchautocomplete/blog_result')}` (
  `query_id` int(10) unsigned NOT NULL,
  `post_id` smallint(6) NOT NULL,
  `relevance` decimal(6,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`query_id`,`post_id`),
  KEY `IDX_BLOG_QUERY` (`query_id`),
  KEY `IDX_BLOG_PAGE` (`post_id`),
  KEY `IDX_BLOG_RELEVANCE` (`query_id`, `relevance`),
  CONSTRAINT `FK_BLOG_RESULT_QUERY` FOREIGN KEY (`query_id`) REFERENCES `{$installer->getTable('catalogsearch_query')}` (`query_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();