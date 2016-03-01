<?php
/**
 * Brander SiteHeart extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        SiteHeart
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_SiteHeart_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getSiteHeartLanguages()
    {
        return $siteHeartLangs = array(
            "ru" => "Русский",
            "uk" => "Украинский",
            "en" => "Английский",
            "de" => "Немецкий",
            "pt" => "Португальский",
            "it" => "Итальянский",
            "ge" => "Грузинский",
            "lt" => "Латвийский",
            "cz" => "Чешский",
        );

    }

}