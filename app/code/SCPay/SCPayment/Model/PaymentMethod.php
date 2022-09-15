<?php

/*
 * Created by ShareCommerce
 * Date 8 Sept 2022
 * PaymentMethod is base class / entry point for SCPay plugin.
 */


namespace SCPay\SCPayment\Model;


class PaymentMethod extends \Magento\Payment\Model\Method\AbstractMethod
{	
	protected $_code = 'scpayment';
	protected $_isInitializeNeeded = true;
    protected $_canCapture = true;
    protected $_canAuthorize = true;
    protected $_canRefund  = true;
    protected $_canVoid = true;
    protected $_isGateway = true;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = false;

    private $objConfigSettings;

    public function log($data){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/scpay.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($data);
    }

	public function assignData(\Magento\Framework\DataObject $data)
	{    	
		parent::assignData($data);

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$catalogSession = $objectManager->create('\Magento\Catalog\Model\Session');

		if(isset($data)) {
			if(!empty($data->getData()['additional_data'])) {
				$catalogSession->setTokenValue($data->getData()['additional_data']['temp_data']);
			}
		}

        return $this;
    }

    /**
     * Instantiate state and set it to state object
     * @param string $paymentAction
     * @param Varien_Object
     */
    public function initialize($paymentAction, $stateObject)
    {        
        $stateObject->setState('Pending_SCPay');
        $stateObject->setStatus('Pending_SCPay');
        $stateObject->setIsNotified(false); 
    }

    function getPaymentGatewayRedirectUrl() {
        if ($this->objConfigSettings['mode']) {
            return 'https://staging.payment.share-commerce.com/Payment';
        } else {        
            return 'https://payment.share-commerce.com/Payment';
        }
    }

    public function loadsettings(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $configSettings = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->objConfigSettings = $configSettings->getValue('payment/scpayment');
    }
}