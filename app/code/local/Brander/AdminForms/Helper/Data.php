<?php
class Brander_AdminForms_Helper_Data extends Mage_Core_Helper_Data{

    public function getAttribute($blockKey, $attribute){
        $data = Mage::getResourceModel('adminforms/block')->getEntityInfo($blockKey);
        if (is_array($data)){
            $entityType = $data['entity_type'];
            return Mage::getSingleton('adminforms/block', array('entity_type'=>$entityType))->getResource()->getAttribute($attribute);
        }
        return null;
    }

    public function getUrl($url){
        if (preg_match('/^https?:/i', $url))
            return $url;
        $elements = explode('?', $url, 2);
        $url = $elements[0];
        $query = count($elements) > 1 ? '?'.$elements[1] : '';
        return Mage::getUrl($url) . $query;
    }

    public function getModel($entityType)
    {
        return Mage::getModel('adminforms/block', array('entity_type' => $entityType));
    }

    public function getCollection($entityType, $select = '*', $isStatus = true)
    {
        //fix for model getCollection unexpected duplicate
        $resourceModel = Mage::getResourceModel('adminforms/block', array('entity_type' => $entityType))->setType($entityType);
        $collection = Mage::getResourceModel('adminforms/block_collection', $resourceModel);

        if ($select) {
            $collection->addAttributeToSelect($select);
        }

        if ($isStatus) {
            $collection->addAttributeToFilter('status', 1);
        }

        return $collection;
    }

    public function getEntityTypeByBlock($blockKey)
    {
        $data = $this->getEntityInfo($blockKey);
        return isset($data['entity_type']) ? $data['entity_type'] : null;
    }

    public function getEntityInfo($blockKey)
    {
        return Mage::getResourceModel('adminforms/block')->getEntityInfo($blockKey);
    }

    public function getBlockGrid($blockKey)
    {
        $entityInfo = $this->getEntityInfo($blockKey);
        
        if(is_array($entityInfo) && @$entityInfo['entity_grid_block']) {
            return $entityInfo['entity_grid_block'];
        }

        return;
    }

    public function getGridOptions($blockKey)
    {
        $entityInfo = $this->getEntityInfo($blockKey);

        if (!isset($entityInfo['entity_grid_options'])) {
            return;
        }

        return unserialize($entityInfo['entity_grid_options']);
    }

}
