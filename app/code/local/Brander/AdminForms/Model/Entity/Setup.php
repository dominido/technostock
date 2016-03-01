<?php
class Brander_AdminForms_Model_Entity_Setup extends Mage_Eav_Model_Entity_Setup
{
    CONST ADMINFORMS_CONFIG = 'adminforms.xml';

    public function getDefaultEntities()
    {
        return array();
    }
    /**
     * Process builder data
     *
     * @param Brander_AdminForms_Model_Entity_Setup_Builder $builder
     * @return Brander_AdminForms_Model_Entity_Setup
     */
    public function processBuilder($builder)
    {
        if (!$builder) {
            return;
        }
        
        $source = $builder->getSource();

        if ($reservedEntity = $this->checkEntity(array_keys($source))) {
            Mage::throwException("Entity type '$reservedEntity' is reserved");
            return;
        }

        foreach ($source as $entityType => $entity) {
            if (!$this->getEntityType($entityType, 'entity_type_id')) {
                $this->addEntityType($entityType, $entity);
            }

            if (!empty($entity['attributes'])){
                $addedAttributes = array();
                $addedAttributeSets = array();

                foreach ($entity['attributes'] as $attributeCode => $params) {
                    $addedAttributes[] = $attributeCode;

                    if (!empty($params['group'])) {
                        $group = $params['group'];
                        $groupSortOrder = $params['group_sort_order'];

                        unset($params['group']);
                        unset($params['group_sort_order']);
                    }

                    $this->addAttribute($entityType, $attributeCode, $params);

                    if (empty($entity['attribute_sets'])) {
                        $setId = 'Default';

                        if (!$this->getAttributeSet($entityType, $setId)) {
                            $this->addAttributeSet($entityType, $setId);
                        }

                        if (isset($group)) {
                            $this->addAttributeGroup($entityType, $setId, $group, $groupSortOrder);
                            $groupId = $this->getAttributeGroupId($entityType, $setId, $group);
                        } else {
                            $groupId = 'General';
                        }

                        $addedAttributeSets[$setId] = '';

                        $this->addAttributeToSet($entityType, $setId, $groupId, $attributeCode, @$params['sort_order']);
                    }
                    // Fix DefaultSetToEntityType
                    $this->setDefaultSetToEntityType($entityType); 
                }
            }

            //add attribute sets
            if (isset($entity['attribute_sets'])) {
                foreach ($entity['attribute_sets'] as $setId => $setOptions) {
                    $this->addAttributeSet($entityType, $setId, $setOptions['sort_order']);

                    $addedAttributeSets[$setId] = '';

                    $setGroups = $setOptions['groups'];
                    foreach ($setGroups as $groupName => $groupOptions) {
                        $this->addAttributeGroup($entityType, $setId, $groupName, $groupOptions['sort_order']);

                        $setAttributes = array();
                        foreach ($groupOptions['attributes'] as $attrCode => $attrOptions) {
                            $this->addAttributeToSet($entityType, $setId, $groupName, $attrCode, $attrOptions['sort_order']);
                            $addedSetAttributeIds[] = $this->getAttributeId($entityType, $attrCode);
                        }
                    }

                    $setAttributeIds = $this->getAttributeSetAttributeIds($entityType, $setId);
                    foreach ($setAttributeIds as $id) {
                        if (!in_array($id, $addedSetAttributeIds)) {
                            $this->removeAttributeFromSet($entityType, $id, $setId);
                        }
                    }
                }
            }

            foreach ($entity['grids'] as $grid) {
                $this->addGrid($grid);
            }

            if (!empty($entity['attributes'])){
                //remove not existing attributes
                $entityAttributes = Mage::getSingleton('eav/config')->getEntityAttributeCodes($entityType);

                foreach ($entityAttributes as $attribute) {
                    if (!in_array($attribute, $addedAttributes)) {
                        $this->removeAttribute($entityType, $attribute);
                    }
                }

                //remove not existing attribute sets
                $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                      ->setEntityTypeFilter(Mage::getSingleton('eav/config')->getEntityType($entityType)->getEntityTypeId())
                      ->load()
                      ->toOptionArray()
                      ;

                foreach ($sets as $set) {
                    if (!isset($addedAttributeSets[$set['label']])) {
                        $this->removeAttributeSet($entityType, $set['value']);
                    }
                }
            }

        }

        return $this;
    }

    public function checkEntity($entities)
    {
        $reserved = array(
            'customer',
            'customer_address',
            'catalog_category',
            'catalog_product',
            'order',
            'invoice',
            'creditmemo',
            'shipment'
        );

        foreach ($entities as $entity) {
            if (in_array($entity, $reserved)) {
                return $entity;
            }
        }

        return;
    }

    /**
     * Returns builder's model
     *
     * @return Brander_AdminForms_Model_Entity_Setup_Builder
     */
    public function getBuilder()
    {
        return Mage::getModel('adminforms/entity_setup_builder');
    }

    /**
     * Add grid to adminforms_block table
     *
     * @param array $params
     *      array(
     *          'key' => string,
     *          'entity_type' => string,
     *          'entity_id' => integer|null,
     *          'entity_grid_block' => string
     *          'entity_grid_options' => array
     *      )
     * @return Brander_AdminForms_Model_Entity_Setup
     */
    public function addGrid($params)
    {
        $key                = isset($params['key']) ? $params['key'] : null;
        $entityType         = isset($params['entity_type']) ? $params['entity_type'] : null;
        $entityId           = is_null($params['entity_id']) ? 'NULL' : $params['entity_id'];
        $entityGridBlock    = isset($params['entity_grid_block']) ? $params['entity_grid_block'] : '';

        if (!$key) {
            Mage::throwException('Block key is required');
        } elseif(!$entityType) {
            Mage::throwException('Entity type is required');
        }

        if (isset($params['entity_grid_options']) && isset($params['entity_grid_options']['set'])) {
            $params['entity_grid_options']['set'] = $this->getAttributeSetId($entityType, $params['entity_grid_options']['set']);
        }

        $entityGridOptions = isset($params['entity_grid_options']) ? serialize($params['entity_grid_options']) : '';

        $sql = "INSERT INTO `{$this->getTable('adminforms_block')}` (`key`,`entity_type`,`entity_id`,`entity_grid_block`,`entity_grid_options`)
                    VALUES ('$key','$entityType',$entityId,'$entityGridBlock','$entityGridOptions')
                ON DUPLICATE KEY UPDATE `entity_type` = '$entityType', `entity_grid_block` = '$entityGridBlock', `entity_grid_options` = '$entityGridOptions'";

        $this->run($sql);

        return $this;
    }

    /**
     * Add Attribute to All Groups on Attribute Set
     *
     * Add saving attribute sort order when attribute is updating.
     * Also removing condition that checking general group for update.
     *
     * @param mixed $entityTypeId
     * @param mixed $setId
     * @param mixed $groupId
     * @param mixed $attributeId
     * @param int $sortOrder
     * @return Mage_Eav_Model_Entity_Setup
     */
    public function addAttributeToSet($entityTypeId, $setId, $groupId, $attributeId, $sortOrder=null)
    {
        $entityTypeId = $this->getEntityTypeId($entityTypeId);
        $setId = $this->getAttributeSetId($entityTypeId, $setId);
        $groupId = $this->getAttributeGroupId($entityTypeId, $setId, $groupId);
        $attributeId = $this->getAttributeId($entityTypeId, $attributeId);
        $generalGroupId = $this->getAttributeGroupId($entityTypeId, $setId, $this->_generalGroupName);

        $oldId = $this->_conn->fetchOne("select entity_attribute_id from ".$this->getTable('eav/entity_attribute')." where attribute_set_id=$setId and attribute_id=$attributeId");

        if ($oldId) {
            if ($groupId) {
                $update = array('attribute_group_id' => $groupId);
                if (!is_null($sortOrder)) {
                    $update['sort_order'] = $sortOrder;
                }
                $condition = $this->_conn->quoteInto('entity_attribute_id = ?', $oldId);
                $this->_conn->update($this->getTable('eav/entity_attribute'), $update, $condition);
            }
            return $this;
        }

        $this->_conn->insert($this->getTable('eav/entity_attribute'), array(
            'entity_type_id'    =>$entityTypeId,
            'attribute_set_id'  =>$setId,
            'attribute_group_id'=>$groupId,
            'attribute_id'      =>$attributeId,
            'sort_order'        =>$this->getAttributeSortOrder($entityTypeId, $setId, $groupId, $sortOrder),
        ));

        return $this;
    }

    /**
     * Prepare attribute values to save
     *
     * Add saving additional attribute data
     *
     * @param array $attr
     * @return array
     */
    protected function _prepareValues($attr)
    {
        $data = parent::_prepareValues($attr);

        $additionalData = array(
            'is_wysiwyg_enabled' => $this->_getValue($attr, 'is_wysiwyg_enabled', 0)
        );

        $data = array_merge($data, $additionalData);

        return $data;
    }

    protected function _modifyResourceDb($actionType, $fromVersion, $toVersion)
    {
        if ($actionType == 'upgrade') {
            $moduleName = (string)$this->_moduleConfig[0]->getName();

            $config = $this->_getAdminformsConfig($moduleName);

            $builder = null;
            if ($config) {
                $builder = $this->getBuilder();
                $this->_prepareBuilder($builder);
                
                $builder->setXml($config);
            }

            $this->processBuilder($builder);  
        }

        return parent::_modifyResourceDb($actionType, $fromVersion, $toVersion);
    }

    protected function _prepareBuilder($builder)
    {
        return $this;
    }

    protected function _getAdminformsConfig($moduleName)
    {
        $config = Mage::getConfig();
        $configFile = $config->getModuleDir('etc', $moduleName) . DS . self::ADMINFORMS_CONFIG;

        if (!file_exists($configFile)) {
            return;
        }

        $model = Mage::getModel('core/config_base');
        $model->loadFile($configFile);

        return $model;
    }

    public function removeAttributeFromSet($entityTypeId, $code, $setId = null)
    {
        $attribute  = $this->getAttribute($entityTypeId, $code);
        if ($attribute) {
            $parentField = $setId ? 'attribute_set_id' : null;
            if ($setId) {
                $this->getAttributeSetId($entityTypeId, $setId);
            }

            $this->deleteTableRow('eav/entity_attribute', 'attribute_id', $attribute['attribute_id'], $parentField, $setId);
        }
    }

    public function getAttributeSetAttributeIds($entityType, $setId)
    {
        $setId = $this->getAttributeSetId($entityType, $setId);

        $sql = "SELECT `attribute_id` FROM `{$this->getTable('eav/entity_attribute')}` WHERE `attribute_set_id` = $setId";

        return $this->_conn->fetchCol($sql);
    }
}
