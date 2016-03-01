<?php
class Brander_AdminForms_Model_Entity_Block_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{

    protected $_storeId = null;

    /**
     * Collection constructor
     *
     * @param Mage_Core_Model_Mysql4_Abstract $resource
     */
    public function __construct($resource=null)
    {
        $this->setEntity($resource);
        parent::__construct();
    }

    /**
     * Standard resource collection initalization
     *
     * @param string $model
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    protected function _init($model, $entityModel=null)
    {
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName($model));
        //->setEntityType($this->getEntity()->getEntityType()->getEntityTypeCode())
        if (empty($this->_entity)) {
            if (is_null($entityModel)) {
                $entityModel = $model;
            }
            $entity = Mage::getResourceSingleton($entityModel);
            $this->setEntity($entity);
        }
        return $this;
    }

    /**
     * Add an object to the collection
     *
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addItem(Varien_Object $object)
    {
        if (get_class($object)!== $this->_itemObjectClass) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Attempt to add an invalid object.'));
        }
        $object->setEntityType($this->getEntity()->getEntityType()->getEntityTypeCode());
        return parent::addItem($object);
    }

    protected function _construct()
    {
        $this->_init('adminforms/block');
    }

    public function setStore($store)
    {
        $this->setStoreId(Mage::app()->getStore($store)->getId());
        return $this;
    }

    public function setStoreId($storeId)
    {
        if ($storeId instanceof Mage_Core_Model_Store) {
            $storeId = $storeId->getId();
        }
        $this->_storeId = $storeId;
        return $this;
    }

    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }
        return $this->_storeId;
    }

    public function getDefaultStoreId()
    {
        return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    }

    /**
     * Retrieve attributes load select
     *
     * @param string $table
     * @param array|int $attributeIds
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getLoadAttributesSelect($table, $attributeIds = array())
    {
        if (empty($attributeIds)) {
            $attributeIds = $this->_selectAttributes;
        }
        $storeId = $this->getStoreId();

        if ($storeId) {
            $adapter        = $this->getConnection();
            $entityIdField  = $this->getEntity()->getEntityIdField();

            if (version_compare(Mage::getVersion(), '1.6.0') > 0) {
                $joinCondition  = array(
                    't_s.attribute_id = t_d.attribute_id',
                    't_s.entity_id = t_d.entity_id',
                    $adapter->quoteInto('t_s.store_id = ?', $storeId)
                );
                $select = $adapter->select()
                    ->from(array('t_d' => $table), array($entityIdField, 'attribute_id'))
                    ->joinLeft(
                        array('t_s' => $table),
                        implode(' AND ', $joinCondition),
                        array())
                    ->where('t_d.entity_type_id = ?', $this->getEntity()->getTypeId())
                    ->where("t_d.{$entityIdField} IN (?)", array_keys($this->_itemsById))
                    ->where('t_d.attribute_id IN (?)', $attributeIds)
                    ->where('t_d.store_id = ?', 0);
            } else {
                $joinCondition = 'store.attribute_id=default.attribute_id
                    AND store.entity_id=default.entity_id
                    AND store.store_id='.(int) $this->getStoreId();

                $select = $this->getConnection()->select()
                    ->from(array('default'=>$table), array($entityIdField, 'attribute_id', 'default_value'=>'value'))
                    ->joinLeft(
                        array('store'=>$table),
                        $joinCondition,
                        array(
                            'store_value' => 'value',
                            'value' => new Zend_Db_Expr('IF(store.value_id>0, store.value, default.value)')
                        )
                    )
                    ->where('default.entity_type_id=?', $this->getEntity()->getTypeId())
                    ->where("default.$entityIdField in (?)", array_keys($this->_itemsById))
                    ->where('default.attribute_id in (?)', $attributeIds)
                    ->where('default.store_id = 0');
            }
        } else {
            $select = parent::_getLoadAttributesSelect($table)
                ->where('store_id = ?', $this->getDefaultStoreId());
        }

        return $select;
    }

    /**
     * @param Varien_Db_Select $select
     * @param string $table
     * @param string $type
     * @return Varien_Db_Select
     */
    protected function _addLoadAttributesSelectValues($select, $table, $type)
    {
        $storeId = $this->getStoreId();
        if ($storeId) {
            $helper = Mage::getResourceHelper('eav');
            $adapter        = $this->getConnection();
            $valueExpr      = $adapter->getCheckSql(
                't_s.value_id IS NULL',
                $helper->prepareEavAttributeValue('t_d.value', $type),
                $helper->prepareEavAttributeValue('t_s.value', $type)
            );

            $select->columns(array(
                'default_value' => $helper->prepareEavAttributeValue('t_d.value', $type),
                'store_value'   => $helper->prepareEavAttributeValue('t_s.value', $type),
                'value'         => $valueExpr
            ));
        } else {
            $select = parent::_addLoadAttributesSelectValues($select, $table, $type);
        }
        return $select;
    }

    /**
     * Adding join statement to collection select instance
     *
     * @param string $method
     * @param object $attribute
     * @param string $tableAlias
     * @param array $condition
     * @param string $fieldCode
     * @param string $fieldAlias
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias)
    {
        if (isset($this->_joinAttributes[$fieldCode]['store_id'])) {
            $store_id = $this->_joinAttributes[$fieldCode]['store_id'];
        } else {
            $store_id = $this->getStoreId();
        }

        $adapter = $this->getConnection();

        if ($store_id != $this->getDefaultStoreId() && !$attribute->isScopeGlobal()) {
            /**
             * Add joining default value for not default store
             * if value for store is null - we use default value
             */
            $defCondition = '('.join(') AND (', $condition).')';
            $defAlias     = $tableAlias.'_default';
            $defFieldCode = $fieldCode.'_default';
            $defFieldAlias= str_replace($tableAlias, $defAlias, $fieldAlias);

            $defCondition = str_replace($tableAlias, $defAlias, $defCondition);
            $defCondition.= $adapter->quoteInto(
                " AND " . $adapter->quoteColumnAs("$defAlias.store_id", null) . " = ?",
                $this->getDefaultStoreId());

            $this->getSelect()->$method(
                array($defAlias => $attribute->getBackend()->getTable()),
                $defCondition,
                array()
            );

            $method = 'joinLeft';
            $fieldAlias = new Zend_Db_Expr("IF($tableAlias.value_id > 0, $fieldAlias, $defFieldAlias)");
            $this->_joinAttributes[$fieldCode]['condition_alias'] = $fieldAlias;
            $this->_joinAttributes[$fieldCode]['attribute']       = $attribute;
        } else {
            $store_id = $this->getDefaultStoreId();
        }

        $condition[] = $adapter->quoteInto(
            $adapter->quoteColumnAs("$tableAlias.store_id", null) . ' = ?', $store_id);
        return parent::_joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias);
    }
}
