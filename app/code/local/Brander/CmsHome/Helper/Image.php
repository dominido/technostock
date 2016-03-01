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
class Brander_CmsHome_Helper_Image extends Mage_Core_Helper_Abstract
{
    public function getImageSrc($entity, $imagePath)
    {
        $src = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'adminforms' . DS . $entity . $imagePath;
        return $src;
    }

    public function getBranderLogo()
    {
        $src = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'brander' . DS . 'brander.png';
        return $src;
    }
}