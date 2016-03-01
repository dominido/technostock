<?php

require_once 'Mage/Catalog/controllers/Product/CompareController.php';
class Brander_Account_Compare_Product_CompareController extends Mage_Catalog_Product_CompareController
{
    public function addAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_redirectReferer();
            return;
        }

        $productId = (int) $this->getRequest()->getParam('product');
        if ($productId
            && (Mage::getSingleton('log/visitor')->getId() || Mage::getSingleton('customer/session')->isLoggedIn())
        ) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

            if ($product->getId()/* && !$product->isSuper()*/) {
                Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
                Mage::getSingleton('core/session')->addSuccess(
                    $this->__('The product %s has been added to comparison list.', Mage::helper('core')->escapeHtml($product->getName()))
                );
                Mage::dispatchEvent('catalog_product_compare_add_product', array('product'=>$product));
            }

            Mage::helper('catalog/product_compare')->calculate();
        }

        $this->_redirectReferer();
    }
}