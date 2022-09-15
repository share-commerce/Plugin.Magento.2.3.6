<?php

/*
 * Created by ShareCommerce
 * Date 8 Sept 2022
 * Payment Request Controller is responsible for send request to ShareCommerce payment gateway.
 */

namespace SCPay\SCPayment\Controller\Payment;

class Request extends \SCPay\SCPayment\Controller\AbstractCheckoutRedirectAction
{	
	public function log($data){
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/scpay.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info($data);
	}

	public function execute() {
		$orderId = $this->getCheckoutSession()->getLastRealOrderId();

		if(empty($orderId)) {
			die('Authentication Error: Order is is empty.');
		}

		$order = $this->getOrderDetailByOrderId($orderId);

		if(!isset($order)) {
			$this->_redirect('');
			return;
		}
		
		$customerSession = $this->getCustomerSession();

		$item_count = count($order->getAllItems());
        $current_count = 0;
        $product_name = '';

        foreach($order->getAllItems() as $item) {

            $product_name .= $item->getName();
            $current_count++;

            if($item_count !== $current_count)
                $product_name .= ', ';
        }

        $product_name .= '.';

		$cust_email = '';
		$cust_name = '';
		$cust_phone = '';
		$cust_addr = '';
		$cust_country = '';
		$cust_city = '';
		$cust_state = '';

		$billingAddress = $order->getBillingAddress();
		$cust_email = $billingAddress->getEmail();
		$cust_name = $billingAddress->getFirstName() . ' ' . $billingAddress->getLastName();
		$cust_phone = $billingAddress->getTelephone();
		$cust_addr = $billingAddress->getStreet();
		$cust_country = $billingAddress->getCountryId();
		$cust_city = $billingAddress->getCity();
		$cust_state = $billingAddress->getRegion();
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		$baseurl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);

		$funscpay_args = array(
			'payment_description'   		=> substr($product_name,0,240),
			'order_id'              		=> $this->getCheckoutSession()->getLastRealOrderId(),
			'invoice_no'            		=> $this->getCheckoutSession()->getLastRealOrderId(),		
			'amount'                		=> round($order->getGrandTotal(),2),
			'customer_email'        		=> $cust_email,
			'customer_name'        			=> $cust_name,
			'customer_phone'       			=> $cust_phone,
			'customer_addr1'       			=> $cust_addr[0],
			'customer_addr2'       			=> $cust_addr[1],
			'customer_country_code'      	=> $cust_country,
			'customer_city'      			=> $cust_city,
			'customer_state'      			=> $cust_state,
			'result_url' 					=> $baseurl.'scpay/payment/response'
		);

		$this->log(json_encode($funscpay_args));

		echo $this->getSCPayRequest($funscpay_args);	
	}
}