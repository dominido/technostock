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

$installer->run("ALTER TABLE {$table} ADD COLUMN notify_admin INT( 2 ) NOT NULL DEFAULT '1'");

$installer->endSetup();
