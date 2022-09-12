<?php
/*
 * Created by ShareCommerce
 * Date 8 Sept 2022
 * This Response action method is responsible for handle the SCPayment payment gateway response.
 */

namespace SCPay\SCPayment\Controller\Payment;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;

class Response extends \SCPay\SCPayment\Controller\AbstractCheckoutRedirectAction
{
	public function log($data){
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/scpay.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info($data);
	}

	public function execute()
	{
		$this->log('in_response');
		$this->log('order_id'.$_REQUEST['MerchantRef1']);
		if(empty($_REQUEST) || empty($_REQUEST['MerchantRef1'])){
			$this->_redirect('');
			return;
		}

		$configHelper = $this->getConfigSettings();
		$objCustomerData = $this->getCustomerSession();

		$signstr = "";
        foreach ($_REQUEST as $key => $value) {
            if ($key == 'SCSign' || $key == 'route') {
                continue;
            }
            
            $signstr .= $value;
        }

		$hash_signstr = hash_hmac('sha256', $signstr, $configHelper['secretKey']);

		$resp_code              = $_REQUEST['RespCode'];
		$resp_desc              = $_REQUEST['RespDesc'];
		$transaction_ref_no 	= $_REQUEST['TxnRefNo']; 
		$order_id 		 	    = $_REQUEST['MerchantRef1'];
		
		$order = $this->getOrderDetailByOrderId($order_id);

		if(empty($order)) {
			$this->_redirect('');
			return;
		}

		if($hash_signstr !== $_REQUEST['SCSign']) {
			$order->setState(\Magento\Sales\Model\Order::STATUS_FRAUD);
			$order->setStatus(\Magento\Sales\Model\Order::STATUS_FRAUD);
			$order->save();
			
			$this->_redirect('');
			return;
		}

		$this->log($resp_code);

		if($resp_code == '00' || $resp_desc == 'Success') {	// success		
			$order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
			$order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
			$order->save();				

			$this->executeSuccessAction($_REQUEST);
			return;
		} else {
			$this->executeCancelAction();
			return;
		}
	}
}