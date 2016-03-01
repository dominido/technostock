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
class Brander_ProductBanners_Block_Banner_View extends Mage_Core_Block_Template
{
    /**
     * get the current banner
     *
     * @access public
     * @return mixed (Brander_ProductBanners_Model_Banner|null)

     */
    public function getCurrentBanner()
    {
        return Mage::registry('current_banner');
    }
}
