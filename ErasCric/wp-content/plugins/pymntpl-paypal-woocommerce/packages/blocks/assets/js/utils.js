import {SHIPPING_OPTION_REGEX} from '@ppcp/utils';
import {getSetting} from '@woocommerce/settings';

export const getShippingOptionId = (packageId, rateId) => `${packageId}:${rateId}`;

export const getSelectedShippingOptionId = (shippingRates) => {
    let shippingOption = '';
    shippingRates.forEach((shippingPackage, idx) => {
        shippingPackage.shipping_rates.forEach(rate => {
            if (rate.selected) {
                shippingOption = getShippingOptionId(idx, rate.rate_id);
            }
        });
    });
    return shippingOption;
}

export const extractShippingRateParams = (id) => {
    const result = id.match(SHIPPING_OPTION_REGEX);
    if (result) {
        const {1: packageIdx, 2: rate} = result;
        return [rate, packageIdx];
    }
    return [];
}

export const removeNumberPrecision = (value, unit) => {
    return value / 10 ** unit;
}

export const hasShippingOptions = (shippingRates) => shippingRates.map(rate => rate?.shipping_rates.length > 0).filter(Boolean).length > 0;

/**
 * Returns a rest route in ajax form given a route path.
 * @param path
 * @returns {*}
 */
export const getRestPath = (path) => {
    path = path.replace(/^\//, '');
    return getSetting('ppcpGeneralData')?.ajaxRestPath?.replace('%s', path);
}

export const isUserAdmin = () => getSetting('ppcpGeneralData')?.isAdmin

const getLocaleFields = (country) => {
    const countryLocale = getSetting('countryLocale', {});
    let localeFields = {...countryLocale.default};
    if (country && countryLocale.hasOwnProperty(country)) {
        localeFields = Object.entries(countryLocale[country]).reduce((locale, [key, value]) => {
            locale[key] = {...locale[key], ...value}
            return locale;
        }, localeFields);
    }
    return localeFields;
}

/**
 * Returns true if the provided value is empty.
 * @param value
 * @returns {boolean}
 */
const isEmpty = (value) => {
    if (typeof value === 'string') {
        return value.length == 0 || value == '';
    }
    if (Array.isArray(value)) {
        return array.length == 0;
    }
    if (typeof value === 'object') {
        return Object.keys(value).length == 0;
    }
    if (typeof value === 'undefined') {
        return true;
    }
    return true;
}

export const isCartPage = () => {
    return getSetting('ppcpGeneralData').context === 'cart';
}

export const isCheckoutPage = () => {
    return getSetting('ppcpGeneralData').context === 'checkout';
}

export const DEFAULT_SHIPPING_ADDRESS = {
    'first_name': '',
    'last_name': '',
    'company': '',
    'address_1': '',
    'address_2': '',
    'city': '',
    'state': '',
    'postcode': '',
    'country': '',
    'phone': '',
}

export const DEFAULT_BILLING_ADDRESS = {
    ...DEFAULT_SHIPPING_ADDRESS,
    'email': ''
}

export const i18n = getSetting('ppcpGeneralData').i18n;