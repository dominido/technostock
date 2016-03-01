<?php
/**
 * Magento Commercial Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Commercial Edition License
 * that is available at: http://www.magentocommerce.com/license/commercial-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Category tabs
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Brander_Cms_Block_Adminhtml_Page_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Default Attribute Tab Block
     *
     * @var string
     */
    protected $_attributeTabBlock = 'cmsadvanced/adminhtml_page_tab_attributes';

    /**
     * Initialize Tabs
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('category_info_tabs');
        $this->setDestElementId('category_tab_content');
        $this->setTitle(Mage::helper('catalog')->__('Category Data'));
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    /**
     * Retrieve cattegory object
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getPage()
    {
        return Mage::registry('current_page');
    }

    /**
     * Return Adminhtml Catalog Helper
     *
     * @return Mage_Adminhtml_Helper_Catalog
     */
    public function getCatalogHelper()
    {
        return Mage::helper('adminhtml/catalog');
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

    /**
     * Prepare Layout Content
     *
     * @return Mage_Adminhtml_Block_Catalog_Category_Tabs
     */
    protected function _prepareLayout()
    {
        $page = $this->getPage();

        $attributeSetId  = $page->getDefaultAttributeSetId();

        $pageType = $page->getPageType();

        $groupCollection = $pageType->getGroupCollection();
        $pageAttributes = $pageType->getAttributes();

        $defaultGroupId = 0;
        foreach ($groupCollection as $group) {
            if ($defaultGroupId == 0 or $group->getIsDefault()) {
                $defaultGroupId = $group->getId();
            }
        }

        foreach ($groupCollection as $group) {
            /* @var $group Mage_Eav_Model_Entity_Attribute_Group */
            $attributes = array();
            foreach ($pageAttributes as $attribute) {
                /* @var $attribute Mage_Eav_Model_Entity_Attribute */
                if ($attribute->isInGroup($attributeSetId, $group->getId())) {
                    $attributes[] = $attribute;
                }
            }

            // do not add grops without attributes
            if (!$attributes) {
                continue;
            }

            $active  = $defaultGroupId == $group->getId();
            $block = $this->getLayout()->createBlock($this->getAttributeTabBlock(), '')
                ->setGroup($group)
                ->setAttributes($attributes)
                ->setAddHiddenFields($active)
                ->toHtml();
                
            $this->addTab('group_' . $group->getId(), array(
                'label'     => Mage::helper('catalog')->__($group->getAttributeGroupName()),
                'content'   => $block,
                'active'    => $active
            ));
        }
        return parent::_prepareLayout();
    }
}
