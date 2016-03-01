<?php
class Brander_AdminForms_Block_Block_List extends Mage_Core_Block_Template
{
    protected $_model = 'adminforms/block';
    protected $_collection = null;

    public function setSortByAttribute($attribute, $dir='ASC'){
        $this->setData('sort_by_attribute', $attribute);
        $this->setData('sort_by_attribute_dir', $dir);
        return $this;
    }

    public function setSortByPosition(){
        $this->setSortByAttribute('position', 'asc');
        return $this;
    }

    public function setModel($model){
        $this->_model = $model;
        return $this;
    }

    public function getModel(){
        return $this->_model;
    }

    public function getCollection()
    {
        $blockKey = $this->getBlockKey();

        if ($blockKey && is_null($this->_collection)){
            $this->_collection = Mage::getModel($this->_model, array('entity_type'=>$blockKey))->getCollection()->addAttributeToSelect('*');

            if ($this->getSortByPosition())
                $this->_collection->addAttributeToSort('position');

            if (!is_null($this->getData('sort_by_attribute')))
                $this->_collection->addAttributeToSort($this->getData('sort_by_attribute'), $this->getData('sort_by_attribute_dir'));

            if ((int)$this->getLimit())
                $this->_collection->setPageSize((int)$this->getLimit());
        }

        return $this->_collection;
    }
}
