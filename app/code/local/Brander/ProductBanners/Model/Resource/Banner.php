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
class Brander_ProductBanners_Model_Resource_Banner extends Mage_Catalog_Model_Resource_Abstract
{
    protected $_bannerProductTable = null;


    /**
     * constructor
     *
     * @access public

     */
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('brander_productbanners_banner')
            ->setConnection(
                $resource->getConnection('banner_read'),
                $resource->getConnection('banner_write')
            );
        $this->_bannerProductTable = $this->getTable('brander_productbanners/banner_product');

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
