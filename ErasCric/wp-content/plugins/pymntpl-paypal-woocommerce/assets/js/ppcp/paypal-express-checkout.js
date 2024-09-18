import {CheckoutGateway, BaseGateway} from '../payments/classes';
import $ from 'jquery';
import cart from '@ppcp/cart';

class PayPalExpressCheckoutGateway extends CheckoutGateway {

    updatedCheckout() {
        BaseGateway.prototype.initialize.apply(this, arguments);
        this.paymentMethodSelected();
    }

    paymentMethodSelected() {

    }

    hidePaymentButton() {
        this.displayPlaceOrderButton();
        this.displayPaymentReadyMessage();
        const container = super.getButtonContainer();
        if (container) {
            $(container).hide();
        }
    }

    getButtonContainer() {
        if (this.settings?.expressElement) {
            const container = document.querySelector(this.settings.expressElement);
            if (container) {
                return container;
            }
        }
        return document.getElementById('wc-ppcp-express-button');
    }

    onInit(source, data, actions) {
        this.cart.trigger('expressCheckoutButtonOnInit');
    }

    onClick(data, actions) {
        $('[name="terms"]').prop('checked', true).trigger('change');
        $(`[name="payment_method"][value="${this.id}"]`).prop("checked", true).trigger('click');
    }

    getFunding() {
        const funding = [];
        if (this.isFundingActive('paypal') && this.isSectionEnabled('paypal', 'express_checkout')) {
            funding.push(paypal.FUNDING.PAYPAL);
        }
        if (this.isFundingActive('paylater') && this.isSectionEnabled('paylater', 'express_checkout')) {
            funding.push(paypal.FUNDING.PAYLATER);
        }
        if (this.isFundingActive('venmo') && this.isSectionEnabled('venmo', 'express_checkout')) {
            funding.push(paypal.FUNDING.VENMO);
        }
        if (this.isFundingActive('card') && this.isSectionEnabled('credit_card', 'express_checkout')) {
            funding.push(paypal.FUNDING.CARD);
        }
        return funding;
    }

    createButton() {
        super.createButton();
        this.$button?.show();
        this.addExpressClasses();
    }

    addExpressClasses() {
        if (this.$button) {
            const count = Object.keys(this.buttons).length;
            this.$button.addClass(`button-count_${count}`);
        }
    }

    getPaymentType() {
        return 'express';
    }

    isCheckoutReviewRequired(token) {
        return true;
    }
}

new PayPalExpressCheckoutGateway(cart, {id: 'ppcp', context: 'checkout'});