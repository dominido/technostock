<?php
class Brander_AdminForms_Model_Entity_Setup_Builder extends Brander_AdminForms_Model_Entity_Setup_Builder_Abstract
{
    protected $_entities = array();
    protected $_templates = array();

    /**
     * Add new entity to builder
     *
     * @param string|array $entity - is string than entity = entity type
     * @return Brander_AdminForms_Model_Entity_Setup_Builder_Entity
     */
    public function addEntity($entity)
    {
        $entityBuilder = $this->getEntityBuilder($entity);
        $entityType = $entityBuilder->getEntityType();
    
        if (isset($this->_entities[$entityType])) {
            return $this->_entities[$entityType];
        }
        
        $this->_entities[$entityType] = $entityBuilder;
        
        return $entityBuilder;
    }

    /**
     * Returns initialized entity builder model
     *
     * @param string|array $entity
     * @return Brander_AdminForms_Model_Entity_Setup_Builder_Entity
     */ 
    public function getEntityBuilder($entity)
    {
        return Mage::getModel('adminforms/entity_setup_builder_entity')->setEntity($entity);
    }

    /**
     * Returns array-based builder's data
     *
     * @return array
     */ 
    public function getSource()
    {
        $source = array();

        foreach ($this->_entities as $entityType => $entity) {
            $source[$entityType] = $entity->getSource();
        }

        return $source;
    }

    /**
     * Clear builder data
     *
     * @return Brander_AdminForms_Model_Entity_Setup_Builder
     */
    public function clear()
    {
        $this->_entities = array();
        return $this;
    }

    /**
     * Setup builder using xml config.
     *
     * @param Varien_Simplexml_Config $xml
     * @return Brander_AdminForms_Model_Entity_Setup_Builder
     */
    public function setXml(Varien_Simplexml_Config $xml)
    {
        $entitiesNode = $xml->getNode('entities');
        if (!$entitiesNode) {
            return $this;
        }
        
        $entities = $entitiesNode->asArray();
        
        $this->setArray($entities);

        return $this;
    }

    public function setArray($entities = array())
    {
        foreach ($entities as $entityType => $entity) {
            $entityBuilder = $this->addEntity($this->_prepareEntity($entityType, $entity));

            foreach ($entity as $key => $value) {
                if ($key == 'groups') {
                    foreach ($entity[$key] as $groupCode => $group) {
                        $group['name']          = isset($group['name']) ? $group['name'] : $this->getNameFromCode($groupCode);
                        $group['sort_order']    = isset($group['sort_order']) ? $group['sort_order'] : null;
                        $group['attributes']    = isset($group['attributes']) ? $group['attributes'] : array();
                        
                        $entityBuilder->addGroup($group['name'], $group['sort_order']);
                        
                        $this->appendAttributes($entityBuilder, $group['attributes']);
                    }
                } elseif ($key == 'attributes') {
                    $this->appendAttributes($entityBuilder, $value);
                } elseif ($key == 'grid') {
                    $entityBuilder->addGrid($value);
                } elseif ($key == 'grids') {
                    $grids = (array) $value;

                    foreach ($grids as $gridKey => $gridOptions) {
                        $gridOptions['block_key'] = $gridKey;
                        $entityBuilder->addGrid($gridOptions);
                    }
                } elseif ($key == 'sets') {
                    $sets = (array) $value;

                    $setSortOrder = 10;
                    foreach ($sets as $setCode => $setOptions) {
                        if (!isset($setOptions['sort_order'])) {
                            $setOptions['sort_order'] = $setSortOrder;
                            $setSortOrder += 10;
                        }
                        $entityBuilder->addAttributeSet($this->getNameFromCode($setCode), $setOptions);
                    }
                }
            }
        }

        return $this;
    }

    protected function _prepareEntity($entityType, $entity)
    {
        $unset = array('attributes', 'grid', 'set', 'sets', 'groups', 'group');
        foreach ($unset as $field) {
            if (isset($entity[$field])) {
                unset($entity[$field]);
            }
        }

        $entity = array_merge(array('entity_type' => $entityType), $entity);

        return $entity;
    }

    public function addTemplate($name, array $data)
    {
        $this->_templates[$name] = $data;
        return $this;
    }

    public function addTemplates($templates)
    {
        $this->_templates = array_merge($this->_templates, $templates);
        return $this;
    }

    /**
     * Append attributes to entity builder.
     *
     * @param Brander_AdminForms_Model_Entity_Setup_Builder_Entity $entityBuilder
     * @param array $attributes
     * @return Brander_AdminForms_Model_Entity_Setup_Builder
     */
    public function appendAttributes($entityBuilder, $attributes = array())
    {
        foreach ($attributes as $code => $params) {
            if (isset($params['template'])) {
                if (isset($this->_templates[$params['template']])) {
                    $params = array_merge($this->_templates[$params['template']], $params);
    
                    $entityBuilder->addAttribute($code, $params);
                } else {
                    $entityBuilder->addTemplate($params['template'], $code, $params);
                }
            } else {
                $entityBuilder->addAttribute($code, $params);
            }
        }

        return $this;
    }
}
