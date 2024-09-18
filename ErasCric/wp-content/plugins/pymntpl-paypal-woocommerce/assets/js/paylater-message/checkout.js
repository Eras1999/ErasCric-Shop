import $ from 'jquery';
import PayLaterBaseMessage from "./base";
import {getSetting, getPayPalQueryParams, loadPayPalSdk} from "@ppcp/utils";
import cart from '@ppcp/cart';

class PayLaterMessageCheckout extends PayLaterBaseMessage {
    constructor(paypal, cart, props) {
        super('checkout', props);
        this.paypal = paypal;
        this.cart = cart;
        this.initialize();
    }

    initialize() {
        this.cart.on('updatedCheckout', this.createMessage.bind(this), 20);
        super.initialize();
    }

    getMessageContainer() {
        const fk = this.getSetting('checkoutLocation');
        let el;
        switch (fk) {
            case 'shop_table':
                el = document.getElementById('wc-ppcp-paylater-msg-checkout');
                break;
            case 'above_button':
                el = document.querySelector('.wc-ppcp-checkout-container');
                if (el) {
                    $(el).find('#wc-ppcp-paylater-msg-checkout').remove();
                    $(el).prepend('<div id="wc-ppcp-paylater-msg-checkout"></div>');
                    el = document.getElementById('wc-ppcp-paylater-msg-checkout');
                } else {
                    setTimeout(this.createMessage.bind(this), 500);
                }
        }
        return el;
    }

    getPlacement() {
        return 'payment';
    }

    getTotal() {
        return this.cart.getTotal();
    }
}

let params = null;
const paypalGatewayData = getSetting('ppcp_data');

if (paypalGatewayData && paypalGatewayData.placeOrderEnabled) {
    params = getPayPalQueryParams();
}

loadPayPalSdk(params).then(paypal => {
    new PayLaterMessageCheckout(paypal, cart, {queryParams: getPayPalQueryParams()});
})

export default PayLaterMessageCheckout;

export {PayLaterMessageCheckout}
