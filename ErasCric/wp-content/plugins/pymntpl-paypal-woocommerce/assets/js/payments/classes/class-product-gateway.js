import $ from 'jquery';
import {submitErrorMessage} from "@ppcp/utils";
import BaseGateway from "./class-base-gateway";

class ProductGateway extends BaseGateway {

    constructor(product, cart, props) {
        super(props);
        this.product = product;
        this.cart = cart;
        this.initialize();
    }

    initialize() {
        this.product.on('foundVariation', this.onFoundVariation.bind(this));
        this.product.on('resetVariation', this.onResetVariation.bind(this));
        this.setMaxWidth();
        super.initialize();
    }

    onInit(...params) {
        super.onInit(...params);
        this.cart.trigger('productButtonOnInit');
        if (this.product.isVariableProduct() && !this.product.isVariationSelected()) {
            this.disableButtons();
        }
    }

    setOptions(options) {
        for (let key in options) {
            this.setOption(key, options[key]);
        }
    }

    needsShipping() {
        return this.product.needsShipping();
    }

    getButtonContainer() {
        const container = document.querySelectorAll('.wc-ppcp-product-button-container');
        if (container && container.length > 0) {
            return container;
        }
        return document.getElementById('wc-ppcp-product-button-container');
    }

    setMaxWidth() {
        if (this.settings?.product?.button_width == 'add_to_cart') {
            this.buttonWidth = $('form.cart div.quantity').outerWidth(true) + $('.single_add_to_cart_button').outerWidth();
            var marginLeft = $('.single_add_to_cart_button').css('marginLeft');
            if (marginLeft) {
                this.buttonWidth += parseInt(marginLeft.replace('px', ''));
            }
            $(this.container).css('max-width', this.buttonWidth + 'px');
        }
    }

    submitError(error) {
        this.hideProcessing();
        submitErrorMessage(error, 'div.woocommerce-notices-wrapper');
    }

    getFunding() {
        const funding = [];
        const settings = this.settings?.funding || [];
        if (settings.includes('paypal')) {
            funding.push(paypal.FUNDING.PAYPAL);
        }
        if (settings.includes('paylater')) {
            funding.push(paypal.FUNDING.PAYLATER);
        }
        if (settings.includes('card')) {
            funding.push(paypal.FUNDING.CARD);
        }
        return this.getSortedFunding(funding);
    }

    addToCart() {
        return this.cart.addToCart({
            payment_method: this.id,
            product_id: this.product.getId(),
            variation_id: this.product.getVariationId(),
            qty: this.product.getQuantity(),
            variation: this.product.getVariationData()
        }).then(response => {
            if (response.code) {
                this.currentError = response;
            }
            return response;
        }).catch(err => {
            this.currentError = err;
            return this.submitError(err);
        })
    }

    createOrder(data, actions) {
        return this.addToCart();
    }

    createBillingAgreement(data, actions) {
        return this.addToCart().then(() => {
            return super.createBillingAgreement(data, actions);
        });
    }

    handleBillingToken(response) {
        super.handleBillingToken(response);
        this.processCartCheckout();
    }

    onFoundVariation(hasChanged, product) {
        if (this.hasChanged) {
            this.destroyButtons();
            this.createButton();
        } else {
            this.enableButtons();
        }
    }

    onResetVariation() {
        this.disableButtons();
    }

    enableButtons() {
        this?.$button?.removeClass('disabled');
        super.enableButtons();
    }

    disableButtons() {
        this?.$button?.addClass('disabled');
        super.disableButtons();
    }
}

export {ProductGateway};