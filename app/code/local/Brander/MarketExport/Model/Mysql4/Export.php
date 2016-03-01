<?php
/**
 * Brander MarketExport extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        MarketExport
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_MarketExport_Model_Mysql4_Export
extends Mage_Core_Model_Mysql4_Abstract
{

    protected $cattree = array();

    public function _construct()
    {
        $this->_init('marketexport/export', 'entity_id');
    }

    public function getCategories() {
        $categories = Mage::getResourceModel('catalog/category_collection')
            ->addFieldToFilter('level', array('in' => array(2)))
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_share');

        $this->get_categories($categories);

        return $this->cattree;
    }

    public function getStores() {
        $storesOptions = Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true);
        return $storesOptions;
    }

    function  get_categories($categories) {
        foreach($categories as $_category) {
            $cat = Mage::getModel('catalog/category')->load($_category->getId());
            $count = $cat->getProductCount();

            $lvlChar = str_repeat(' ¬ ', $_category->getLevel() - 2);
            $this->cattree[] = array(
                'value' => $_category->getId(),
                'label' => $lvlChar . $_category->getName().' ('.$count.')');


            if($_category->hasChildren() && $_category->getLevel() < 4) {
                $children = Mage::getModel('catalog/category')->getCategories($_category->getId());
                $this->get_categories($children);
            }
        }
        return ;
    }
}
