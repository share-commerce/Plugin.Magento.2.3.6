define([
    "uiComponent",
    "Magento_Checkout/js/model/payment/renderer-list",
], function (Component, rendererList) {
    "use strict";
    rendererList.push({
        type: "scpayment",
        component: "SCPay_SCPayment/js/view/payment/method-renderer/SCPayment",
    });
    return Component.extend({});
});
