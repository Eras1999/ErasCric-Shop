import $ from 'jquery';
import apiFetch from "@wordpress/api-fetch";
import {defaultHooks} from "@wordpress/hooks";
import {
    loadPayPalSdk,
    getFieldValue,
    isValidAddress,
    extractShippingMethod,
    getRestRoute,
    getRestPath,
    convertPayPalAddressToCart,
    fieldsToJson,
    extractFullName,
    getSetting,
    setFieldValue,
    isValid,
    isValidFieldValue,
    getPayPalQueryParams,
    getErrorMessage
} from "@ppcp/utils";
import {isEmpty} from "lodash";

class BaseGateway {

    constructor({id, context, container = null}) {
        this.id = id;
        this.buttons = {};
        this.actions = {};
        this.context = context;
        this.settings = getSetting(`${id}_data`);
        this.container = container ? container : `li.payment_method_${id}`;
        this.order_field_key = `${this.id}_paypal_order_id`;
        this.billing_token_key = `${this.id}_billing_token`;
    }

    initialize() {
        //load the paypal script
        this.loadPayPalScript().then(() => {
            // setup required events;
            this.initializeEvents();
            // render the button
            this.createButton();
        });
    }

    initializeEvents() {

    }

    isActive() {
        return this.getData() !== null;
    }

    /**
     * Loads the PayPal JS SDK
     */
    loadPayPalScript() {
        return new Promise((resolve, reject) => {
            loadPayPalSdk(this.getPayPalSDKArgs()).then(paypal => {
                this.paypal = paypal;
                resolve();
            }).catch(error => {
                console.log(error);
                if (error?.code) {
                    this.submitError(getErrorMessage(error));
                }
                reject();
            })
        })
    }

    /**
     * Returns params used to laod the PayPal SDK
     * @returns {{}}
     */
    getPayPalSDKArgs() {
        return getPayPalQueryParams();
    }

    getClientId() {
        let {clientId = null, environment} = getSetting('generalData');
        if (!clientId && environment == 'sandbox') {
            clientId = 'sb';
        }
        return clientId;
    }

    getData() {
        let data = $(`${this.container}`).find('.wc-ppcp-payment-method-data').data('payment-method-data');
        if (!data) {
            return null;
        }
        return data;
    }

    setData(data) {
        $(`${this.container}`).find('.wc-ppcp-payment-method-data').data('payment-method-data', data);
    }

    setOption(key, value) {
        const data = this.getData();
        if (data) {
            data[key] = value;
            this.setData(data);
        }
    }

    setVariable(key, value) {
        const data = getSetting(`${this.id}_data`);
        data[key] = value;
    }

    getVariable(key, defaultValue = null) {
        const data = getSetting(`${this.id}_data`);
        if (data.hasOwnProperty(key)) {
            return data[key];
        }
        if (defaultValue) {
            this.setVariable(key, defaultValue);
        }
        return defaultValue;
    }

    /**
     * Fetches an option from the payment method data object.
     * @param key
     * @param defaultValue
     * @returns {null|*}
     */
    getOption(key, defaultValue = null) {
        const data = this.getData();
        if (data.hasOwnProperty(key)) {
            return data[key];
        } else {
            if (defaultValue) {
                this.setOption(key, defaultValue);
            }
        }
        return defaultValue;
    }

    createButton() {
        const container = this.getButtonContainer();
        if (container && !$(container).find('.paypal-buttons').length) {
            this.getSortedFunding(this.getFunding()).forEach(source => {
                const button = this.paypal.Buttons(this.getButtonOptions(source));
                this.buttons[source] = button;
                if (button.isEligible()) {
                    if (container instanceof NodeList) {
                        for (const node of container) {
                            button.render(node);
                        }
                    } else {
                        button.render(container);
                    }
                }
            });
            this.$button = $(container);
            defaultHooks.doAction('wcPPCPButtonCreated', this);
        }
    }

    getButton() {
        return this.$button;
    }

    destroyButtons() {
        Object.keys(this.buttons).forEach(key => {
            if (this.buttons[key]['close']) {
                this.buttons[key].close();
            }
        })
    }

    disableButtons() {
        Object.keys(this.actions).forEach(key => {
            this.actions[key].disable();
        });
    }

    enableButtons() {
        Object.keys(this.actions).forEach(key => {
            this.actions[key].enable();
        });
    }

    getFunding() {
        const funding = [];
        const settings = this.settings?.funding || [];
        if (this.settings?.paypal_sections?.includes(this.getPage())) {
            funding.push(paypal.FUNDING.PAYPAL);
        }
        if (settings.includes('paylater') && this.settings?.paylater_sections?.includes(this.getPage())) {
            if (this.isCheckoutFlow()) {
                funding.push(paypal.FUNDING.PAYLATER);
            }
        }
        if (settings.includes('card') && this.settings?.credit_card_sections?.includes(this.getPage())) {
            funding.push(paypal.FUNDING.CARD);
        }
        return funding;
    }

    isFundingActive(type) {
        return this.settings?.funding?.includes(type);
    }

    isSectionEnabled(type, section) {
        const key = `${type}_sections`;
        return this.settings?.[key]?.includes(section);
    }

    getSortedFunding(funding) {
        const sortOrder = this.settings.buttons_order || [];
        funding.sort((a, b) => {
            const indexA = sortOrder.indexOf(a);
            const indexB = sortOrder.indexOf(b);
            return indexA < indexB ? -1 : 1;
        });
        return funding;
    }

    getButtonContainer() {
        return null;
    }

    getButtonOptions(source) {
        const options = {
            fundingSource: source,
            style: this.getButtonStyle(source),
            onInit: (...args) => {
                this.onInit(source, ...args);
                $(document.body).triggerHandler('wc_ppcp_on_init', [this, source]);
            },
            onClick: (...args) => {
                this.onClick(...args);
                $(document.body).triggerHandler('wc_ppcp_on_click', [this, source]);
            },
            onApprove: (data, actions) => {
                this.onApprove(data, actions);
                $(document.body).triggerHandler('wc_ppcp_on_approve', [this, source]);
            },
            onCancel: (data) => {
                this.orderId = data.orderID;
                $(document.body).triggerHandler('wc_ppcp_on_cancel', [this, source, data]);
            },
            onError: (error) => {
                if (this.currentError) {
                    this.submitError(this.currentError);
                    this.currentError = null;
                } else {
                    this.submitError(error);
                }
                $(document.body).triggerHandler('wc_ppcp_on_error', [this, source, error]);
            },
            onDestroy: () => {
                $(document.body).triggerHandler('wc_ppcp_on_destroy', [this, source]);
            }
        };
        if (this.isCheckoutFlow()) {
            options.createOrder = (data, actions) => {
                return this.createOrder(data, actions);
            }
            if (this.needsShipping() && source !== paypal.FUNDING.VENMO) {
                options.onShippingChange = (...args) => {
                    return this.onShippingChange(...args);
                }
            }
        } else {
            options.createBillingAgreement = (...args) => {
                return this.createBillingAgreement(...args);
            }
        }
        return options;
    }

    needsShipping() {
        return this.getOption('needsShipping', false);
    }

    isCheckoutFlow() {
        return getPayPalQueryParams()?.vault !== 'true';
    }

    getTotal() {
        return this.getOption('total', 0);
    }

    getButtonStyle(source) {
        let style = null;
        switch (source) {
            case paypal.FUNDING.PAYPAL:
                style = this.settings?.buttons?.paypal;
                break;
            case paypal.FUNDING.PAYLATER:
                style = this.settings?.buttons?.paylater;
                break;
            case paypal.FUNDING.CARD:
                style = {...this.settings?.buttons?.card};
                if (style.tagline) {
                    delete style.tagline;
                    style.layout = 'vertical';
                } else {
                    style.layout = 'horizontal';
                }
                break;
            case paypal.FUNDING.VENMO:
                const {color = '', label = '', ...venmoStyle} = this.settings?.buttons?.paypal;
                style = venmoStyle;
                break;
        }
        if (style?.height) {
            style.height = parseInt(style.height);
        }
        return style;
    }

    onApprove(data, actions) {
        setFieldValue(this.order_field_key, data.orderID, '');
        if (data.billingToken) {
            setFieldValue(this.billing_token_key, data.billingToken, '');
            return this.fetchBillingToken(data.billingToken).then(response => {
                this.handleBillingToken(response, data);
            }).catch(error => {
                return this.submitError(error?.message);
            });
        } else {
            actions.order.get().then((res) => {
                this.handleOnApproveResponse(data, res);
            }).catch(error => this.submitError(error));
        }
    }

    handleOnApproveResponse(data, response) {
        this.populateCheckoutFields(response);
        this.processCartCheckout();
    }

    fetchBillingToken(token) {
        return apiFetch({
            method: 'GET',
            path: `/wc-ppcp/v1/billing-agreement/token/${token}`
        });
    }

    handleBillingToken(token, data) {
        if (!isEmpty(token.payer_info.billing_address)) {
            this.populateBillingAddressFields(convertPayPalAddressToCart(token.payer_info.billing_address));
        }
        if (token?.payer_info?.first_name) {
            if (!isValid('billing_first_name')) {
                setFieldValue('first_name', token.payer_info.first_name, 'billing');
            }
        }
        if (token?.payer_info?.last_name) {
            if (!isValid('billing_last_name')) {
                setFieldValue('last_name', token.payer_info.last_name, 'billing');
            }
        }
        if (token?.payer_info?.email) {
            if (!isValid('billing_email')) {
                setFieldValue('billing_email', token.payer_info.email);
            }
        }
        if (token?.payer_info?.phone) {
            if (!isValid('billing_phone')) {
                setFieldValue('billing_phone', token.payer_info.phone);
            }
        }
        if (this.needsShipping() && token.shipping_address) {
            if (!isEmpty(token.shipping_address)) {
                let address = convertPayPalAddressToCart(token.shipping_address);
                this.populateNameFields(extractFullName(token.shipping_address.recipient_name), 'shipping');
                this.populateShippingAddressFields(address);
                if (!isValidAddress(this.getCartFullAddress('billing'), ['phone', 'email'])) {
                    if (!isValidFieldValue(getFieldValue('billing_first_name')) && !isValidFieldValue(getFieldValue('billing_last_name'))) {
                        this.populateNameFields(token.shipping_address.recipient_name, 'billing');
                    }
                    this.populateBillingAddressFields(address);
                }
            }
        }
    }

    createOrder(data, actions) {
        return actions.order.create(args);
    }

    createBillingAgreement(data, actions, extraData = null) {
        return apiFetch({
            method: 'POST',
            url: getRestPath('/wc-ppcp/v1/billing-agreement/token'),
            data: {
                payment_method: this.id,
                context: this.getPage(),
                ...extraData
            }
        }).then(token => {
            return token;
        }).catch(error => {
            this.currentError = error;
        })
    }

    onShippingChange(data, actions, extraData = {}) {
        const address = convertPayPalAddressToCart(data?.shipping_address || {}, true);
        const shipping_method = extractShippingMethod(data?.selected_shipping_option?.id || '');
        return apiFetch({
            method: 'POST',
            url: getRestRoute('cart/shipping'),
            data: {
                order_id: data.orderID,
                address,
                shipping_method,
                payment_method: this.id,
                ...extraData
            }
        }).then(response => {
            if (response.code) {
                return actions.reject();
            } else {
                return actions.resolve();
            }
        }).catch(error => {
            return actions.reject();
        })
    }

    submitError(error) {

    }

    getShippingPrefix() {
        return 'shipping';
    }

    getCartAddress(type) {
        return {
            address_1: getFieldValue('address_1', type),
            address_2: getFieldValue('address_2', type),
            state: getFieldValue('state', type),
            city: getFieldValue('city', type),
            postcode: getFieldValue('postcode', type),
            country: getFieldValue('country', type)
        }
    }

    getCartFullAddress(type) {
        return {
            first_name: getFieldValue('first_name', type),
            last_name: getFieldValue('last_name', type),
            address_1: getFieldValue('address_1', type),
            address_2: getFieldValue('address_2', type),
            state: getFieldValue('state', type),
            city: getFieldValue('city', type),
            postcode: getFieldValue('postcode', type),
            country: getFieldValue('country', type)
        }
    }

    onInit(source, data, actions) {
        this.actions[source] = actions;
    }

    onClick(data, actions) {

    }

    isPage(page) {
        return this.getPage() === page;
    }

    getPage() {
        let page = getSetting('generalData')?.page;
        if (page == 'cart' && $(document.body).is('.woocommerce-checkout')) {
            page = 'checkout';
        }
        return page;
    }

    populateShippingAddressFields(address) {
        for (let key in address) {
            setFieldValue(key, address[key], 'shipping');
        }
    }

    populateBillingAddressFields(address) {
        for (let key in address) {
            setFieldValue(key, address[key], 'billing');
        }
    }

    populateNameFields(name, prefix = '') {
        let first_name, last_name;
        if (Array.isArray(name)) {
            [first_name, last_name] = name;
        } else {
            ({given_name: first_name, surname: last_name} = name);
        }
        setFieldValue('first_name', first_name, prefix);
        setFieldValue('last_name', last_name, prefix);
    }

    populateCheckoutFields(response) {
        if (!isEmpty(response?.payer?.address)) {
            let address = convertPayPalAddressToCart(response.payer.address);
            this.populateBillingAddressFields(address);
        }
        if (response?.payer?.name) {
            this.populateNameFields(response.payer.name, 'billing');
        }
        if (response?.payer?.email_address) {
            setFieldValue('billing_email', response.payer.email_address);
        }
        if (response?.payer?.phone?.phone_number?.national_number) {
            setFieldValue('billing_phone', response.payer.phone.phone_number.national_number);
            setFieldValue('shipping_phone', response.payer.phone.phone_number.national_number);
        }
        if (this.needsShipping() && response?.purchase_units?.[0]?.shipping) {
            const address = convertPayPalAddressToCart(response.purchase_units[0].shipping.address)
            this.populateShippingAddressFields(address);
            if (response.purchase_units[0].shipping?.name?.full_name) {
                const name = extractFullName(response.purchase_units[0].shipping.name.full_name);
                this.populateNameFields(name, 'shipping');
            }
            // add billing address if possible
            if (!isValidAddress(this.getCartFullAddress('billing'), ['phone', 'email'])) {
                this.populateBillingAddressFields(address);
            }
        }
    }

    processCartCheckout() {
        this.showProcessing();
        return apiFetch({
            method: 'POST',
            url: getRestRoute('cart/checkout'),
            data: this.getCartCheckoutData()
        }).then(response => {
            if (response.result && 'success' == response.result) {
                window.location = response.redirect;
            } else {
                if (response.messages) {
                    return this.submitError(response.messages);
                }
            }
        }).catch(error => {
            if (error.code) {
                this.submitError(error.message);
            }
        });
    }

    getCartCheckoutData() {
        return {payment_method: this.id, context: this.getPage(), ...fieldsToJson()};
    }

    getProcessingSelector() {
        return 'body';
    }

    showProcessing() {
        $(this.getProcessingSelector())?.block({
            message: this.getProcessingMessage(),
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
    }

    hideProcessing() {
        $(this.getProcessingSelector())?.unblock();
    }

    getProcessingMessage() {
        return `<div class="wc-ppcp-loader">
                        <div></div>
                        <div></div>
                        <div></div>
                      </div>`;
    }

    getFullName(prefix) {
        const names = [
            getFieldValue('first_name', prefix),
            getFieldValue('last_name', prefix)
        ].filter(Boolean);
        if (names.length == 0) {
            return null;
        } else if (names.length == 1) {
            return names[0];
        }
        return `${names[0]} ${names[1]}`
    }
}

export {BaseGateway};
export default BaseGateway;