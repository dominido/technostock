<?php
class Brander_AdminForms_Block_Adminhtml_Block_Grid_Standard extends Brander_AdminForms_Block_Adminhtml_Block_Grid_Abstract
{
    protected function _prepareCollection()
    {
        $collection = $this->getModel()->getCollection()
                    ->setStore($this->_getStore())
                    ->addAttributeToSelect('*');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $model = $this->getModel();

        $attributes = $model->getGridAttributes();

        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('adminforms')->__('ID'),
                'width' => '1',
                'type'  => 'number',
                'index' => 'entity_id',
        ));

        foreach ($attributes as $attribute) {
            $options = array(
                'width'  => 1,
                'index'  => $attribute->getAttributeCode()
            );

            $options = $this->_appendGridTypeData($options, $attribute);
            $options = $this->_appendGridOptions($options, $attribute);

            $this->addColumn($attribute->getAttributeCode(), $options);
        };

        if (!$this->getReadonlyGrid()){
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('adminforms')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'    => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('adminforms')->__('Edit'),
                            'url'     => array(
                                'base'=>'*/*/edit',
                                'params'=>array('block_key'=>$this->getRequest()->getParam('block_key'),'store'=>$this->getRequest()->getParam('store'))
                            ),
                            'field'   => 'entity_id'
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
            ));
        }

        return parent::_prepareColumns();
    }

    protected function _appendGridTypeData($options, $attribute)
    {
        if (!isset($options['type'])) {
            $options['type'] = $this->getGridTypeByAttribute($attribute);
        }

        switch ($options['type']) {
            case 'options':
                $options['options'] = $this->_getSourceOptions($attribute->getSourceModel());
                break;

            default:
        }

        return $options;
    }

    protected function _appendGridOptions($options, $attribute)
    {
        return $options = array_merge($options, (array) $attribute->getGridOptions());
    }

    protected function _getSourceOptions($sourceModel)
    {
        try {
            $source = Mage::getModel($sourceModel);
            if ($source) {
                $options = $source->getOptions();
            } else {
                $options = array();
            }
        } catch (Exception $e) {
            $options = array();
        }

        return $options;
    }

    public function getGridTypeByAttribute($attribute)
    {
        $attrInput = $attribute->getFrontendInput();
        $attrType  = $attribute->getType();

        switch ($attrInput) {
            case 'select':
                $type = 'options';
                break;

            case 'text':
                if ('int' == $attrType) {
                    $type = 'number';
                } else {
                    $type = $attrInput;
                }
                break;

            default:
                $type = $attrInput;
        }

        return $type;
    }

    public function getBlockKey()
    {
        if (!$this->hasBlockKey()) {
            $this->setBlockKey($this->getRequest()->getParam('block_key'));
        }

        return $this->getData('block_key');
    }

    public function getEntityType()
    {
        if (!$this->hasEntityType()) {
            $this->setEntityType($this->helper('adminforms')->getEntityTypeByBlock($this->getBlockKey()));
        }

        return $this->getData('entity_type');
    }

    public function getModel()
    {
        return $this->helper('adminforms')->getModel($this->getEntityType())->setBlockKey($this->getBlockKey());
    }
}
