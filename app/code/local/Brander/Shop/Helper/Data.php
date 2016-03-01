<?php
/**
 * Brander Shop extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        Shop
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_Shop_Helper_Data extends Mage_Core_Helper_Abstract
{

    /*
     * $position = header/footer
     * $data = logo/alt
     */
    public function getLogoObj($position)
    {
        $entity = 'logo';
        $logo = new Varien_Object();

        $storeId = Mage::app()->getStore()->getId();
        $mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $logo->setLogoSrc(
            $mediaUrl.$entity.DS.
            $this->getCfg('design/'.$position.'/custom_'.$position.'_logo', $storeId)
        );

        if ($smallSrc = $this->getCfg('design/'.$position.'/custom_'.$position.'_logo_small', $storeId)) {
            $logo->setLogoSrcSmall(
                $mediaUrl.$entity.DS.$smallSrc
            );
        }

        $logo->setLogoAlt(
            $this->getCfg('design/'.$position.'/'.$entity.'_alt', $storeId));
        return $logo;
    }

    public function getCfg($paramName, $storeId = null)
    {
        $configOption = Mage::getStoreConfig($paramName, $storeId);
        if (is_array($configOption)) {
            $config = new Varien_Object($configOption);
            return $config;
        }
        return $configOption;
    }

    public function getIconPackId()
    {
        return $this->getCfg('ultimo_design/font/custom_acc_icons_pack');
    }

}