<?php
class Brander_AdminForms_Model_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Identifuer of default store
     * used for loading default data for entity
     */
    const DEFAULT_STORE_ID = 0;

    /**
     * Get resource instance
     *
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _getResource()
    {
        if (empty($this->_resourceName)) {
            Mage::throwException(Mage::helper('core')->__('Resource is not set.'));
        }

        return Mage::getResourceSingleton($this->_resourceName,array('entity_type'=>$this->getEntityType()))->setType($this->getEntityType());
    }


    /**
     * Get collection instance
     *
     * @return object
     */
    public function getResourceCollection()
    {
        if (empty($this->_resourceCollectionName)) {
            Mage::throwException(Mage::helper('core')->__('Model collection resource name is not defined.'));
        }

        return Mage::getResourceModel($this->_resourceCollectionName, $this->_getResource());
    }

    /**
     * Retrieve attributes
     *
     * if $groupId is null - retrieve all attributes
     *
     * @param   int $groupId
     * @return  array
     */
    public function getAttributes($groupId = null, $skipSuper=false)
    {
        $formAttributes = $this->getSetAttributes();
        if ($groupId) {
            $attributes = array();
            foreach ($formAttributes as $attribute) {
                if ($attribute->isInGroup($this->getAttributeSetId(), $groupId)) {
                    $attributes[] = $attribute;
                }
            }
        }
        else {
            $attributes = $formAttributes;
        }

        return $attributes;
    }

    /**
     * Get array of set attributes
     *
     * @return array
     */
    public function getSetAttributes()
    {
        return $this->getResource()
            ->loadAllAttributes($this)
            ->getSortedAttributes($this->getAttributeSetId());
    }

    /**
     * Retrieve store model instance
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        $storeId = $this->getStoreId();
        if ($storeId) {
            return Mage::app()->getStore($storeId);
        }
        return Mage::app()->getStore();
    }

    public function addAttributeUpdate($code, $value, $store)
    {
        $oldValue = $this->getData($code);
        $oldStore = $this->getStoreId();

        $this->setData($code, $value);
        $this->setStoreId($store);
        $this->getResource()->saveAttribute($this, $code);

        $this->setData($code, $oldValue);
        $this->setStoreId($oldStore);
    }

    /**
     * Processing object after save data
     *
     * @return Brander_AdminForms_Model_Abstract
     */
    protected function _afterSave()
    {
        Mage::dispatchEvent('adminforms_block_save_after', array('block'=>$this));
        Mage::dispatchEvent($this->getEntityType().'_adminforms_block_save_after', array('block'=>$this));
        return $this;
    }

    /**
     * Load entity by attribute
     *
     * @param Mage_Eav_Model_Entity_Attribute_Interface|integer|string|array $attribute
     * @param null|string|array $value
     * @param string $additionalAttributes
     * @return bool|Mage_Catalog_Model_Abstract
     */
    public function loadByAttribute($attribute, $value, $additionalAttributes = '*', $storeId = self::DEFAULT_STORE_ID )
    {
        $collection = $this->getResourceCollection()
            ->setStoreId($storeId)
            ->addAttributeToSelect($additionalAttributes)
            ->addAttributeToFilter($attribute, $value)
            ->setPage(1,1);
        foreach ($collection as $object) {
            return $object;
        }
        return false;
    }

    public function getIdFieldName()
    {
        return 'entity_id';
    }

    /**
     * Get attribute text by its code
     *
     * @param $attributeCode Code of the attribute
     * @return string
     */
    public function getAttributeText($attributeCode)
    {
        return $this->_getResource()
            ->getAttribute($attributeCode)
                ->getSource()
                    ->getOptionText($this->getData($attributeCode));
    }
}
