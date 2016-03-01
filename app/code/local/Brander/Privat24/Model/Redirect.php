<?php
class Brander_Privat24_Model_Redirect extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'privat24_redirect';

    protected $_formBlockType = 'privat24/message';

    /**
     * Payment Method features
     * 
     * @var bool
     */
    protected $_canUseForMultishipping  = false;
    protected $_canUseInternal          = false;
    protected $_isInitializeNeeded      = true;

    /**
     * Instantiate state and set it to state object
     * 
     * @param string $paymentAction
     * @param Varien_Object $stateObject
     */
    public function initialize($paymentAction, $stateObject)
    {
        $stateObject->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);
        $stateObject->save();
    }

    /**
     * Return Order place redirect url
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('privat24/payment/redirect', array('_secure' => true));
    }

    /**
     * Return Privat24 place URL
     * 
     * @return string
     */
    public function getPrivat24PlaceUrl()
    {
        return $this->getConfigData('test') ? 'https://api.privatbank.ua/p24api/ishop'
            : 'https://api.privatbank.ua/p24api/ishop';	
    }

    /**
     * Return redirect form fields
     * @return array
     */
    public function getRedirectFormFields()
    {
        $result = array();
        $session = Mage::getSingleton('checkout/session');
        $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
        
        if (!$order->getId()) {
            return $result;
        }
		
		$items = $order->getAllItems();
		$name=array();
		foreach ($items as $itemId => $item){
			$name[] = $item->getName();
		}
		$details = implode(', ',$name);
        $result['merchant'] = $this->getConfigData('login');
        $result['amt'] = $order->getBaseGrandTotal();
        $result['order'] = $order->getIncrementId();
        //$result['ext_details'] = Mage::helper('privat24')->__('Shopping in '). Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_STORE_STORE_NAME);
        $result['ext_details'] = '';
		$result['details'] = $details;
		$result['pay_way'] = 'privat24';
		$result['return_url'] = Mage::getUrl('privat24/payment/return');
		$result['server_url'] = Mage::getUrl('privat24/payment/returnapi');
		$result['ccy'] = $order->getBaseCurrencyCode();
        return $result;
    }

    /**
     * Check incoming request CRC
     *
     * @param array $request
     * @return bool
     */
    public function validateRequest($request)
    {
        
		if(isset($request['payment']) && isset($request['signature'])){
			$signature_post = $request['signature'];
			$payment = $request['payment'];
			$signature_post = $request['signature'];
			$crc = sha1(md5($payment . $this->getConfigData('password1')));

			return strtoupper($request['signature']) == strtoupper($crc);
			
		} else {
			return false;
		}
    }
	
	public function saveOrderState($request)
	{
		$payment = $request['payment'];
		parse_str($payment, $output);
		
		if(is_array($output) && count($output) > 0){
			$test_module = $this->getConfigData('test');
			if(isset($output['state'])){
				$state = $output['state'];
				if(($test_module && $state == 'test') || (!$test_module && $state == 'ok')){
					return $this->saveOrder($output);
				} else {
					$this->setCanceled($output);
					return false;
				}					
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	protected function saveOrder($output)
	{
		if(isset($output['order'])){	
			try{
				$text = $this->getDataText($output);
				$this->setProcessing($output['order'], $text);
				return true;
			} catch (Exception $e){
				Mage::log($e->getMessage(), null, 'privat24.log');
				return false;
			}
		} else {	
			return false;
		}
	}
	
	public function setProcessing($id, $text)
	{
		try{
			$order = Mage::getModel('sales/order')->loadByIncrementId($id);
			$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $text);
			$order->setStatus('processing');
			$order->setIsNotified(false);
			$order->save();
			return true;
		} catch (Exception $e){
			Mage::log($e->getMessage(), null, 'privat24.log');
			return false;
		}
	}
	
	public function setCanceled($output)
	{
		if(isset($output['order'])){
			try{
				$text = $this->getDataText($output);
				$order = Mage::getModel('sales/order')->loadByIncrementId($output['order']);
				$order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, $text);
				$order->setStatus('canceled');
				$order->save();
				return true;
			} catch (Exception $e){
				Mage::log($e->getMessage(), null, 'privat24.log');
				return false;
			}
		} else {	
			return false;
		}
	}
	
	public function getDataText($output)
	{
		$datetime = '';
		$sender_phone = '';
		$ref = '';
		$text = '';
		if(isset($output['date'])){
			$datatime = $output['date'];
			$newtext = chunk_split($datatime, 6, ' ');
			$explode = explode(' ', $newtext);
			$date = substr(chunk_split($explode[0], 2, '-'), 0, -1); 
			$time = substr(chunk_split($explode[1], 2, ':'), 0, -1);
			$datetime = Mage::helper('privat24')->__('Payment time') . ' <strong>' . $date.' '.$time . '</strong>';
		}
		if(isset($output['sender_phone'])){
			$sender_phone = Mage::helper('privat24')->__('Sender phone') . ' <strong>' . $output['sender_phone'] . '</strong>';
		}
		
		if(isset($output['ref'])){
			$ref = Mage::helper('privat24')->__('Reference') . ' <strong>' . $output['ref'] . '</strong>';
		}
		$text = $datetime . '<br>' . $sender_phone . '<br>' . $ref;
		
		return $text;
	}

}