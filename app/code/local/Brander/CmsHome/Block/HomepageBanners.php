<?php
/**
 * Brander CmsHome extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        CmsHome
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_CmsHome_Block_HomepageBanners extends Mage_Core_Block_Template
{
    protected $_collection = null;

    protected function _construct() {
        parent::_construct();
        $this->addData(array(
            'cache_lifetime' => 72000,
            'cache_tags' => array(
                'HOME',
                'HOMEPAGE_BANNERS'
            )
        ));
    }

    public function getCacheKeyInfo() {
        $cacheId = array(
            'HOME_HOMEPAGE_BANNERS',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout()
        );

        return $cacheId;
    }

    public function getCollection()
    {
        if(!$this->_collection){
            $this->_collection = Mage::helper('adminforms')->getCollection('homepage_banners')
                ->addAttributeToFilter('status', Brander_AdminForms_Model_Config_Source_Status::STATUS_ENABLED)
                ->addAttributeToSort('position', 'ASC')
                ->addAttributeToSort('entity_id', 'ASC');
        }
        return $this->_collection;
    }

}