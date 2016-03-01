<?php
/**
 * Brander Benefits extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        Benefits
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_Benefits_Block_Benefit_List extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $benefits = Mage::getResourceModel('brander_benefits/benefit_collection')
                         ->setStoreId(Mage::app()->getStore()->getId())
                         ->addAttributeToSelect('*')
                         ->addAttributeToFilter('status', 1);
        $benefits->setOrder('order_position', 'asc');
        $this->setBenefits($benefits);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Brander_Benefits_Block_Benefit_List
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'brander_benefits.benefit.html.pager'
        )
        ->setCollection($this->getBenefits());
        $this->setChild('pager', $pager);
        $this->getBenefits()->load();
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
