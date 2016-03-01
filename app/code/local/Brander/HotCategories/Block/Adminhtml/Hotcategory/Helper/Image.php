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
class Brander_HotCategories_Block_Adminhtml_Hotcategory_Helper_Image extends Varien_Data_Form_Element_Image
{
    /**
     * get the url of the image
     *
     * @access protected
     * @return string
     */
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = Mage::helper('brander_hotcategories/hotcategory_image')->getImageBaseUrl().
                $this->getValue();
        }
        return $url;
    }
}
