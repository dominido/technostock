<?php
/**
 * Brander Benefits extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        Benefits
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_Benefits_Block_Adminhtml_Benefit_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('benefit_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('brander_benefits')->__('Benefit Information'));
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Brander_Benefits_Block_Adminhtml_Benefit_Edit_Tabs
     */
    protected function _prepareLayout()
    {
        $benefit = $this->getBenefit();
        $entity = Mage::getModel('eav/entity_type')
            ->load('brander_benefits_benefit', 'entity_type_code');
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($entity->getEntityTypeId());
        $attributes->getSelect()->order('additional_table.position', 'ASC');

        $this->addTab(
            'info',
            array(
                'label'   => Mage::helper('brander_benefits')->__('Benefit Information'),
                'content' => $this->getLayout()->createBlock(
                    'brander_benefits/adminhtml_benefit_edit_tab_attributes'
                )
                ->setAttributes($attributes)
                ->toHtml(),
            )
        );
        $this->addTab(
            'products',
            array(
                'label' => Mage::helper('brander_benefits')->__('Associated products'),
                'url'   => $this->getUrl('*/*/products', array('_current' => true)),
                'class' => 'ajax'
            )
        );
        $this->addTab(
            'categories',
            array(
                'label' => Mage::helper('brander_benefits')->__('Associated categories'),
                'url'   => $this->getUrl('*/*/categories', array('_current' => true)),
                'class' => 'ajax'
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve benefit entity
     *
     * @access public
     * @return Brander_Benefits_Model_Benefit
     */
    public function getBenefit()
    {
        return Mage::registry('current_benefit');
    }
}
