<?php
class Brander_AdminForms_Model_Entity_Setup_Builder_Entity extends Brander_AdminForms_Model_Entity_Setup_Builder_Abstract
{
    CONST DEFAULT_GRID_BLOCK = 'adminforms/adminhtml_block_grid_standard';
    
    protected $_entity = array();
    
    protected $_attributes = array();
    protected $_group = array();
    protected $_groupSortOrder = 10;
    protected $_sortOrder = 10;

    protected $_grids = array();
    protected $_attributeSets = array();

    
    protected $_templateBuilder;

    /**
     * Return default adminforms entity setup
     *
     * @return array
     */
    public function getDefaultEntity()
    {
        return array(
            'entity_type'                => '',
            'entity_model'               => 'adminforms/block',
            'attribute_model'            => 'adminforms/entity_attribute',
            'additional_attribute_table' => 'adminforms/eav_attribute'
        );
    }

    /**
     * Set entity setup
     *
     * @param string|array $entity
     * @return Brander_AdminForms_Model_Entity_Setup_Builder_Entity
     */
    public function setEntity($entity)
    {
        if (!is_array($entity)) {
            $entity = array('entity_type' => $entity);
        }

        $this->_entity = $this->_appendDefault($entity, $this->getDefaultEntity());

        return $this;
    }

    /**
     * Set attributes current group
     *
     * @param string $group
     * @param integer|null $sortOrder
     * @return Brander_AdminForms_Model_Entity_Setup_Builder_Entity
     */
    public function setGroup($group, $sortOrder = null)
    {
        if (is_null($sortOrder)) {
            $sortOrder = $this->_groupSortOrder;
            $this->_groupSortOrder += 10;
        }
        
        $this->_group = array(
            'name'       => $group,
            'sort_order' => $sortOrder
        );
        
        return $this;
    }

    /**
     * Add group to attributes
     *
     * @param string $group
     * @param integer|null $sortOrder
     * @return Brander_AdminForms_Model_Entity_Setup_Builder_Entity
     */
    public function addGroup($group, $sortOrder = null)
    {
        return $this->setGroup($group, $sortOrder);
    }

    /**
     * Returns entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return $this->_entity['entity_type'];
    }

    /**
     * Returns array-based entity builder's data
     *
     * @return array
     */
    public function getSource()
    {
        $entity = $this->_entity;

        $source = array(
            'entity_model'               => $entity['entity_model'],
            'attribute_model'            => $entity['attribute_model'],
            'additional_attribute_table' => $entity['additional_attribute_table'],
            'attributes'                 => $this->_attributes,
            'grids'                      => $this->_grids,
            'attribute_sets'             => $this->_attributeSets
        );

        return $source;
    }

    /**
     * Add new grid data
     *
     * @param array $params -
     *      array(
     *          'block_key' => string|null,
     *          'fields' => array|null,
     *          'block' => string|null, - grid block, if null than takes default grid
     *          'set'   => string|null  - attribute set
     *      )
     * @return Brander_AdminForms_Model_Entity_Setup_Builder_Entity
     */
    public function addGrid($params = array())
    {
        $blockKey   = isset($params['block_key']) ? $params['block_key'] : $this->getEntityType(); 
        $fields     = isset($params['fields']) ? $params['fields'] : null;
        $block      = isset($params['block']) ? $params['block'] : self::DEFAULT_GRID_BLOCK;
        $set        = isset($params['set']) ? $params['set'] : null;

        $options = array(
            'fields' => $fields,
            'set'    => $set
        );

        $this->_grids[$blockKey] = array(
            'key'                 => $blockKey,
            'entity_type'         => $this->getEntityType(),
            'entity_id'           => null,
            'entity_grid_block'   => $block,
            'entity_grid_options' => $options
        );

        return $this;
    }

    /**
     * Add new attribute set to builder
     *
     * @param string $name
     * @param array $options
     *      array(
     *          'name' => string|null,
     *          'sort_order' => integer|null,
     *          'groups' => array(
     *              groupCode => array(
     *                  'name' => string|null,
     *                  'sort_order' => integer|null,
     *                  'attributes' => array(
     *                      attributeCode => array(
     *                          'sort_order' => integer|null
     *                      )
     *                  )
     *              )
     *          )
     *      )
     * @return Brander_AdminForms_Model_Entity_Setup_Builder_Entity
     */
    public function addAttributeSet($name, $options = array())
    {
        $name = isset($options['name']) ? $options['name'] : $name;
        $sortOrder = isset($options['sort_order']) ? $options['sort_order'] : 0;

        if (isset($options['groups'])) {
            $groups = $options['groups'];
        } else {
            $groups = array(
                'general' => array(
                    'name' => 'General',
                    'attributes' => $options['attributes']
                )
            );
        }

        $groupSortOrder = 10;

        $formatGroups = array();
        
        foreach ($groups as $groupCode => $groupOptions) {
            if (!isset($groupOptions['sort_order'])) {
                $groupOptions['sort_order'] = $groupSortOrder;
                $groupSortOrder += 10;
            }

            if (!isset($groupOptions['name'])) {
                $groupOptions['name'] = $this->getNameFromCode($groupCode);
            }

            $attrSortOrder = 10;
            foreach ($groupOptions['attributes'] as $attrCode => $attrOptions) {
                if (!isset($attrOptions['sort_order'])) {
                    $groupOptions['attributes'][$attrCode]['sort_order'] = $attrSortOrder;
                    $attrSortOrder += 10;
                }
            }

            $formatGroups[$groupOptions['name']] = $groupOptions;
        }

        $options = array(
            'sort_order' => $sortOrder,
            'groups'     => $formatGroups
        );
        
        $this->_attributeSets[$name] = $options;

        return $this;
    }

    /**
     * Append new attribute to builder
     * 
     * @param string $code
     * @param array $params
     *  Default params:
     *      array(
     *          'backend' => '',
     *          'type' => 'varchar',
     *          'table' => '',
     *          'frontend' => '',
     *          'input' => 'text',
     *          'label' => '',
     *          'frontend_class' => '',
     *          'source' => '',
     *          'required' => 0,
     *          'user_defined' => 1,
     *          'default' => '',
     *          'unique' => 0,
     *          'note' => ''
     *      )
     * @param array $default
     * @return Brander_AdminForms_Model_Entity_Setup_Builder_Entity
     */
    public function addAttribute($code, $params, $default = array())
    {
        $params = $this->_appendDefault($params, array(
            'user_defined' => 1,
            'required'     => 0
        ));

        $params = $this->_appendDefault($params, $default);

        if ($this->_group && empty($params['group'])) {
            $params['group'] = $this->_group['name'];
            $params['group_sort_order'] = $this->_group['sort_order'];
        }

        if (!isset($params['sort_order'])) {
            $params['sort_order'] = $this->_sortOrder;
            $this->_sortOrder += 10;
        }

        if (!isset($params['label'])) {
            $params['label'] = $this->getNameFromCode($code);
        }
        
        $this->_attributes[$code] = $params;
        return $this;
    }

    /**
     * Append new attribute to builder by template
     *
     * @param string $template
     * @param string $code
     * @param array $params
     * @return Brander_AdminForms_Model_Entity_Setup_Builder_Entity
     */
    public function addTemplate($template, $code, $params = array())
    {
        $template = $this->getTemplateBuilder()->getTemplate($template);

        return $this->addAttribute($code, $params, $template);
    }

    public function getTemplateBuilder()
    {
        if (is_null($this->_templateBuilder)) {
            $this->_templateBuilder = Mage::getModel('adminforms/entity_setup_builder_template');
        }

        return $this->_templateBuilder;
    }
}
