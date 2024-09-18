import $ from 'jquery';
import PayLaterBaseMessage from "./base";
import cart from '@ppcp/cart';
import {loadPayPalSdk, getPayPalQueryParams} from "@ppcp/utils";

class PayLaterMessageCart extends PayLaterBaseMessage {
    constructor(paypal, cart, props) {
        super('cart', props);
        this.paypal = paypal;
        this.cart = cart;
        this.initialize();
    }

    initialize() {
        super.initialize();
        this.cart.on('cartUpdated', this.refreshMessage.bind(this));
    }

    async refreshMessage() {
        this.createMessage();
    }

    getTotal() {
        return this.cart.getTotal();
    }

    getMessageContainer() {
        const location = this.getSetting('cartLocation');
        let el = document.getElementById('wc-ppcp-paylater-msg-cart');
        if (el && this.getSetting('isShortcode')) {
            return el;
        }
        switch (location) {
            case 'shop_table':
                el = document.getElementById('wc-ppcp-paylater-msg-cart');
                break;
            case 'above_button':
                el = document.getElementById('wc-ppcp-cart-button-container');
                if (el) {
                    if (!$(el).find('#wc-ppcp-paylater-msg-cart')?.length) {
                        $(el).prepend('<div id="wc-ppcp-paylater-msg-cart"></div>');
                    }
                    el = document.getElementById('wc-ppcp-paylater-msg-cart');
                }
        }
        return el;
    }

    getPlacement() {
        return 'cart';
    }
}

const queryParams = getPayPalQueryParams();

loadPayPalSdk(queryParams).then(paypal => {
    new PayLaterMessageCart(paypal, cart, {queryParams});
}).catch(error => {
});

export {PayLaterMessageCart}