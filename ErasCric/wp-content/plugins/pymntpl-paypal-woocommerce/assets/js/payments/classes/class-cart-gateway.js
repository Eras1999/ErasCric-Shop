import $ from 'jquery';
import BaseGateway from "./class-base-gateway";
import {
    submitErrorMessage
} from "@ppcp/utils";

class CartGateway extends BaseGateway {

    constructor(cart, props) {
        super(props);
        this.cart = cart;
        this.initialize();
    }

    initialize() {
        this.cart.on('cartUpdated', this.onCartUpdated.bind(this));
        $(window).on('resize', this.doCartHtml.bind(this));
        super.initialize();
        this.doCartHtml();
    }

    onInit(...params) {
        super.onInit(...params);
        this.cart.trigger('cartButtonOnInit');
    }

    needsShipping() {
        return this.cart.needsShipping();
    }

    getButtonContainer() {
        return document.getElementById('wc-ppcp-cart-button-container');
    }

    onCartUpdated() {
        super.initialize();
        this.doCartHtml();
    }

    submitError(error) {
        this.hideProcessing();
        submitErrorMessage(error, 'div.woocommerce-notices-wrapper', 'checkout');
    }

    handleBillingToken(response) {
        super.handleBillingToken(response);
        this.processCartCheckout();
    }

    createOrder(data, actions) {
        return this.cart.createOrder({payment_method: this.id}).catch(error => {
            this.currentError = error;
            return error;
        });
    }

    doCartHtml() {
        const $button = $('.checkout-button');
        const width = $button.outerWidth();
        if (width && $('.wc-ppcp-cart-payments__container').length) {
            $('.wc-ppcp-cart-payments__container').width(width);
        }
        if ($button.css('float') !== 'none') {
            $('.wc-ppcp-cart-payments__container ').css('float', $button.css('float'));
        }
    }
}

export {CartGateway};