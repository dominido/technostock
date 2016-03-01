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
class Brander_Shop_Helper_Image extends Mage_Core_Helper_Abstract
{
    public function getCategoryImageUrl($category, $param)
    {
        $url = false;
        if ($image = $category->getData($param)) {
            $url = Mage::getBaseUrl('media').'catalog/category/'.$image;
        }
        return $url;
    }

    public function checkImageExist($url)
    {
        $file_headers = @get_headers($url);
        if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
            return false;
        }
        return $url;
    }
}