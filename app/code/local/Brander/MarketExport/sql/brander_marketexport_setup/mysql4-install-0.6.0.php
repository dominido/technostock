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

/**
 * @var $this Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS {$this->getTable('market_export')}");
$installer->run("CREATE TABLE {$this->getTable('market_export')} (
  entity_id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) character set utf8 NOT NULL,
  path VARCHAR(255) character set utf8 NOT NULL DEFAULT '/var/exports/',
  type INT(11) NOT NULL,
  shopname VARCHAR(255) NULL,
  companyname VARCHAR(255) NULL,
  stores VARCHAR(255) NOT NULL DEFAULT '0',
  description TEXT character set utf8 NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  is_active INT(1) NOT NULL,
  min_price DECIMAL NULL DEFAULT NULL,
  max_price DECIMAL NULL DEFAULT NULL,
  rating INT NULL DEFAULT NULL,
  categories TEXT,
  include_out_of_stock INT(1) NOT NULL,
  use_utm INT(1) NOT NULL DEFAULT  '0',
  utm_medium VARCHAR(255) NOT NULL DEFAULT  'price_list',
  custom_attributes VARCHAR(255) NULL,
  custom_attributes_data VARCHAR(255),
  PRIMARY KEY (entity_id))
  ENGINE = InnoDB DEFAULT CHARSET=utf8;
");
/*$installer->run("ALTER TABLE {$this->getTable('market_export')} ADD COLUMN created_at DATETIME NOT NULL");
$installer->run("ALTER TABLE {$this->getTable('market_export')} ADD COLUMN updated_at DATETIME NOT NULL");
$installer->run("ALTER TABLE {$this->getTable('market_export')} ADD COLUMN is_active INT(1) NOT NULL");
$installer->run("ALTER TABLE {$this->getTable('market_export')} ADD COLUMN min_price INT(11) NULL");
$installer->run("ALTER TABLE {$this->getTable('market_export')} ADD COLUMN max_price INT(11) NULL");
$installer->run("ALTER TABLE {$this->getTable('market_export')} MODIFY min_price DECIMAL NULL DEFAULT NULL");
$installer->run("ALTER TABLE {$this->getTable('market_export')} MODIFY max_price DECIMAL NULL DEFAULT NULL");
$installer->run("ALTER TABLE {$this->getTable('market_export')} ADD COLUMN rating INT NULL DEFAULT NULL");
$installer->run("ALTER TABLE {$this->getTable('market_export')} ADD COLUMN categories TEXT");
$installer->run("ALTER TABLE {$this->getTable('market_export')} ADD COLUMN include_out_of_stock INT(1) NOT NULL");

$installer->run("ALTER TABLE {$this->getTable('market_export')} ADD COLUMN use_utm INT( 1 ) NOT NULL DEFAULT  '0'");
$installer->run("ALTER TABLE {$this->getTable('market_export')} ADD COLUMN utm_medium VARCHAR( 255 ) NOT NULL DEFAULT  'price_list'");
$installer->run("ALTER TABLE {$this->getTable('market_export')} ADD COLUMN shopname VARCHAR( 255 ) NULL AFTER type");
$installer->run("ALTER TABLE {$this->getTable('market_export')} ADD COLUMN companyname VARCHAR( 255 ) NULL AFTER shopname");
$installer->run("ALTER TABLE {$this->getTable('market_export')} ADD COLUMN stores VARCHAR( 255 ) NOT NULL DEFAULT '0' AFTER companyname");
$installer->run("ALTER TABLE {$this->getTable('market_export')} ADD COLUMN custom_attributes VARCHAR( 255 ) NULL ");
$installer->run("ALTER TABLE {$this->getTable('market_export')} ADD COLUMN custom_attributes_data VARCHAR( 255 ) NULL");*/
/*$installer->getConnection()->addColumn(
    $this->getTable('marketexport/export'),
    'categories',
    'text'
);*/


$installer->addAttribute('catalog_category', 'utm_bid', array(
        'label'    => 'UTM-Bid',
        'type'     => 'varchar',
        'input'    => 'text',
        'required' => false,
        'default'  => '',
        'group'    => 'General Information',
        'position' => 1010,
    ));

/*$installer->getConnection()->addColumn(
    $installer->getTable('marketexport/export'),
    'use_warranty', 'TINYINT(1) NOT NULL DEFAULT 0'
);

$installer->getConnection()->addColumn(
    $installer->getTable('marketexport/export'),
    'use_part_number', 'TINYINT(1) NOT NULL DEFAULT 0'
);

$installer->getConnection()->addColumn(
    $this->getTable('marketexport/export'),
    'delivery_options',
    'text'
);*/



$installer->endSetup();

