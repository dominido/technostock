<?php
class Brander_SearchAutocomplete_Model_Mysql4_Setup extends Mage_Core_Model_Resource_Setup {

    public function generateCategorySearchIndexes() {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();

        $entityTypeId = $connection->fetchOne("SELECT `entity_type_id` FROM `".$tablePrefix."eav_entity_type` WHERE `entity_type_code` = 'catalog_category'");
        if (!$entityTypeId) return false;
        $attributeNameId = $connection->fetchOne("SELECT `attribute_id` FROM `".$tablePrefix."eav_attribute` WHERE `attribute_code` = 'name' AND `entity_type_id`=".$entityTypeId);
        if (!$attributeNameId) return false;
        $attributeIsActiveId = $connection->fetchOne("SELECT `attribute_id` FROM `".$tablePrefix."eav_attribute` WHERE `attribute_code` = 'is_active' AND `entity_type_id`=".$entityTypeId);
        if (!$attributeIsActiveId) return false;


        $categories = $connection->fetchAll("SELECT ccev.* FROM `".$tablePrefix."catalog_category_entity_varchar` AS ccev
            LEFT JOIN `".$tablePrefix."catalog_category_entity_int` AS ccei USING(`entity_id`, `store_id`)
            WHERE ccev.`attribute_id` = ".$attributeNameId." AND ccei.`attribute_id` = ".$attributeIsActiveId." AND ccei.`value` = 1");


        if (!$categories || !is_array($categories)) return false;
        foreach ($categories as $cat) {
            if ($cat['value']!='') {
                $data = array(
                    'category_id' => $cat['entity_id'],
                    'store_id' => $cat['store_id'],
                    'data_index' => $cat['value']
                );
                $connection->insert($tablePrefix . 'catalogsearch_category_fulltext', $data);
            }
        }

        // problems with the storeId:
        //$collection = Mage::getModel('catalog/category')->getCollection()->addIsActiveFilter()->addNameToResult();

    }

    public function regenerateFullIndex() {
        // for can reindex
        Mage::app()->reinitStores();
        Mage::app()->getStore(null)->resetConfig();
        Mage::getResourceModel('searchautocomplete/fulltext')->regenerateIndex();
    }

}