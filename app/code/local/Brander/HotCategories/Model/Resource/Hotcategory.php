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
class Brander_HotCategories_Model_Resource_Hotcategory extends Mage_Catalog_Model_Resource_Abstract
{


    /**
     * constructor
     *
     * @access public
     */
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('brander_hotcategories_hotcategory')
            ->setConnection(
                $resource->getConnection('hotcategory_read'),
                $resource->getConnection('hotcategory_write')
            );

    }

    /**
     * wrapper for main table getter
     *
     * @access public
     * @return string
     */
    public function getMainTable()
    {
        return $this->getEntityTable();
    }
}
