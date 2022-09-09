<?php

/*
 * Created by ShareCommerce
 * Date 8 Sept 2022
 * AbstractCheckoutRedirectAction is used for intermediate for request and reponse.
 */

namespace SCPay\SCPayment\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\OrderFactory;
use Magento\Catalog\Model\Session as catalogSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session as Customer;
use SCPay\SCPayment\Controller\AbstractCheckoutAction;
use SCPay\SCPayment\Helper\Checkout;
use SCPay\SCPayment\Helper\SCPayRequest;

abstract class AbstractCheckoutRedirectAction extends AbstractCheckoutAction
{
    protected $objCheckoutHelper, $objCustomer;
    protected $objSCPayHashHelper, $objConfigSettings;
    protected $objCatalogSession, $objSCPayRequestHelper;

    public function __construct(Context $context,
            Session $checkoutSession, OrderFactory $orderFactory,
            Customer $customer, Checkout $checkoutHelper,
            SCPayRequest $scpayRequest,ScopeConfigInterface $configSettings ,
            catalogSession $catalogSession) {

        parent::__construct($context, $checkoutSession, $orderFactory);
        $this->objCheckoutHelper = $checkoutHelper;
        $this->objCustomer = $customer;
        $this->objSCPayRequestHelper = $scpayRequest;
        $this->objConfigSettings = $configSettings->getValue('payment/scpayment');
        $this->objCatalogSession = $catalogSession;        
    }

    //This object is hold the custom filed data for payment method like selected store Card's, other setting, etc.
    protected function getCatalogSession() {
        return $this->objCatalogSession;
    }

    //Get the Magento configuration setting object that hold global setting for Merchant configuration
    protected function getConfigSettings() {
        return $this->objConfigSettings;
    }

    //Get the scpay request helper class. It is responsible for construct the current user request for SCPay Payment Gateway.
    protected function getSCPayRequest($paramter) {
        return $this->objSCPayRequestHelper->scpay_construct_request($paramter);
    }

    //This is magento object to get the customer object.
    protected function getCustomerSession() {
        return $this->objCustomer;
    }

    //Get the SCPay cehckout object. It is reponsible for hold the current users cart detail's
    protected function getCheckoutHelper() {
        return $this->objCheckoutHelper;
    }

    //This function is used to redirect to customer message action method after make successfully payment / 123 payment type.
    protected function executeSuccessAction($request){
        if ($this->getCheckoutSession()->getLastRealOrderId()) {
            $this->_forward('Success','Payment','scpay', $request);
        }
    }
    
    //This function is redirect to cart after customer is cancel the payment.
    protected function executeCancelAction(){
        $this->getCheckoutHelper()->cancelCurrentOrder('');
        $this->getCheckoutHelper()->restoreQuote();
        $this->redirectToCheckoutCart();
    }    
}