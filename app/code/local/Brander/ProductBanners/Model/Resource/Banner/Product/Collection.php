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
class Brander_ProductBanners_Model_Resource_Banner_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    /**
     * remember if fields have been joined
     *
     * @var bool
     */
    protected $_joinedFields = false;

    /**
     * join the link table
     *
     * @access public
     * @return Brander_ProductBanners_Model_Resource_Banner_Product_Collection

     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('brander_productbanners/banner_product')),
                'related.product_id = e.entity_id',
                array('position')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * add banner filter
     *
     * @access public
     * @param Brander_ProductBanners_Model_Banner | int $banner
     * @return Brander_ProductBanners_Model_Resource_Banner_Product_Collection

     */
    public function addBannerFilter($banner)
    {
        if ($banner instanceof Brander_ProductBanners_Model_Banner) {
            $banner = $banner->getId();
        }
        if (!$this->_joinedFields ) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.banner_id = ?', $banner);
        return $this;
    }
}
