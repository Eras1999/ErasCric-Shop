import $ from 'jquery';
import BaseGateway from './class-base-gateway';
import {
    getFieldValue,
    submitErrorMessage,
    setFieldValue,
    convertPayPalAddressToCart,
    extractFullName,
    isValidAddress,
    isValid,
    extractShippingMethod,
    getPage
} from "@ppcp/utils";
import {isEmpty, isEqual} from 'lodash';

class CheckoutGateway extends BaseGateway {

    constructor(cart, props) {
        super(props);
        this.cart = cart;
        this.shippingAddressChanged = false;
        this.initialize();
    }


    initialize() {
        super.initialize();
        this.setVariable('readyToCheckout', false);
        this.actions = {};
        this.cart.on('updatedCheckout', this.updatedCheckout.bind(this));
        $(document.body).on('payment_method_selected', this.paymentMethodSelected.bind(this));
        $(document.body).on(`checkout_place_order_${this.id}`, this.validateCheckoutFields.bind(this));
        $(document.body).on('click', '.wc-ppcp-cancel__payment', this.cancelPayment.bind(this));
        $(document.body).on('change', '[name="terms"]', this.handleTermsClick.bind(this));
        $(document.body).on('change', '[type="checkbox"]', this.handleCheckboxChange.bind(this));
        window.addEventListener('hashchange', this.handleHashError.bind(this));
        this.handleOrderPay();
    }

    needsShipping() {
        return getPage() !== 'order_pay' && this.cart.needsShipping();
    }

    getFunding() {
        const funding = super.getFunding();
        if (this.isFundingActive('venmo') && this.isSectionEnabled('venmo', 'checkout')) {
            funding.push(paypal.FUNDING.VENMO);
        }
        return funding;
    }

    updatedCheckout() {
        super.initialize();
        this.paymentMethodSelected();
        if (this.isOrderReview()) {
            this.displayPaymentReadyMessage();
        } else if (this.isReadyToCheckout()) {
            this.displayPaymentReadyMessage();
        }
    }

    /**
     * Method that is called when a payment method is selected
     */
    paymentMethodSelected() {
        if (this.isPaymentGatewaySelected() && !this.isReadyToCheckout()) {
            this.displayPaymentButton();
        } else {
            this.hidePaymentButton();
        }
    }

    handleHashError(e) {
        var match = e.newURL.match(/ppcp_error=(.*)/);
        if (match) {
            const {1: error} = match;
            if (error == 'true') {
                this.displayPaymentButton();
                history.pushState({}, '', window.location.pathname + window.location.search);
            }
        }
    }

    isOrderReview() {
        let match = window?.location?.search?.match(/_ppcp_order_review=(.*)/);
        return match?.length > 0;
    }

    handleOrderPay() {
        if (this.isOrderReview()) {
            try {
                let match = window?.location?.search?.match(/_ppcp_order_review=(.*)/);
                const {1: obj} = match;
                const {payment_method, paypal_order, fields} = JSON.parse(atob(decodeURIComponent(obj)));
                setFieldValue(this.order_field_key, paypal_order, '');
                if (!isEmpty(fields)) {
                    for (let key in fields) {
                        setFieldValue(key, fields[key], '');
                    }
                }
                this.setVariable('readyToCheckout', true);
                this.hidePaymentButton();
                if (this.needsShipping() && $('[name="ship_to_different_address"]')?.length) {
                    const bool = !isEqual(this.getCartAddress('billing'), this.getCartAddress('shipping'))
                    $('[name="ship_to_different_address"]').prop('checked', bool).trigger('change');
                }
            } catch (error) {
                console.log(error);
            }
        }
    }

    createOrder(data, actions) {
        if (this.isPage('checkout')) {
            const formData = {...this.convertFormToData(), context: this.getPaymentType()};
            return this.cart.createOrder(formData);
        } else {
            return this.cart.doOrderPay(this.id);
        }
    }

    createBillingAgreement(data, actions, extraData = null) {
        if (this.isPage('checkout')) {
            extraData = {...this.convertFormToData(), context: this.getPaymentType()};
        }
        return super.createBillingAgreement(data, actions, extraData);
    }

    createButton() {
        super.createButton();
        this.paymentMethodSelected();
    }

    displayPaymentButton() {
        this.getButton()?.show();
        this.hidePlaceOrderButton();
    }

    hidePaymentButton() {
        this.getButton()?.hide();
        this.displayPlaceOrderButton();
    }

    displayPlaceOrderButton() {
        this.getPlaceOrderButton()?.removeClass('wc-ppcp-hide-button');
    }

    hidePlaceOrderButton() {
        this.getPlaceOrderButton()?.addClass('wc-ppcp-hide-button');
    }

    getPlaceOrderButton() {
        let $button = $('#place_order');
        if (!$button.length) {
            $button = $('[name="woocommerce_checkout_place_order"]');
        }
        if (!$button.length) {
            $button = $('form.checkout button[type="submit"]');
        }
        if (!$button.length) {
            $button = $('form[id="order_review"] button[type="submit"]');
        }
        return $button;
    }

    getButtonPlacement() {
        return this.settings?.buttonPlacement || 'place_order';
    }

    isPlaceOrderPlacement() {
        return this.getButtonPlacement() == 'place_order';
    }

    getButtonContainer() {
        let $parent = null;
        switch (this.getButtonPlacement()) {
            case 'place_order':
                let $checkoutContainer = $('.wc-ppcp-checkout-container');
                if (!$checkoutContainer.length) {
                    this.getPlaceOrderButton().after('<div class="wc-ppcp-checkout-container"></div>');
                }
                break;
            case 'payment_method':
                $parent = $(`div.payment_method_${this.id}`);
                $('.wc-ppcp-payment-method__container').append('<div class="wc-ppcp-checkout-container"></div>');
                break;
        }
        // add container to parent;
        return document.querySelector('.wc-ppcp-checkout-container');
    }

    isPaymentGatewaySelected() {
        return $('[name="payment_method"]:checked')?.val() === this.id;
    }

    submitError(error) {
        if (error?.code === 'validation_errors') {
            return submitErrorMessage(error.data.messages, this.getForm(), 'checkout');
        }
        return submitErrorMessage(error, this.getForm(), 'checkout');
    }

    getShippingPrefix() {
        if ($('[name="ship_to_different_address"]')?.length) {
            if ($('[name="ship_to_different_address"]').is(':checked')) {
                return 'shipping';
            }
        }
        return 'billing';
    }

    handleOnApproveResponse(data, response) {
        this.populateCheckoutFields(response);
        this.processCheckout(data, response);
    }

    processCheckout(data, response) {
        this.hidePaymentButton();
        this.setVariable('readyToCheckout', true);
        if (this.update_required) {
            $(document.body).one('updated_checkout', () => {
                if (data.billingToken && this.needsShipping()) {
                    // show message that they can click place order
                    this.displayPaymentReadyMessage();
                } else {
                    this.submitCheckoutForm(response);
                }
            });
            $('[name="billing_country"],[name="billing_state"]').trigger('change');
            if (this.shipToDifferentAddressChecked()) {
                $('[name="shipping_country"],[name="shipping_state"]').trigger('change');
            }
            $(document.body).trigger('update_checkout', {update_shipping_method: false});
        } else {
            this.submitCheckoutForm(response);
        }
    }

    handleBillingToken(token, data) {
        this.update_required = this.isCheckoutReviewRequired(token);
        super.handleBillingToken(token);
        this.maybeShipToDifferentAddress();
        this.processCheckout(data);
    }

    isCheckoutReviewRequired(token) {
        if (this.needsShipping() && !this.isPayPalAddressDisabled()) {
            // if the address changed, then an update is required
            if (!isEmpty(token.shipping_address)) {
                if (!isEqual(
                    {
                        city: token.shipping_address.city,
                        state: token.shipping_address.state,
                        postal_code: token.shipping_address.postal_code,
                        country_code: token.shipping_address.country_code
                    },
                    {
                        city: getFieldValue('shipping_city'),
                        state: getFieldValue('shipping_state'),
                        postal_code: getFieldValue('shipping_postcode'),
                        country_code: getFieldValue('shipping_country')
                    }
                )) {
                    return true;
                }
            }
        }
        return false;
    }

    populateCheckoutFields(response) {
        if (!this.isAddressPopulationDisabled() && !isEmpty(response?.payer?.address)) {
            let address = convertPayPalAddressToCart(response.payer.address);
            if (isValidAddress(address, ['first_name', 'last_name']) && !isEqual(this.getCartAddress('billing'), address)) {
                this.populateBillingAddressFields(address);
            }
        }
        if (response?.payer?.name) {
            if (!isValid('billing_first_name')) {
                this.populateNameFields(response.payer.name, 'billing');
            }
        }
        // only overwrite billing email if the field is blank
        if (response?.payer?.email_address && !isValid('billing_email')) {
            setFieldValue('billing_email', response.payer.email_address);
        }
        if (response?.payer?.phone?.phone_number?.national_number) {
            setFieldValue('billing_phone', response.payer.phone.phone_number.national_number);
            if (this.needsShipping()) {
                setFieldValue('shipping_phone', response.payer.phone.phone_number.national_number);
            }
        }
        // update the shipping address if one is included
        if (!this.isAddressPopulationDisabled() && this.needsShipping()) {
            if (!isEmpty(response?.purchase_units?.[0]?.shipping?.address)) {
                let address = convertPayPalAddressToCart(response.purchase_units[0].shipping.address);
                let name = '';
                if (!isEqual(this.cartAddress, address)) {
                    this.update_required = true;
                    this.cartAddress = address;
                    this.populateShippingAddressFields(address);
                    $(document.body).one('updated_checkout', () => this.populateShippingAddressFields(address));
                }
                if (response.purchase_units[0].shipping?.name?.full_name) {
                    name = extractFullName(response.purchase_units[0].shipping.name.full_name);
                    this.populateNameFields(name, 'shipping');
                }
                // add billing address if possible
                if (!isValidAddress(this.getCartFullAddress('billing'), ['phone', 'email'])) {
                    if (name && !isValid('billing_first_name') && !isValid('billing_last_name')) {
                        this.populateNameFields(name, 'billing');
                    }
                    this.populateBillingAddressFields(address);
                }
            }
            this.maybeShipToDifferentAddress();
        }
    }

    maybeShipToDifferentAddress() {
        // compare billing and shipping address. If not equal, then select ship to different address
        if ($('[name="ship_to_different_address"]')?.length) {
            const bool = !isEqual({
                ...this.getCartAddress('billing'),
                name: this.getFullName('billing')
            }, {...this.getCartAddress('shipping'), name: this.getFullName('shipping')})
            $('[name="ship_to_different_address"]').prop('checked', bool).trigger('change');
        }
    }

    getForm() {
        if (this.isPage('checkout')) {
            return $(this.container).closest('form.checkout');
        } else {
            return $(this.container).closest('form');
        }
    }

    validateTerms() {
        if ($('[name="terms"]').length) {
            return $('[name="terms"]').is(':checked');
        }
        return true;
    }

    validateCheckoutFields() {
        if (!this.validateTerms()) {
            this.submitError({code: 'terms'});
            return false;
        }
        return true;
    }

    handleTermsClick() {
        if (this.isPlaceOrderPlacement()) {
            if ($('[name="terms"]').length) {
                const checked = $('[name="terms"]').is(':checked');
                if (checked) {
                    this.enableButtons();
                } else {
                    this.disableButtons();
                }
            }
        }
    }

    handleCheckboxChange() {
        setTimeout(this.handleTermsClick.bind(this), 250);
    }

    onInit(source, data, actions) {
        super.onInit(source, data, actions);
        this.handleTermsClick();
    }

    onClick(data, actions) {
        if (this.isPlaceOrderPlacement() && !this.validateTerms()) {
            this.submitError({code: 'terms'});
        }
    }

    onShippingChange(data, actions) {
        let shippingMethod;
        if (data?.selected_shipping_option?.id) {
            shippingMethod = extractShippingMethod(data.selected_shipping_option.id);
            for (let index of Object.keys(shippingMethod)) {
                const method = shippingMethod[index];
                const el = $(`[name="shipping_method[${index}]"][value="${method}"]`);
                if (el.length) {
                    el.prop('checked', true);
                }
            }
        }
        if (data.shipping_address) {
            this.shippingAddressChanged = !isEqual(
                convertPayPalAddressToCart(data.shipping_address, true),
                {
                    city: getFieldValue('shipping_city'),
                    state: getFieldValue('shipping_state'),
                    postcode: getFieldValue('shipping_postcode'),
                    country: getFieldValue('shipping_country')
                }
            );
        }
        this.cart.trigger('checkout_on_shipping_change', data, shippingMethod, this);
        return super.onShippingChange(data, actions, this.convertFormToData());
    }

    shipToDifferentAddressChecked() {
        if ($('[name="ship_to_different_address"]')?.length) {
            return $('[name="ship_to_different_address"]').is(':checked');
        }
        return false;
    }

    displayPaymentReadyMessage() {
        $('.wc-ppcp-popup__container').hide();
        $('.wc-ppcp-order-review-message__container').show();
        const txt = $('.wc-ppcp-order-review__message').text().replace('%s', $('#place_order').text());
        $('.wc-ppcp-order-review__message').text(txt);
    }

    hidePaymentReadyMessage() {
        $('.wc-ppcp-popup__container').show();
        $('.wc-ppcp-order-review-message__container').hide();
    }

    /**
     * Cancels an existing payment method
     */
    cancelPayment(e) {
        e.preventDefault();
        this.setVariable('readyToCheckout', false);
        this.hidePaymentReadyMessage();
        this.displayPaymentButton();
    }

    getProcessingSelector() {
        return this.container;
    }

    getProcessingMessage() {
        return null;
    }

    fetchBillingToken(token) {
        this.showProcessing();
        return super.fetchBillingToken(token).then(response => {
            this.hideProcessing();
            return response;
        });
    }

    isReadyToCheckout() {
        return this.getVariable('readyToCheckout', false);
    }

    convertFormToData() {
        return $('form.checkout').serializeArray().reduce((prev, current) => ({...prev, [current.name]: current.value}), {});
    }

    submitCheckoutForm(order) {
        if (this.cart.applyFilters('checkout_submit_form', true, order, this)) {
            this.getForm().submit();
        }
    }

    getPaymentType() {
        return 'checkout';
    }

    isPayPalAddressDisabled() {
        return this.settings.paypalAddressDisabled;
    }

    isValidationEnabled() {
        return this.settings.checkoutValidationEnabled;
    }

    isAddressPopulationDisabled() {
        return this.isPayPalAddressDisabled() || !this.shippingAddressChanged;
    }
}

export {
    CheckoutGateway
}