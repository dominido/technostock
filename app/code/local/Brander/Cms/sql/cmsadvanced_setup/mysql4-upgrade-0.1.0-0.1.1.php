<?php
$installer = $this;
$installer->startSetup();

$installer->run('
ALTER TABLE `cms_page_entity` ADD `path` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
ADD `position` INT NOT NULL ,
ADD `level` INT NOT NULL ,
ADD `children_count` INT NOT NULL,
ADD `url_path` VARCHAR( 512 ) NOT NULL
');

$installer->endSetup();
