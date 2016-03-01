<?php
class Brander_Cms_Model_Page_Type extends Varien_Object
{
    /*
     * @var Brander_Cms_Model_Page
     */
    protected $_page;

    /*
     * Returns page groups
     *
     * @return Varien_Data_Collection
     */
    public function getGroupCollection()
    {
        $collection = Mage::getResourceModel('eav/entity_attribute_group_collection')
            ->setAttributeSetFilter($this->getPage()->getDefaultAttributeSetId())
            ->addFieldToFilter('attribute_group_name', array('in' => $this->getGroupNames()))
            ;

        $prepCol = new Varien_Data_Collection();
        foreach ($this->getGroupNames() as $groupName) {
            if ($group = $collection->getItemByColumnValue('attribute_group_name', $groupName)) {
                $prepCol->addItem($group);
            }
        }

        return $prepCol;
    }

    /*
     * Returns page type attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributeCodes = array_keys($this->getAllAttributes());
        $resource = $this->getPage()->getResource();

        foreach ($attributeCodes as $code) {
            $resource->getAttribute($code);
        }

        $attributes = $resource->getSortedAttributes();

        foreach ($attributes as $key => $attribute) {
            if (!in_array($attribute->getAttributeCode(), $attributeCodes)) {
                unset($attributes[$key]);
            }
        }
        
        if (!$this->getPage()->getId()) {
            foreach ($attributes as $attribute) {
                $default = $attribute->getDefaultValue();
                if ($default != '') {
                    $this->getPage()->setData($attribute->getAttributeCode(), $default);
                }
            }
        }

        return $attributes;
    }

    /*
     * Returns page type all config attributes
     *
     * @return array
     */
    public function getAllAttributes()
    {
        $attributes = array();

        foreach ($this->getGroups() as $group) {
            if (isset($group['attributes']) && is_array($group['attributes'])) {
                $attributes = array_merge($attributes, $group['attributes']);
            }
        }

        return $attributes;
    }

    /*
     * Returns page type prepared group names
     *
     * @return array
     */
    public function getGroupNames()
    {
        if (!$groups = $this->getData('groups')) {
            return;
        }

        $names = array();

        $bufName = '';
        $helper = Mage::helper('cmsadvanced');

        foreach ($groups as $groupKey => $group) {
            if (isset($group['name'])) {
                $bufName = $group['name'];
            } else {
                $bufName = $helper->getNameFromCode($groupKey);
            }

            $names[] = $bufName;
        }

        return $names;
    }

    /* Returns page type page
     *
     * @return Brander_Cms_Model_Page
     */
    public function getPage()
    {
        return $this->_page;
    }

    /*
     * Sets page
     *
     * @param Brander_Cms_Model_Page
     * @return Brander_Cms_Model_Page_Type
     */
    public function setPage(Brander_Cms_Model_Page $page)
    {
        $this->_page = $page;
        return $this;
    }

    /*
     * Returns page type name
     *
     * @return string
     */
    public function getName()
    {
        if (!$name = $this->getData('name')) {
            $name = Mage::helper('cmsadvanced')->getNameFromCode($this->getCode());
        };

        return $name;
    }

    /*
     * Returns page type parent codes
     *
     * return array
     */
    public function getParentCodes()
    {
        return Mage::getSingleton('cmsadvanced/config')->getPageTypeParents($this->getCode());
    }

    public function canCached()
    {
        return (bool) $this->getCached();
    }
}
