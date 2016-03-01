<?php
/**
 * NOTICE OF LICENSE
 *
 * You may not give, sell, distribute, sub-license, rent, lease or lend
 * any portion of the Software or Documentation to anyone.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   ET
 * @package    ET_SocialLogin
 * @copyright  Copyright (c) 2013 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-commercial-v1/   ETWS Commercial License (ECL1)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('et_social_customer')}`;
CREATE TABLE `et_social_customer` (
	`id` int NOT NULL AUTO_INCREMENT,
	`social_provider` varchar(255),
	`social_customer_id` varchar(255) NULL DEFAULT NULL,
	`customer_id` int,
	`website_id` int,
	`social_profile_link` varchar(255) NULL,
	`social_name`  varchar(255) NULL,
	`social_photo`  varchar(255) NULL,
	PRIMARY KEY (`id`),
	INDEX `social_customer_id_idx` USING BTREE (social_customer_id),
	INDEX `social_provider_idx` USING BTREE (social_provider),
	INDEX `customer_id_idx` (customer_id),
	  UNIQUE KEY `social_account_idx` (`social_provider`,`social_customer_id`) COMMENT 'unique social account'
) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");

$installer->endSetup();