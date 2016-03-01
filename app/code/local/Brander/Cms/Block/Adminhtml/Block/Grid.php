<?php
class Brander_Cms_Block_Adminhtml_Block_Grid extends Brander_AdminForms_Block_Adminhtml_Block_Grid_Standard
{
    public function __construct($data)
    {
        parent::__construct();

        $this->setId('adminFormsGrid_' . $data['entity_type']);
        $this->setVarNameFilter('block_filter_' . Mage::getSingleton('cmsadvanced/config')->getDefaultEntityType());
    }
    
    protected function _prepareCollection()
    {        
        $collection = $this->getModel()->getCollection()
                    ->setStore($this->_getStore())
                    ->addAttributeToSelect('*');

        $collection->addAttributeToFilter('page_id', $this->getPageId());

        $this->setCollection($collection);

        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/cms_advanced_block/grid', $this->_getUrlParams());
    }

    public function getRowUrl($row)
    {
        $params = array_merge(array('entity_id' => $row->getId()), $this->_getUrlParams());
        return $this->getUrl('*/cms_advanced_block/edit', $params);
    }

    public function getRowClickCallback()
    {
        return 'function(data, event){' . $this->_getActionJs('event.currentTarget.title') . '; return false;}';
    }

    public function getMainButtonsHtml()
    {
        $html = parent::getMainButtonsHtml();
        $html .= $this->getCreateButtonHtml();
        return $html;
    }

    public function getCreateButtonHtml()
    {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Add New'),
                    'onclick'   => $this->_getActionJs($this->getAddNewUrl(), false),
                    'class'   => 'task'
                ))->toHtml();
    }

    public function getAddNewUrl()
    {
        return $this->getUrl('*/cms_advanced_block/new', $this->_getUrlParams());
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        unset($this->_columns['action']);
    }

    protected function _getUrlParams()
    {
        return array(
            'block_key' => $this->getBlockKey(),
            'store'=> $this->getRequest()->getParam('store'),
            'page_id' => $this->getPageId()
        );
    }

    protected function _getActionJs($url = '', $isPlain = true)
    {
        $refreshUrl = $this->getUrl('*/cms_advanced/edit', array('id' => $this->getPageId(), 'store' => $this->_getStore()->getId()));

        if (!$isPlain) {
            $url = "'" . $url . "'";
        }

        $js = "var activeTabId = $$('.tab-item-link.active')[0].id; openDialogWindow.refreshUrl='$refreshUrl?active_tab_id=' + activeTabId;openDialogWindow.open($url);return false;";
        return $js;
    }

    public function getPageId()
    {
        if (!$pageId = $this->getData('page_id')) {
            $pageId = $this->getPage()->getId();
        }

        return $pageId;
    }

    public function getPage()
    {
        return Mage::registry('current_page');
    }
}
