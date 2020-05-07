/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list',
        'https://code.jquery.com/jquery-1.11.3.min.js',
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';

        const config = window.checkoutConfig.payment;

        if (config.paymentez_card.is_active) {
            rendererList.push(
                {
                    type: 'paymentez_card',
                    component: 'Paymentez_PaymentGateway/js/view/payment/method-renderer/paymentez_card'
                }
            );
        }
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
