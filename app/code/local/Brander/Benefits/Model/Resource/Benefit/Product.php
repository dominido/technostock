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
class Brander_Benefits_Model_Resource_Benefit_Product extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * initialize resource model
     *
     * @access protected
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     */
    protected function  _construct()
    {
        $this->_init('brander_benefits/benefit_product', 'rel_id');
    }
    /**
     * Save benefit - product relations
     *
     * @access public
     * @param Brander_Benefits_Model_Benefit $benefit
     * @param array $data
     * @return Brander_Benefits_Model_Resource_Benefit_Product
     */
    public function saveBenefitRelation($benefit, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('benefit_id=?', $benefit->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $productId => $info) {
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'benefit_id' => $benefit->getId(),
                    'product_id'    => $productId,
                    'position'      => @$info['position']
                )
            );
        }
        return $this;
    }

    /**
     * Save  product - benefit relations
     *
     * @access public
     * @param Mage_Catalog_Model_Product $prooduct
     * @param array $data
     * @return Brander_Benefits_Model_Resource_Benefit_Product
     */
    public function saveProductRelation($product, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('product_id=?', $product->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $benefitId => $info) {
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'benefit_id' => $benefitId,
                    'product_id'    => $product->getId(),
                    'position'      => @$info['position']
                )
            );
        }
        return $this;
    }
}
