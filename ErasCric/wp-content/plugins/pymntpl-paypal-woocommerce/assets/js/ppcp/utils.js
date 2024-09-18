import {isEqual, isEmpty} from 'lodash';
import $ from 'jquery';
import {defaultHooks} from "@wordpress/hooks";
import {loadScript} from '@paypal/paypal-js';

let urlParams = {};

let isLoading = false;

const fields = new Map();

let locales = null;

export const SHIPPING_OPTION_REGEX = /^([\w]+)\:(.+)$/;

const ADDRESS_MAPPING = {
    address_1: 'address_line_1|line1',
    address_2: 'address_line_2|line2',
    state: 'admin_area_1|state',
    city: 'admin_area_2|city',
    postcode: 'postal_code',
    country: 'country_code'
};

const INTERMEDIATE_ADDRESS_MAPPING = {
    city: 'city',
    state: 'state',
    postal_code: 'postcode',
    country_code: 'country'
}

//export const hooks = createHooks();

const removeScriptById = (id) => {
    const element = document.getElementById(id);
    if (element) {
        element.remove();
    }
}

const hasPayPalScript = (id) => {
    const element = document.getElementById(id);
    return !!element;
}

export const loadPayPalSdk = (params = null) => {
    return new Promise((resolve, reject) => {

        // params may have changed so reload script¬
        if (params && !isEmpty(params) && !isEqual(params, urlParams)) {
            urlParams = params;
            if (window.paypal) {
                // cleanup
                defaultHooks.doAction('paypalInstanceCleanup', window.paypal);
            }
            isLoading = true;
            loadScript({...params}).then(paypal => {
                resolve(paypal);
                defaultHooks.doAction('paypalInstanceCreated', paypal, urlParams);
            }).catch(error => {
                console.log(error);
                const msg = error?.message?.toLowerCase() || null;
                let response;
                if (msg) {
                    if (msg.includes('locale')) {
                        const {locale, ...newParams} = params;
                        return loadPayPalSdk(newParams).then(paypal => {
                            resolve(paypal);
                        });
                    } else if (msg.includes('client-id not recognized')) {
                        response = {code: 'invalid_client_id'};
                    } else if (msg.includes('invalid query value for client-id')) {
                        response = {code: 'invalid_client_id'};
                    } else if (msg.includes('invalid query value for currency')) {
                        //response = {code: 'unsupported_currency', message: getErrorMessage({code: 'invalid_currency'}).replace('%', urlParams.currency)};
                    }
                }
                defaultHooks.doAction('paypalLoadError');
                reject(response);
            }).finally(() => {
                isLoading = false;
            })
        } else {
            if (window.paypal && !isLoading) {
                resolve(window.paypal);
            } else {
                defaultHooks.addAction('paypalInstanceCreated', 'wcPPCP', (paypal) => {
                    resolve(paypal);
                });
                defaultHooks.addAction('paypalLoadError', 'wcPPCP', () => {
                    reject();
                })
            }
        }
    })
}

export const getSetting = (key) => {
    if (typeof window.wcPPCPSettings !== 'undefined') {
        return window.wcPPCPSettings[key] || {};
    }
    return {};
}

export const setSetting = (key, value) => {
    if (typeof window.wcPPCPSettings !== 'undefined') {
        return window.wcPPCPSettings[key] = value;
    }
}

export const getPayPalQueryParams = () => getSetting('queryParams');

export const getFieldValue = (key, prefix = 'billing') => {
    if (key.substring(0, 'shipping'.length) != 'shipping' && key.substring(0, 'billing'.length) != 'billing') {
        key = `${prefix}_${key}`;
    }
    if ($(`[name="${key}"]`).length) {
        return $(`[name="${key}"]`).val();
    }
    return fields.get(key);
}

export const fieldsToJson = () => {
    const json = {};
    fields.forEach((value, key) => {
        json[key] = value;
    });
    return json;
}

export const setFieldValue = (key, value, prefix = 'billing') => {
    if (!!prefix && key.substring(0, 'shipping'.length) != 'shipping' && key.substring(0, 'billing'.length) != 'billing') {
        key = `${prefix}_${key}`;
    }
    fields.set(key, value);
    if ($(`[name="${key}"]`).length) {
        $(`[name="${key}"]`).val(value);
    }
}

export const getErrorMessage = (error) => {
    const messages = getSetting('errorMessages');
    if (typeof error == 'string') {
        return error;
    }
    if (error?.code && messages?.[error.code]) {
        return messages[error.code];
    }
    if (error?.message) {
        return error.message;
    }
}

export const submitErrorMessage = (error, container, context = 'checkout') => {
    if (error?.message?.toLowerCase()?.match(/detected popup close|window is closed/)) {
        return;
    }
    let msg = getErrorMessage(error);
    let classes = 'woocommerce-NoticeGroup';
    const $container = $(container);
    if (context == 'checkout') {
        classes += ' woocommerce-NoticeGroup-checkout';
    }
    if (Array.isArray(error)) {
        msg = '<div class="' + classes + '"><ul class="woocommerce-error"><li>' + error.join('</li><li>') + '</li></ul></div>';
    } else if (typeof error === 'string' && /<[^>]*>/.test(error)) {
        msg = '<div class="' + classes + '">' + error + '</div>';
    } else {
        msg = '<div class="' + classes + '"><ul class="woocommerce-error"><li>' + msg + '</li></ul></div>';
    }
    $('.woocommerce-NoticeGroup-checkout, .woocommerce-error, .woocommerce-message').remove();
    $container.prepend(msg);
    if ($.scroll_to_notices) {
        $.scroll_to_notices($container);
    } else {
        $('html, body').animate({
            scrollTop: $container.offset().top - 100
        }, 1000);
    }
}

export const isValidAddress = (address, exclude = []) => {
    let i18n_params = typeof wc_address_i18n_params == 'undefined' ? getSetting('i18n') : wc_address_i18n_params;
    if (isEmpty(address)) {
        return false;
    }
    if (!locales) {
        locales = JSON.parse(i18n_params.locale.replace(/&quot;/g, '"'));
    }
    if (!address.country || isEmpty(address)) {
        return false;
    }
    let locale = locales?.[address.country] ? locales[address.country] : locales['default'];
    locale = $.extend(true, {}, locales['default'], locale);
    const entries = Object.entries(locale).filter(([key, value]) => !exclude.includes(key));
    locale = Object.fromEntries(entries);
    for (let key in locale) {
        if (locale[key]?.required) {
            const value = address?.[key] || null;
            if (!value || !value?.trim()) {
                return false;
            }
        }
    }
    return true;
}

/**
 * Converts a WC cart address to a PayPal formatted address
 */
export const convertCartAddressToPayPal = (address) => {
    const newAddress = {};
    for (let key in address) {
        if (ADDRESS_MAPPING?.[key]) {
            if (ADDRESS_MAPPING[key].includes('|')) {
                const [k1, k2] = ADDRESS_MAPPING[key].split('|');
                newAddress[k1] = address[key];
            } else {
                newAddress[ADDRESS_MAPPING[key]] = address[key];
            }
        }
    }
    return newAddress;
}

export const convertPayPalAddressToCart = (address, intermediate = false) => {
    let mappings = {};
    if (intermediate) {
        mappings = INTERMEDIATE_ADDRESS_MAPPING;
    } else {
        mappings = Object.fromEntries(Object.entries(ADDRESS_MAPPING).map(([key, key2]) => [key2, key]));
    }
    const newAddress = {};
    for (let key in mappings) {
        if (key.includes('|')) {
            const keys = key.split('|');
            for (let k1 of keys) {
                if (address.hasOwnProperty(k1)) {
                    newAddress[mappings[key]] = address[k1];
                    break;
                } else {
                    newAddress[mappings[key]] = '';
                }
            }
        } else {
            if (address.hasOwnProperty(key)) {
                newAddress[mappings[key]] = address[key];
            } else {
                newAddress[mappings[key]] = '';
            }
        }
    }
    return newAddress;
}

export const isValidFieldValue = (value) => {
    value = value?.trim();
    return !!value;
}

export const isValid = key => {
    return isValidFieldValue(getFieldValue(key));
}

/**
 * Given a formatted shipping method, extract it into the WC format.
 * @param selectedMethod
 */
export const extractShippingMethod = (selectedMethod) => {
    const matches = selectedMethod.match(SHIPPING_OPTION_REGEX);
    if (matches) {
        const {1: packageId, 2: method} = matches;
        return {[packageId]: method};
    }
    return null;
}

export const extractFullName = (name) => {
    name = name.trim();
    const firstName = name.split(' ').slice(0, -1).join(' ');
    const lastName = name.split(' ').pop();
    return [firstName, lastName];
}

/**
 * Returns a rest route in ajax form given a route key
 * @param route
 * @returns {*|null}
 */
export const getRestRoute = (route) => {
    return getSetting('generalData')?.restRoutes?.[route]?.url || null;
}

/**
 * Returns a rest route in ajax form given a route path.
 * @param path
 * @returns {*}
 */
export const getRestPath = (path) => {
    path = path.replace(/^\//, '');
    return getSetting('generalData')?.ajaxRestPath?.replace('%s', path);
}

export const getPage = () => {
    return getSetting('generalData').page;
}

export const isPluginConnected = () => getSetting('generalData')?.clientId?.length > 0;