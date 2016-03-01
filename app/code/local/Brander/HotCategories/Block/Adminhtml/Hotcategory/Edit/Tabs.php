<?php
/**
 * Brander HotCategories extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        HotCategories
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_HotCategories_Block_Adminhtml_Hotcategory_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('hotcategory_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('brander_hotcategories')->__('Hot Category Information'));
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Brander_HotCategories_Block_Adminhtml_Hotcategory_Edit_Tabs
     */
    protected function _prepareLayout()
    {
        $hotcategory = $this->getHotcategory();
        $entity = Mage::getModel('eav/entity_type')
            ->load('brander_hotcategories_hotcategory', 'entity_type_code');
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($entity->getEntityTypeId());
        $attributes->getSelect()->order('additional_table.position', 'ASC');

        $this->addTab(
            'info',
            array(
                'label'   => Mage::helper('brander_hotcategories')->__('Hot Category Information'),
                'content' => $this->getLayout()->createBlock(
                    'brander_hotcategories/adminhtml_hotcategory_edit_tab_attributes'
                )
                ->setAttributes($attributes)
                ->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve hot category entity
     *
     * @access public
     * @return Brander_HotCategories_Model_Hotcategory
     */
    public function getHotcategory()
    {
        return Mage::registry('current_hotcategory');
    }
}
