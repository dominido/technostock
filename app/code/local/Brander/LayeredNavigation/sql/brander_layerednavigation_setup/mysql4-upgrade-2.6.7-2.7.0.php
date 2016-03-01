<?php
/**
 * @author
 * @copyright Copyright (c) 2015
 * @package Brander_LayeredNavigation
 */
$this->startSetup();

function alterPageMultipleStores($setup)
{
    /**
     * @Migration field_exist:brander_layerednavigation/page|stores:1
     * @Migration field_exist:brander_layerednavigation/page|store_id:0
     */
    $table = $setup->getTable('brander_layerednavigation/page');

    $setup->run("ALTER TABLE `{$table}` ADD `stores` TEXT NOT NULL AFTER `page_id`");
    $setup->run("UPDATE `{$table}` SET `stores` = `store_id`");
    $setup->run("ALTER TABLE `{$table}` DROP FOREIGN KEY `FK_LAYEREDNAVIGATION_PAGE_CORE_STORE`");
    $setup->run("ALTER TABLE {$table} DROP INDEX IDX_LAYEREDNAVIGATION_PAGE_STORE_VIEW_ID");
    $setup->run("ALTER TABLE `{$table}` DROP `store_id`");
}

function alterSliderStep($setup)
{
    $table = $setup->getTable('brander_layerednavigation/filter');

    $setup->run("ALTER TABLE `{$table}` CHANGE `slider_decimal` `slider_decimal` DECIMAL(6,2) NOT NULL DEFAULT '1'");
    $setup->run("UPDATE `{$table}` SET slider_decimal = POW(10, -slider_decimal)");
}

function enlargeValueMultistoreFields($setup)
{
    $table = $setup->getTable('brander_layerednavigation/value');

    $setup->run("ALTER TABLE {$table} CHANGE COLUMN `title` `title` TEXT NOT NULL");
    $setup->run("ALTER TABLE {$table} CHANGE COLUMN `meta_title` `meta_title` TEXT NOT NULL");
    $setup->run("ALTER TABLE {$table} CHANGE COLUMN `meta_descr` `meta_descr` TEXT");
    $setup->run("ALTER TABLE {$table} CHANGE COLUMN `meta_kw` `meta_kw` TEXT NOT NULL");
}

alterPageMultipleStores($this);
alterSliderStep($this);
enlargeValueMultistoreFields($this);

$this->endSetup();
