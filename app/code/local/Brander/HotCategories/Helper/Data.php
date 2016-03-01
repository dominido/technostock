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
class Brander_HotCategories_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * convert array to options
     *
     * @access public
     * @param $options
     * @return array
     */
    public function convertOptions($options)
    {
        $converted = array();
        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value']) &&
                isset($option['label']) && !is_array($option['label'])) {
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }

    public function convertCategoryOptions($options)
    {
        $converted = array();

        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value'] && isset($option['level'])) &&
                isset($option['label']) && !is_array($option['label'])) {
                if (!empty($option['base_label'])){
                    $converted[$option['value']] = $option['path'].$option['base_label']. ' ' . $this->__('(level %s)', $option['level']);
                } else {
                    $converted[$option['value']] = '';
                }

            }
        }
        return $converted;
    }
}
