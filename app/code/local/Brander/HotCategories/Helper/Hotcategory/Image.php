<?php
/**
 * Brander HotCategories extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        HotCategories
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_HotCategories_Helper_Hotcategory_Image extends Brander_Shop_Helper_Image_Abstract
{
    /**
     * image placeholder
     * @var string
     */
    protected $_placeholder = 'images/brander/hotcategories/placeholder.jpg';
    /**
     * image subdir
     * @var string
     */
    protected $_subdir      = 'hotcategory';

    public function getGridWidth($imageFile)
    {
        // TODO: create select, to set width of banner
        list($originalWidth, $originalHeight) = getimagesize($imageFile);
        if (($originalWidth/$originalHeight) >= 1) {
            $ratio = $originalWidth / $originalHeight;
        } else {
            $ratio = $originalHeight / $originalWidth;
        }

        $grid = round($ratio/1.6)*3;
        return $grid;
    }
}
