<?php

/*
 * Created by ShareCommerce
 * Date 8 Sept 2022
 * PaymentModeType is give the model for dropdown page in admin configuration setting page.
 */

namespace SCPay\SCPayment\Model;

class PaymentModeType extends \Magento\Payment\Model\Method\AbstractMethod
{
	public function toOptionArray()
	{
		return array(
			array('value' => 1, 'label' => 'Test Mode'),
			array('value' => 0, 'label' => 'Live Mode'),
		);
	}
}