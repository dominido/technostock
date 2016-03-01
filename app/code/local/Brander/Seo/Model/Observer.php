<?php
class Brander_Seo_Model_Observer
{
	public function changeRobots($observer)
	{

		if(Mage::helper('brander_seo')->checkCurrentStoreIsNoIndex()) {
			$layout = $observer->getEvent()->getLayout();
			$layout->getUpdate()->addUpdate('<reference name="head"><action method="setRobots"><value>NOINDEX,FOLLOW</value></action></reference>');
			$layout->generateXml();
			return $this;
		}

		$fullActionName = $observer->getEvent()->getAction()->getFullActionName();
        $params = $observer->getEvent()->getAction()->getRequest()->getParams();
        $seohelper = Mage::helper ('brander_seo');
        if($fullActionName == 'catalog_category_view')
        {
            //$params = $observer->getEvent()->getAction()->getRequest()->getParams();
            if(isset($params['id'])) {
                unset($params['id']);

            }
            if (isset($params['price'])) {
                $layout = $observer->getEvent()->getLayout();
                $layout->getUpdate()->addUpdate('<reference name="head"><action method="setRobots"><value>NOINDEX,NOFOLLOW</value></action></reference>');
                $layout->generateXml();
            } elseif (isset($params['p'])) {
                $layout = $observer->getEvent()->getLayout();
                $layout->getUpdate()->addUpdate('<reference name="head"><action method="setRobots"><value>NOINDEX,FOLLOW</value></action></reference>');
                $layout->generateXml();
            } elseif (count($params) > 1 || isset($params['order']) || isset($params['dir']) || isset($params['mode']) || isset($params['limit'])) {
                //$seohelper->checkParam($params)
                $layout = $observer->getEvent()->getLayout();
                $layout->getUpdate()->addUpdate('<reference name="head"><action method="setRobots"><value>NOINDEX,FOLLOW</value></action></reference>');
                $layout->generateXml();
            }
        }

		$disallowFullActions = array(
			'catalogsearch_result_index',
			'catalog_product_compare_index',
			'wishlist_index_index',
			'customer_account_index',
			'sales_order_history',
			'sales_order_view',
			'catalog_product_viewed',
            'sales_guest_form',
            'customer_account_login',
            'checkout_cart_index',
            'checkout_onepage_index'
		);

		if(in_array($fullActionName, $disallowFullActions))
		{
			$layout = $observer->getEvent()->getLayout();
			$layout->getUpdate()->addUpdate('<reference name="head"><action method="setRobots"><value>NOINDEX,NOFOLLOW</value></action></reference>');
			$layout->generateXml();
			return $this;
		}
	}
}