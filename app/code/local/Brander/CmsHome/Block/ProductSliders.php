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
class Brander_CmsHome_Block_ProductSliders extends Mage_Core_Block_Template
{
    public $_miniSliders = null;
    static $_showBannerBlock = true;

    protected function _construct()
    {
        parent::_construct();
        $this->getBlocksConfiguration();
    }

    public function getSliderConfiguration($type)
    {
        $confPath = 'brander_homepage/products_sliders_'.$type;
        $config = Mage::helper('brander_shop')->getCfg($confPath);
        if (!$config->getEnable()) {
            return false;
        }

        if ($config->getCustomConfig()) {
            return $config;
        } else {
            $sliderConfig = Mage::helper('brander_shop')->getCfg('unitop_settings/products_sliders');
            $sliderConfig->setBlockName($config->getBlockName());
            return $sliderConfig;
        }
    }

    public function getBlocksConfiguration()
    {
        $options = array();
        $helper = Mage::helper('brander_cmshome');

        foreach ($this->getBlockTypes() as $type) {
            if (!$helper->getSliderParams($type, 'enable')) {
                continue;
            }

            $configuration = $helper->getHomepageSlidersConfiguration($type);
            /*$configuration->setType($type)
                ->setCategoryId($configuration->getCategorySelect())
                ->setIsRandom($configuration->getRandom());*/

            $slider = new Varien_Object();

            //$slider->setData($configuration->getData());

            $slider->setType($type)
                ->setBlockAlias($this->getBlockSource($type))
                ->setPosition($position = $this->getPosition($type))
                ->setCategoryId($configuration->getCategorySelect())
                ->setProductCount($configuration->getProductCount()) // $param : new/special/hits, field_name
                ->setBreakpoints($this->getBreakpoints($position))
                ->setIsRandom($configuration->getRandom())
                ->setMove($configuration->getMove())
                ->setPagination($configuration->getPagination())
                ->setCentered($configuration->getCentered())
                ->setHideButton($configuration->getHideButton())
                ->setBlockName($configuration->getBlockName())
            ;

            $options[$type] = $slider;
        }
        $this->setSlidersConfiguration($options);
        //return $options;
    }

    protected function getPosition($type)
    {
        if ($this->_miniSliders || ($this->isMiniSliderAvailable())) {
            $position = Mage::helper('brander_cmshome')->getSliderParams($type, 'position');
            $model = 'brander_cmshome/config_source_position'.$type;
            $options = $this->convertOptions(Mage::getModel($model)->toOptionArray());
            if (!empty($options[$position])) {
                return mb_strtolower($options[$position]);
            }
        }
        return '';
    }

    protected function isMiniSliderAvailable()
    {
        $helper = Mage::helper('brander_cmshome');

        if ($helper->getCfg('sliders_config/mini_sliders') &&
            $helper->getSliderParams('hits', 'enable') &&
            $helper->getSliderParams('special', 'enable') ){
                $this->_miniSliders = true;
                return true;
        }
    }

    protected function getBlockTypes()
    {
        if ($this->isMiniSliderAvailable() && (Mage::helper('brander_cmshome')->getSliderParams('hits', 'position') == 1)) {
            return array('hits','special');
        }
        return array('special','hits');
    }

    protected function getBlockSource($type)
    {
        return 'product_sliders.list_'.$type;
    }

    protected function convertOptions($options)
    {
        if (empty($options)) {return array();}

        $converted = array();
        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value']) &&
                isset($option['label']) && !is_array($option['label'])) {
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }

    public function getBreakpoints($postion)
    {
        if ($postion) {
            return Mage::helper('brander_cmshome')->getCfg('sliders_config/breakpoints_mini');

        } else {
            return Mage::helper('brander_cmshome')->getCfg('sliders_config/breakpoints');
        }
    }

    public function getShowBannerBlock()
    {
        return self::$_showBannerBlock;
    }

    public function setShowBannerBlock($value)
    {
        self::$_showBannerBlock = $value;
    }

    public function checkForGutter($value)
    {
        $switch = array(
            'left'  => 'right',
            'right' => 'left'
        );

        if (isset($switch[$value])) {
            return $switch[$value];
        }
        return false;
    }
}