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
class Brander_ProductBanners_Block_Banner_List extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public

     */
    public function __construct()
    {
        parent::__construct();
        $banners = Mage::getResourceModel('brander_productbanners/banner_collection')
                         ->setStoreId(Mage::app()->getStore()->getId())
                         ->addAttributeToSelect('*')
                         ->addAttributeToFilter('status', 1);
        $banners->setOrder('title', 'asc');
        $this->setBanners($banners);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Brander_ProductBanners_Block_Banner_List

     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'brander_productbanners.banner.html.pager'
        )
        ->setCollection($this->getBanners());
        $this->setChild('pager', $pager);
        $this->getBanners()->load();
        return $this;
    }

    /**
     * get the pager html
     *
     * @access public
     * @return string

     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
