<?php
class Brander_AdminForms_Block_Adminhtml_Block_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected $_attributeTabBlock = 'adminforms/adminhtml_block_edit_tab_attributes';
    protected $_blockGroup = 'adminforms';

    public function __construct()
    {
        parent::__construct();
        $this->setId('block_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('adminforms')->__('Item Information'));
    }

    protected function _prepareLayout()
    {
        $block = $this->getBlock();

        if (!($setId = $block->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }

        if ($setId) {
            $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
                ->setAttributeSetFilter($setId)
                ->setOrder('sort_order', 'asc')
                ->load();

            foreach ($groupCollection as $group) {
                $attributes = $block->getAttributes($group->getId(), true);
                // do not add grops without attributes

                if (count($attributes)==0) {
                    continue;
                }

                $this->addTab('group_'.$group->getId(), array(
                    'label'     => Mage::helper('adminforms')->__($group->getAttributeGroupName()),
                    'content'   => $this->getLayout()->createBlock($this->getAttributeTabBlock())
                        ->setGroup($group)
                        ->setGroupAttributes($attributes)
                        ->toHtml(),
                ));
            }
			Mage::dispatchEvent('adminforms_block_edit_tabs', array('tabs' => $this, 'request' => $this->getRequest()));
			Mage::dispatchEvent('adminforms_block_edit_tabs_'.$block->getEntityType(), array('tabs' => $this, 'block' => $block));
        }
        else {
            $this->addTab('set', array(
                'label'     => Mage::helper('adminforms')->__('Settings'),
                'content'   => $this->getLayout()->createBlock('adminforms/adminhtml_block_edit_tab_settings')->toHtml(),
                'active'    => true
            ));
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrive block object from object if not from registry
     *
     */
    public function getBlock()
    {
        if (!($this->getData('block') instanceof Brander_AdminForms_Model_Block)) {
            $this->setData('block', Mage::registry('block'));
        }
        return $this->getData('block');
    }

    /**
     * Getting attribute block name for tabs
     *
     * @return string
     */
    public function getAttributeTabBlock()
    {
        return $this->_attributeTabBlock;
    }

    public function setAttributeTabBlock($attributeTabBlock)
    {
        $this->_attributeTabBlock = $attributeTabBlock;
        return $this;
    }
}
