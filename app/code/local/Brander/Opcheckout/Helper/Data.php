<?php

require_once 'Mage/Checkout/Helper/Data.php';

class Brander_Opcheckout_Helper_Data extends Mage_Checkout_Helper_Data {

    public function isActive() {
        return Mage::getStoreConfigFlag('opcheckout/general/enabled');
    }

    public function isOrderCommentEnabled() {
        return Mage::getStoreConfigFlag('opcheckout/order/opcheckout_order_comment') ? true : false;
    }

    public function isCouponDiscountEnabled() {
        return Mage::getStoreConfigFlag('opcheckout/order/opcheckout_order_couponcode') ? true : false;
    }

    public function isOrderDeliveryEnabled() {
        return Mage::getStoreConfigFlag('opcheckout/order/opcheckout_order_deliverydate') ? true : false;
    }

	public function showShippingAsBilling() {
		return Mage::getStoreConfigFlag('opcheckout/order/opcheckout_show_billing_same_as_shipping') ? true : false;
	}

	public function showCountries() {
		return Mage::getStoreConfigFlag('opcheckout/order/opcheckout_show_countries') ? true : false;
	}

	public function getDefaultCountryId() {
		return Mage::getStoreConfig('opcheckout/order/opcheckout_default_country');
	}

	public function showStates() {
		return Mage::getStoreConfigFlag('opcheckout/order/opcheckout_show_states') ? true : false;
	}

	public function getDefaultState() {
		return Mage::getStoreConfig('opcheckout/order/opcheckout_default_state');
	}

	public function enableCreateAccountCheckbox() {
		return Mage::getStoreConfigFlag('opcheckout/order/opcheckout_create_account_later_use') ? true : false;
	}

    public function setOrderCommentCompatible($observer) {
        $quote = $observer->getEvent()->getQuote();
        if ($this->isOrderCommentEnabled()) {
            $orderComment = trim($this->_getRequest()->getPost('order_comment'));
            if ($orderComment != "") {
                $observer->getEvent()->getOrder()->setOrderComment($orderComment);
            } else {
                $observer->getEvent()->getOrder()->setOrderComment(Mage::getSingleton('checkout/session')->getOpcheckoutOrderComment());
                Mage::getSingleton('checkout/session')->unsOpcheckoutOrderComment();
            }
        }
        $this->setGiftMessage($observer);
        $this->saveShippingArrivalDate($observer);
        $invetoryObserver = Mage::getModel('cataloginventory/observer');
        if (!$quote->getInventoryProcessed()) {
            $invetoryObserver->subtractQuoteInventory($observer);
            $invetoryObserver->reindexQuoteInventory($observer);
        }
    }

    public function isAllowedNewsletterSubscription() {
        $customerSession = Mage::getSingleton('customer/session');
        if (defined('Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG') && Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 && !$customerSession->isLoggedIn()) {
            return false;
        } else {
            if ($customerSession->isLoggedIn()) {
                $subscriber = Mage::getModel('newsletter/subscriber')->loadByCustomer($customerSession->getCustomer());
                return !$subscriber->isSubscribed() && Mage::getStoreConfig('opcheckout/order/opcheckout_newsletter_subscribe');
            }
            return Mage::getStoreConfig('opcheckout/order/opcheckout_newsletter_subscribe');
        }
    }

    public function isNewsletterSubscriptionChecked() {
        return (boolean) Mage::getStoreConfig('opcheckout/order/newsletter_checked_bydefault');
    }

	public function showAgreements() {
		return (boolean) Mage::getStoreConfig('opcheckout/order/opcheckout_agreements');
	}

	public function isAgreementsCheckedByDefault() {
		return (boolean) Mage::getStoreConfig('opcheckout/order/opcheckout_agreement_checked_default');
	}

    /*
     *  Set Order Delivery Date..
     */

    public function saveShippingArrivalDate($observer) {
        $DDate = $this->_getRequest()->getPost('shipping_arrival_date');
        if ($this->isOrderDeliveryEnabled()) {
            if ($DDate == "") {
                $DDate = Mage::getSingleton('checkout/session')->getShippingArrivalDate();
                Mage::getSingleton('checkout/session')->unsShippingArrivalDate();
            }
            $order = $observer->getEvent()->getOrder();
            $desiredArrivalDate = $DDate;
            if (isset($desiredArrivalDate) && !empty($desiredArrivalDate)) {
                $order->setShippingArrivalDate($desiredArrivalDate);
            }
        }
    }

    public function getFormatedDeliveryDateToSave($date = null) {
        if (empty($date) || $date == null || $date == '0000-00-00 00:00:00') {
            return null;
        }

        $timestamp = null;
        try {
            //TODO: add Better Date Validation
            $timestamp = strtotime($date);
            $dateArray = explode("-", $date);
            if (count($dateArray) != 3) {
                //invalid date
                return null;
            }
            $formatedDate = date('Y-m-d H:i:s', strtotime($date));
        } catch (Exception $e) {
            //TODO: email error 
            //return null if not converted ok
            return null;
        }

        return $formatedDate;
    }

    /*
     * @ Gift Messages for Order and Item
     */

    public function setGiftMessage($observer) {
        $message = $this->_getRequest()->getPost('giftmessage');
        if (empty($message)) {
            $message = Mage::getSingleton('checkout/session')->getData('opgiftmessage');
            Mage::getSingleton('checkout/session')->unsetData('opgiftmessage');
        }
        $quotemsg = array();
        $order = $observer->getEvent()->getOrder();
        if(is_array($message)) {
            foreach ($message as $msgid => $msg):
                if ($msg['type'] == 'quote') {
                    $m = $msg['message'];
                    $from = $msg['from'];
                    $to = $msg['to'];
                    $giftMessage = Mage::getModel('giftmessage/message');
                    if ($m !== '') {
                        $giftMessage->setSender($from);
                        $giftMessage->setRecipient($to);
                        $giftMessage->setMessage($m);
                        $giftMessage->save();
                    }
                    $order->setGiftMessageId($giftMessage->getId());
                } else if ($msg['type'] == 'quote_item') {
                    $m = $msg['message'];
                    $from = $msg['from'];
                    $to = $msg['to'];
                    $giftMessage = Mage::getModel('giftmessage/message');
                    if ($m !== '') {
                        $giftMessage->setSender($from);
                        $giftMessage->setRecipient($to);
                        $giftMessage->setMessage($m);
                        $giftMessage->save();
                    }
                    $quotemsg[$msgid] = $giftMessage->getId();
                }
            endforeach;
            $items = $order->getAllItems();
            foreach ($items as $itemId => $item) {
                if (array_key_exists($item['quote_item_id'], $quotemsg)) {
                    $item->setGiftMessageId($quotemsg[$item['quote_item_id']]);
                }
            }
        }
    }

}