<?php
/**
 * Brander MarketExport extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        MarketExport
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('market_export')} ADD COLUMN utm_source VARCHAR( 255 ) NULL DEFAULT NULL AFTER utm_medium");
$installer->run("ALTER TABLE {$this->getTable('market_export')} ADD COLUMN utm_term VARCHAR( 255 ) NULL DEFAULT NULL AFTER utm_source");
$installer->run("ALTER TABLE {$this->getTable('market_export')} ADD COLUMN utm_campaign VARCHAR( 255 ) NULL DEFAULT NULL AFTER utm_term");

$installer->endSetup();
