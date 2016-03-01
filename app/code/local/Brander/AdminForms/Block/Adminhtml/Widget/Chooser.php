<?php

class Brander_AdminForms_Block_Adminhtml_Widget_Chooser extends Brander_AdminForms_Block_Adminhtml_Block_Grid_Standard
{
    protected $_selectedBlocks = array();

    /**
     * Block construction, prepare grid params
     *
     * @param array $arguments Object data
     */
    public function __construct($arguments=array())
    {
        parent::__construct($arguments);
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);

        $this->setReadonlyGrid(true);
    }

    /**
     * Prepare chooser element HTML
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('adminforms/adminhtml_chooser/chooser', array(
            'uniq_id' => $uniqId,
            'use_massaction' => false,
        ));

        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);

        if ($element->getValue()){
            $chooser->setLabel($element->getValue());
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Checkbox Check JS Callback
     *
     * @return string
     */
    public function getCheckboxCheckCallback()
    {
        if ($this->getUseMassaction()) {
            return "function (grid, event){
                $(grid.containerId).fire('adminforms_block:changed', {});
            }";
        }
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        if (!$this->getUseMassaction()) {
            $chooserJsObject = $this->getId();
            return '
                function (grid, event) {
                    var trElement = Event.findElement(event, "tr");
                    var blockId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                    '.$chooserJsObject.'.setElementValue(optionValue);
                    '.$chooserJsObject.'.setElementLabel(optionValue);
                    '.$chooserJsObject.'.close();
                }
            ';
        }
    }

    /**
     * Filter checked/unchecked rows in grid
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_blocks'){
            $selected = $this->getSelectedBlocks();
            if ($column->getFilter()->getValue()){
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$selected));
            } else {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$selected));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareColumns()
    {
        if ($this->getUseMassaction()){
            $this->addColumn('in_blocks', array(
                'width'             => '1',
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_blocks',
                'inline_css'        => 'checkbox entities',
                'field_name'        => 'in_blocks',
                'values'            => $this->getSelectedBlocks(),
                'align'             => 'center',
                'index'             => 'entity_id',
                'use_index'         => true,
            ));
        }
        return parent::_prepareColumns();
    }

    /**
     * Adds additional parameter to URL for loading only blocks grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminforms/adminhtml_chooser/chooser', array(
            '_current'       => true,
            'uniq_id'        => $this->getId(),
            'use_massaction' => $this->getUseMassaction(),
            'block_key'      => $this->getBlockKey()
        ));
    }

    public function setSelectedBlocks($selectedBlocks)
    {
        $this->_selectedBlocks = $selectedBlocks;
        return $this;
    }

    public function getSelectedBlocks()
    {
        if ($selectedBlocks = $this->getRequest()->getParam('selected_blocks', null)) {
            $this->setSelectedBlocks($selectedBlocks);
        }
        return $this->_selectedBlocks;
    }

}
