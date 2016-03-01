<?php
/**
 * Brander ProductDiscount extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        ProductDiscount
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_ProductDiscount_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getProductDiscount($product)
    {
        $specialPrice = number_format($product->getFinalPrice(), 2, '.', '');
        $regularPrice = number_format($product->getPrice(), 2, '.', '');
        if ($specialPrice < $regularPrice) {
            $discount = round((1 - $specialPrice / $regularPrice) * 100);
            return '-' . $discount . '%';
        }
    }
}