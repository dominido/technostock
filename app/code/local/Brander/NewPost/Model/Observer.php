<?php

class Brander_NewPost_Model_Observer extends Varien_Object
{
	public function saveShippingMethod($observer){
		$request = $observer->getEvent()->getRequest();
		$quote = $observer->getEvent()->getQuote();
        if($request->getParam('shipping_method') == 'brander_newpost_brander_newpost') {
            $shippingOptions = $request->getParam('shipping_newpost');
            $quote->getShippingAddress()->setStreet($shippingOptions['address']);
            //$quote->setShippingComment($shippingOptions['address']);
            $quote->collectTotals()->save();
        }
	}

	public function salesQuoteItemSetDimensions($observer){
		$quoteItem = $observer->getQuoteItem();
		$product = $observer->getProduct();
		$quoteItem->setWidth($product->getWidth());
		$quoteItem->setHeight($product->getHeight());
		$quoteItem->setDepth($product->getDepth());
	}
}