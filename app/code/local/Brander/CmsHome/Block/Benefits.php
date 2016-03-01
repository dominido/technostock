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
 * @package        Benefits
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_CmsHome_Block_Benefits extends Mage_Core_Block_Template
{

    protected $_collection = null;

    protected function _construct() {
        parent::_construct();
        $this->addData(array(
            'cache_lifetime' => 72000,
            'cache_tags' => array(
                'HOME',
                'HOMEPAGE_BENEFITS'
            )
        ));
    }

    public function getCacheKeyInfo() {
        $cacheId = array(
            'HOME_HOMEPAGE_BENEFITS',
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
            $config = Mage::helper('brander_shop')->getCfg('brander_benefits/benefits_config');

            if ($config->getEnable() && $config->getEnableHomepage()) {
                $collection = Mage::getModel('brander_benefits/benefit')
                    ->getCollection()
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('status', 1)
                    ->addAttributeToFilter('show_on_homepage', 1)
                    ->setOrder('order_position', 'ASC');

                if ($limit = $config->getHomepageBenefitLimit()) {
                    $collection->setPageSize($limit)->setCurPage(1);
                }
            } else {
                $collection = Mage::getModel('brander_benefits/benefit');
            }

            $this->_collection = $collection;
        }
        return $this->_collection;
    }
}
