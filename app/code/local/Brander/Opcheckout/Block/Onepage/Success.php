<?php

class Brander_Opcheckout_Block_Onepage_Success extends Mage_Checkout_Block_Onepage_Success
{

    protected $_itemRenders = array();

    /**
     * @param $type
     * @param $block
     * @param $template
     */
    public function addItemRender($type, $block, $template) {
        $this->_itemRenders[$type] = array('block' => $block, 'template' => $template, 'renderer' => null,);
    }

    /**
     * Return product type for quote/order item
     *
     * @param Varien_Object $item
     * @return string
     */
    protected function _getItemType(Varien_Object $item) {
        if ($item->getOrderItem()) {
            $type = $item->getOrderItem()->getProductType();
        } elseif ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            $type = $item->getQuoteItem()->getProductType();
        } else {
            $type = $item->getProductType();
        }
        return $type;
    }

    /**
     * Retrieve item renderer block
     *
     * @param string $type
     * @return Mage_Core_Block_Abstract
     */
    public function getItemRenderer($type) {
        if (!isset($this->_itemRenders[$type])) {
            $type = 'default';
        }

        if (is_null($this->_itemRenders[$type]['renderer'])) {
            $renderer = $this->getLayout()
                             ->createBlock($this->_itemRenders[$type]['block']);
            /** @var Mage_Core_Block_Template $renderer */
            $renderer->setTemplate($this->_itemRenders[$type]['template'])
                     ->setRenderedBlock($this);
            $this->_itemRenders[$type]['renderer'] = $renderer;
        }
        return $this->_itemRenders[$type]['renderer'];
    }

    /**
     * Get item row html
     *
     * @param   Varien_Object $item
     * @return  string
     */
    public function getItemHtml(Varien_Object $item) {
        $type = $this->_getItemType($item);

        $block = $this->getItemRenderer($type)
                      ->setItem($item);
        return $block->toHtml();
    }


    public function getOrderInfoById($id){
        $order = Mage::getModel('sales/order')->loadByIncrementId($id);
        return $order;
    }

}