define([
    "jquery",
    "Magento_Checkout/js/model/quote",
    "Magento_Checkout/js/model/url-builder",
    "mage/storage",
    "Magento_Customer/js/customer-data",
    "Magento_Checkout/js/view/payment/default",
    "Magento_Checkout/js/action/place-order",
    "Magento_Checkout/js/action/select-payment-method",
    "Magento_Customer/js/model/customer",
    "Magento_Checkout/js/checkout-data",
    "Magento_Checkout/js/model/payment/additional-validators",
    "mage/url",
    "ko",
], function (
    $,
    quote,
    urlBuilder,
    storage,
    customerData,
    Component,
    placeOrderAction,
    selectPaymentMethodAction,
    customer,
    checkoutData,
    additionalValidators,
    url,
    ko
) {
    "use strict";

    self.specializationArray = ko.observableArray();

    return Component.extend({
        defaults: {
            template: "SCPay_SCPayment/payment/SCPayment",
        },
        placeOrder: function (data, event) {
            if (event) {
                event.preventDefault();
            }
            var self = this,
                placeOrder,
                emailValidationResult = customer.isLoggedIn(),
                loginFormSelector = "form[data-role=email-with-possible-login]";
            if (!customer.isLoggedIn()) {
                $(loginFormSelector).validation();
                emailValidationResult = Boolean(
                    $(loginFormSelector + " input[name=username]").valid()
                );
            }
            if (
                emailValidationResult &&
                this.validate() &&
                additionalValidators.validate()
            ) {
                this.isPlaceOrderActionAllowed(false);
                placeOrder = placeOrderAction(
                    this.getData(),
                    false,
                    this.messageContainer
                );

                $.when(placeOrder)
                    .fail(function () {
                        self.isPlaceOrderActionAllowed(true);
                    })
                    .done(this.afterPlaceOrder.bind(this));
                return true;
            }
            return false;
        },
        getData: function () {
            return {
                method: this.item.method,
                additional_data: {
                    temp_data: $("#" + this.getCode() + "_temp_data").val(),
                },
            };
        },
        selectPaymentMethod: function () {
            selectPaymentMethodAction(this.getData());
            checkoutData.setSelectedPaymentMethod(this.item.method);
            return true;
        },

        afterPlaceOrder: function () {
            window.location.replace(url.build("scpay/payment/request"));
        },
    });
});
