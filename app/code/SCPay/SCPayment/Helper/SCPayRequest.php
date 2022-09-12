<?php

/*
 * Created by ShareCommerce
 * Date 8 Sept 2022
 * SCPayRequest helper class is used to generate the current user request and send it to scpay payment gateway.
 */

namespace SCPay\SCPayment\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;

class SCPayRequest extends AbstractHelper{
	private $objConfigSettings;
	private $objStoreManagerInterface;

	function __construct(ScopeConfigInterface $configSettings, StoreManagerInterface $storeManagerInterface) {
		$this->objConfigSettings = $configSettings->getValue('payment/scpayment');
		$this->objStoreManagerInterface = $storeManagerInterface;
	}

	private $arraySCPayFormFields = array(
		"MerchantID"     		=> "",
		"CurrencyCode" 			=> "", 
		"TxnAmount" 			=> "",
		"MerchantOrderNo" 		=> "", 
		"MerchantOrderDesc" 	=> "",
		"MerchantRef1" 			=> "",
		"MerchantRef2" 			=> "", 
		"MerchantRef3" 			=> "",
		"CustReference" 		=> "",
		"CustName" 				=> "",
		"CustEmail" 			=> "",
		"CustPhoneNo" 			=> "",
		"CustAddress1" 			=> "",
		"CustAddress2" 			=> "",
		"CustCountryCode" 		=> "",
		"CustAddressState" 		=> "",
		"CustAddressCity" 		=> "",
		"RedirectUrl" 			=> ""
	);

	public function scpay_construct_request($parameter) {
		$this->generateSCPayCommonFormFields($parameter);

		$signstr = "";
        foreach ($this->arraySCPayFormFields as $key => $value) {
            $signstr .= $value;
        }

		$this->arraySCPayFormFields['SCSign'] = hash_hmac('sha256', $signstr, $this->objConfigSettings['secretKey']);

		$strHtml = '<form name="scpayform" action="'. $this->getPaymentGatewayRedirectUrl() .'" method="post"/>';

		foreach ($this->arraySCPayFormFields as $key => $value) {
			if (!empty($value)) {
				$strHtml .= '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($value) . '">';
			}
		}

		$strHtml .= '</form>';
		$strHtml .= '<script type="text/javascript">';
		$strHtml .= 'document.scpayform.submit()';
		$strHtml .= '</script>';			
		return $strHtml;
	}

	private function generateSCPayCommonFormFields($parameter) {
		$this->arraySCPayFormFields["MerchantID"] 			= $this->objConfigSettings['merchantId'];
		$this->arraySCPayFormFields["CurrencyCode"] 		= 'MYR';
		$this->arraySCPayFormFields["TxnAmount"] 			= $parameter['amount'];
		$this->arraySCPayFormFields["MerchantOrderNo"] 		= $parameter['order_id'] . '_' . time();
		$this->arraySCPayFormFields["MerchantOrderDesc"]  	= substr($parameter['payment_description'], 0 , 1000);
		$this->arraySCPayFormFields["MerchantRef1"] 		= $parameter['order_id'];
		$this->arraySCPayFormFields["MerchantRef2"] 		= "";
		$this->arraySCPayFormFields["MerchantRef3"] 		= "";
		$this->arraySCPayFormFields["CustReference"] 		= "";
		$this->arraySCPayFormFields["CustName"] 			= $parameter['customer_name'];
		$this->arraySCPayFormFields["CustEmail"] 			= $parameter['customer_email'];
		$this->arraySCPayFormFields["CustPhoneNo"] 			= $parameter['customer_phone'];
		$this->arraySCPayFormFields["CustAddress1"] 		= $parameter['customer_addr1'];
		$this->arraySCPayFormFields["CustAddress2"] 		= $parameter['customer_addr2'];
		$this->arraySCPayFormFields["CustCountryCode"] 		= $parameter['customer_country_code'];
		$this->arraySCPayFormFields["CustAddressState"] 	= $parameter['customer_city'];
		$this->arraySCPayFormFields["CustAddressCity"] 		= $parameter['customer_state'];
		$this->arraySCPayFormFields["RedirectUrl"] 			= $this->getMerchantReturnUrl();
    }

    function getPaymentGatewayRedirectUrl() {
    	if ($this->objConfigSettings['mode']) {
    		return 'https://staging.payment.share-commerce.com/Payment';
    	} else {  		
    		return 'https://payment.share-commerce.com/Payment';
    	}
    }

    function getMerchantReturnUrl() {
    	$baseUrl = $this->objStoreManagerInterface->getStore()->getBaseUrl();
    	return  $baseUrl.'scpay/payment/response';
    }
}