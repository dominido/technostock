<?php

class Brander_CommerceML_Model_Import_Product_Attribute extends Varien_Object
{
    const ATTRIBUTE_PREFIX          = 'brandercml/options/created_attr_prefix';
    const DEFAULT_ATTRIBUTE_TYPE    = 'brandercml/options/default_attr_type';

    /**
     * Available attributes type
     */
    protected $_type = array(
        'price' => array(
            'backend' => 'catalog/product_attribute_backend_price',
            'type'    => 'decimal'
        ),
        'text' => array(
            'type'    => 'text'
        ),
        'textarea' => array(
            'type'    => 'text'
        ),
        'date' => array(
            'backend' => 'eav/entity_attribute_backend_datetime',
            'type'    => 'datetime'
        ),
        'select' => array(
            'source'  => 'eav/entity_attribute_source_table',
            'type'    => 'int'
        ),
        'boolean' => array(
            'source'  => 'eav/entity_attribute_source_boolean',
            'type'    => 'int'
        ),
        'multiselect' => array(
            'backend' => 'eav/entity_attribute_backend_array',
            'type'    => 'varchar'
        ),
    );

    public function push($id, $params = array())
    {
        if (!$this->hasData($id)) {
            $this->setData($id, $params);
            $this->setOrigData($params->getCode(), $params);
        }
        return $this;
    }

    public function createAttribute($id, $params = array())
    {
        if (!$params->getLabel() || !$params->getCode() || !$params->getInput()) {
            return false;
        }

        if (Mage::getModel('eav/entity_attribute')->load($params->getCode(), 'attribute_code')->getId()) {
            return true;
        }

        $attrModel = Mage::getModel('eav/config');

        try {
            $setup = Mage::getModel("eav/entity_setup", "core_setup");
            if ($setup instanceof Mage_Eav_Model_Entity_Setup) {
                $attrData = array(
                    'group'                      => 'General',
                    'input'                      => $params->getInput(),
                    'label'                      => $params->getLabel(),
                    'required'                   => $params->getRequired(),
                    'type'                       => '',
                    'backend'                    => '',
                    'visible'                    => '1',
                    'user_defined'               => '1',
                    'comparable'                 => '0',
                    'visible_on_front'           => '1',
                    'is_visible_on_front'        => '1',
                    'visible_in_advanced_search' => '0',
                    'is_html_allowed_on_front'   => '1',
                    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                );
                
                $type = $this->_type[$params->getInput()];
                $attrData = array_merge($attrData, $type);

                $setup->addAttribute('catalog_product', $params->getCode(), $attrData);
                $attrModel->getAttribute('catalog_product', $params->getCode())
                    ->setIsSearchable($params->getSearchable())
                    ->setIsFilterable($params->getFilterable())
                    ->setIsConfigurable(0)
                    ->save();
                return true;
            }
        } catch (Exception $e) {
            Mage::helper('brandercml/import')->log($e->getMessage());
            return false;
        }

        return false;
    }

    public function removeAllCreatedAttributes()
    {
        $count = 0;

        if ($prefix = $this->getPrefix()) {
            $setup = Mage::getModel("eav/entity_setup", "core_setup");
            if ($setup instanceof Mage_Eav_Model_Entity_Setup) {
                $attributes = Mage::getResourceModel('catalog/product_attribute_collection')->getItems();
                $pattern = '/'.$prefix.'.*/';
                foreach ($attributes as $attribute) {
                    if (preg_match($pattern, $attribute->getAttributeCode())) {
                        $setup->removeAttribute('catalog_product', $attribute->getAttributeCode());
                        $count++;
                    }
                }
            }
        }

        return $count;
    }

    public function getAttributeByCode($code)
    {
        if ($data = $this->getOrigData($code)) {
            return $data;
        }
        return null;
    }

    public function getAttributeValueByCode($attrCode, $valueID)
    {
        $attr = $this->getAttributeByCode($attrCode);
        $valKey = 'values/' . $valueID;
        return $attr->getData($valKey);
    }

    public function getAttribute($id)
    {
        if (array_key_exists($id, $this->_data)) {
            return $this->_data[$id];
        }
        return null;
    }

    public function isAllowType($type)
    {
        return array_key_exists($type, $this->_type);
    }

    public function getPrefix()
    {
        return Mage::getStoreConfig(self::ATTRIBUTE_PREFIX);
    }

    public function getDefaultType()
    {
        return Mage::getStoreConfig(self::DEFAULT_ATTRIBUTE_TYPE);
    }
}