<?php
/**
 * Brander CustomerCallbacks extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        CustomerCallbacks
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
$installer = $this;

$installer->startSetup();
$table = $this->getTable('brander_customercallbacks/callbacks');

$installer->run("ALTER TABLE {$table} ADD COLUMN comment VARCHAR( 255 ) NULL DEFAULT NULL");
$installer->run("ALTER TABLE {$table} ADD COLUMN current_url VARCHAR( 255 ) NULL DEFAULT NULL");
$installer->run("ALTER TABLE {$table} ADD COLUMN status INT(2) NULL DEFAULT '0'");
$installer->run("ALTER TABLE {$table} ADD COLUMN created_at DATETIME NULL DEFAULT NULL");
$installer->run("ALTER TABLE {$table} ADD COLUMN modify_at DATETIME NULL DEFAULT NULL");

$installer->endSetup();
