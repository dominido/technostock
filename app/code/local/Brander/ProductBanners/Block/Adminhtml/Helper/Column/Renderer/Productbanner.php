<?php
/**
 * Brander ProductBanners extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        ProductBanners
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_ProductBanners_Block_Adminhtml_Helper_Column_Renderer_Productbanner extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        //get value
        $value = $row->getImage();
        $url = $row->getRedirectUrl();
        //return path
        $path = Mage::helper('brander_productbanners/banner_image')->getImageBaseUrl() . $value;
        return '<img src="'.$path.'" style = "max-width: 300px;max-height: 200px;" title="'.$url.'"/>';
    }
}