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
class Brander_Benefits_Model_Resource_Benefit extends Mage_Catalog_Model_Resource_Abstract
{
    protected $_benefitProductTable = null;
    protected $_benefitCategoryTable = null;


    /**
     * constructor
     *
     * @access public
     */
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('brander_benefits_benefit')
            ->setConnection(
                $resource->getConnection('benefit_read'),
                $resource->getConnection('benefit_write')
            );
        $this->_benefitProductTable = $this->getTable('brander_benefits/benefit_product');
        $this->_benefitCategoryTable = $this->getTable('brander_benefits/benefit_category');

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
