<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$attrGroupName = 'General';
$attrNote = '';

$attrCode = 'height';
$attrLabel = 'Height';

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrId = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attrCode);

if ($attrId === false) {
	$objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode, array(
		'group' => $attrGroupName,
		'sort_order' => 7,
		'type' => 'decimal',
		'backend' => '',
		'frontend' => '',
		'label' => $attrLabel,
		'note' => $attrNote,
		'input' => 'text',
		'class' => '',
		'source' => '',
		'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'visible' => true,
		'required' => false,
		'user_defined' => false,
		'default' => '0',
		'visible_on_front' => false,
		'unique' => false,
		'is_configurable' => false,
		'used_for_promo_rules' => true
	));
}

$attrCode = 'width';
$attrLabel = 'Width';

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrId = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attrCode);

if ($attrId === false) {
	$objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode, array(
		'group' => $attrGroupName,
		'sort_order' => 7,
		'type' => 'decimal',
		'backend' => '',
		'frontend' => '',
		'label' => $attrLabel,
		'note' => $attrNote,
		'input' => 'text',
		'class' => '',
		'source' => '',
		'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'visible' => true,
		'required' => false,
		'user_defined' => false,
		'default' => '0',
		'visible_on_front' => false,
		'unique' => false,
		'is_configurable' => false,
		'used_for_promo_rules' => true
	));
}

$attrCode = 'depth';
$attrLabel = 'Depth';

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrId = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attrCode);
//if ($attrId)
//	Mage::getModel('catalog/product_attribute_set_api')->attributeRemove($attrId, $set->getId());
if ($attrId === false) {
	$objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode, array(
		'group' => $attrGroupName,
		'sort_order' => 7,
		'type' => 'decimal',
		'backend' => '',
		'frontend' => '',
		'label' => $attrLabel,
		'note' => $attrNote,
		'input' => 'text',
		'class' => '',
		'source' => '',
		'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'visible' => true,
		'required' => false,
		'user_defined' => false,
		'default' => '0',
		'visible_on_front' => false,
		'unique' => false,
		'is_configurable' => false,
		'used_for_promo_rules' => true
	));
}

$installer->getConnection()->dropTable(Mage::getSingleton('core/resource')->getTableName('brander_newpost/locale'));
$table_locale = $installer->getConnection()
	->newTable($installer->getTable('brander_newpost/locale'))
	->addColumn('locale_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true,
	), 'Id')
	->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
		'nullable'  => false,
	), 'Locale name')
	->addColumn('magento_store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'nullable'  => false,
	), 'Store ID');

$installer->getConnection()->createTable($table_locale);

$installer->getConnection()->dropTable(Mage::getSingleton('core/resource')->getTableName('brander_newpost/city'));
$table_city = $installer->getConnection()
	->newTable($installer->getTable('brander_newpost/city'))
	->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true,
	), 'Id')
	->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
		'nullable'  => false,
	), 'City name')
	->addColumn('city_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'nullable'  => false,
	), 'City ID')
	->addColumn('locale_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'nullable'  => false,
	), 'Locale ID')
	->addColumn('timestamp', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
		'nullable'  => false,
		'default'  => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE,
	), 'Timestamp');

$installer->getConnection()->createTable($table_city);

$installer->getConnection()->dropTable(Mage::getSingleton('core/resource')->getTableName('brander_newpost/address'));
$table_address = $installer->getConnection()
	->newTable($installer->getTable('brander_newpost/address'))
	->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true,
	), 'Id')
	->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
		'nullable'  => false,
	), 'Address')
	->addColumn('warehouse_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'nullable'  => false,
	), 'Warehouse ID')
	->addColumn('locale_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'nullable'  => false,
	), 'Locale ID');

$installer->getConnection()->createTable($table_address);

$installer->getConnection()->dropTable(Mage::getSingleton('core/resource')->getTableName('brander_newpost/warehouse'));
$table_warehouse = $installer->getConnection()
	->newTable($installer->getTable('brander_newpost/warehouse'))
	->addColumn('warehouse_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => false,
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true,
	), 'Id')
	->addColumn('city_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
		'nullable'  => false,
	), 'City ID')
	->addColumn('phone', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
		'nullable'  => true,
	), 'Phone')
	->addColumn('max_weight_allowed', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'nullable'  => false,
		'default'   => 0,
	), 'Max weight')
	->addColumn('weekday_work_hours', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
		'nullable'  => true,
	), 'Weekday work hours')
	->addColumn('weekday_receiving_hours', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
		'nullable'  => true,
	), 'Weekday receiving hours')
	->addColumn('weekday_delivery_hours', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
		'nullable'  => true,
	), 'Saturday delivery hours')
	->addColumn('saturday_work_hours', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
		'nullable'  => true,
	), 'Saturday work hours')
	->addColumn('saturday_receiving_hours', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
		'nullable'  => true,
	), 'Saturday receiving hours')
	->addColumn('saturday_delivery_hours', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
		'nullable'  => true,
	), 'Weekday delivery hours')
	->addColumn('x', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
		'nullable'  => true,
	), 'Coordinate X')
	->addColumn('y', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
		'nullable'  => true,
	), 'Coordinate Y')
	->addColumn('timestamp', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
		'nullable'  => false,
		'default'  => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE,
	), 'Timestamp');

$installer->getConnection()->createTable($table_warehouse);

$data = array('name'=>'Русский', 'magento_store_id'=>Mage::app()->getStore()->getId());
Mage::getModel('brander_newpost/locale')->setData($data)->save();
$data = array('name'=>'Украинский', 'magento_store_id'=>Mage::app()->getStore()->getId());
Mage::getModel('brander_newpost/locale')->setData($data)->save();

$installer->endSetup();