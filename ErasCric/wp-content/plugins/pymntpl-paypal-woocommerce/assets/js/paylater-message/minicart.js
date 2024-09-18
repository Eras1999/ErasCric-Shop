import $ from 'jquery';
import PayLaterBaseMessage from "./base";
import cart from '@ppcp/cart';
import {loadPayPalSdk, getPayPalQueryParams} from "@ppcp/utils";

class PayLaterMessageMiniCart extends PayLaterBaseMessage {

    constructor(paypal, cart, props) {
        super('minicart', props);
        this.paypal = paypal;
        this.cart = cart;
        this.initialize();
    }

    initialize() {
        super.initialize();
        this.cart.on('fragmentsChanged', this.createMessage.bind(this));
    }

    getMessageContainer() {
        let el = null;
        $('#wc-ppcp-minicart-msg').remove();
        switch (this.getSetting('miniCartLocation')) {
            case 'cart_total':
                el = document.querySelector('.woocommerce-mini-cart__total');
                $(el).after('<p class="wc-ppcp-minicart-msg__container"><span id="wc-ppcp-minicart-msg"></span></p>');
                break;
            case 'checkout':
                el = document.querySelector('.woocommerce-mini-cart__buttons .checkout');
                $(el).after('<span id="wc-ppcp-minicart-msg"></span>');
                break;

        }
        if (el) {
            return document.getElementById('wc-ppcp-minicart-msg');
        }
        return document.querySelector('.woocommerce-mini-cart__total');
    }

    showContainer(container) {
        $(container).show();
    }

    hideMessage(container) {
        $(container).hide();
    }

    getTotal() {
        return this.cart.getTotal();
    }

    getPlacement() {
        return 'cart';
    }
}

const queryParams = getPayPalQueryParams();

loadPayPalSdk(queryParams).then(paypal => {
    new PayLaterMessageMiniCart(paypal, cart, {queryParams});
}).catch(error => {
});