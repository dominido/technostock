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
class Brander_CmsHome_Block_HomepageSlider extends Mage_Core_Block_Template
{
    protected $_collection = null;
    protected $_sliderSrc = null;

    protected function _construct() {
        parent::_construct();
        $this->addData(array(
            'cache_lifetime' => 72000,
            'cache_tags' => array(
                'HOME',
                'HOMEPAGE_SLIDERS'
            )
        ));
    }

    public function getCacheKeyInfo() {
        $cacheId = array(
            'HOME_HOMEPAGE_SLIDERS',
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
            $this->_collection = Mage::helper('adminforms')->getCollection('homepage_slider_banners')
                ->addAttributeToFilter('status', Brander_AdminForms_Model_Config_Source_Status::STATUS_ENABLED)
                ->addAttributeToSort('position', 'ASC')
                ->addAttributeToSort('entity_id', 'ASC');
        }

        return $this->_collection;
    }

    protected function getSliderSrcJson() {
        $_helper = Mage::helper('brander_cmshome/image');
        $collection = $this->getCollection();
        $imageSrc = $slideShowSrc = array(); $srcList = array('regular' => 'image', 'pad' => 'pad_image', 'phone' => 'phone_image');
        if (!$collection->getSize()) {
            return json_encode($slideShowSrc);
        }

        foreach ($collection as $slide) {
            foreach ($srcList as $src) {
                if ($slide->getData($src)) {
                    $imageSrc[$src][$slide->getEntityId()] = $_helper->getImageSrc('homepage_slider_banners', $slide->getData($src));
                }
            }
        }

        foreach ($srcList as $key => $src) {
            if (count($imageSrc[$src]) == count($imageSrc['image'])) $slideShowSrc[$key] = $imageSrc[$src];
        }

        return json_encode($slideShowSrc);
    }

    public function getSliderSrc()
    {
        $this->_sliderSrc = $this->getSliderSrcJson();
        return $this->_sliderSrc;
    }
}