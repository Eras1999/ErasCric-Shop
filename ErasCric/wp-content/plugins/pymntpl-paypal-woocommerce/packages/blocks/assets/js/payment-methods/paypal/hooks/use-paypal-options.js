import {useEffect, useCallback, useRef} from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import {convertPayPalAddressToCart, extractShippingMethod} from "@ppcp/utils";
import {
    extractShippingRateParams,
    getRestPath,
    i18n
} from '../../../utils';

export const usePayPalOptions = (
    {
        isExpress,
        paypal,
        vault,
        buttonStyles,
        shippingData,
        billing,
        setError,
        setPaymentData,
        onClick,
        onClose
    }) => {
    const currentShippingData = useRef(shippingData);
    const currentBilling = useRef(billing);
    const currentData = useRef({onClick, onClose, buttonState: true, actions: {}, error: null});
    useEffect(() => {
        currentShippingData.current = shippingData;
        currentBilling.current = billing;
        currentData.current = {...currentData.current, onClick, onClose};
    });

    const disableButtons = useCallback(() => {
        Object.keys(currentData.current.actions).forEach(key => {
            currentData.current.actions[key].disable();
            currentData.current.buttonState = false;
        });
    }, []);

    const getOptions = useCallback(fundingSource => {
        const {needsShipping, shippingAddress} = currentShippingData.current;
        const billingAddress = billing.billingData;
        const options = {
            fundingSource: fundingSource,
            style: getButtonStyle(fundingSource),
            onApprove,
            onError
        };
        if (isExpress) {
            options.onClick = () => currentData.current.onClick();
            options.onCancel = () => currentData.current.onClose()
        } else {
            options.onClick = () => {
                // if address is not valid, show a notification that address data must be filled out first
                if (!isExpress && !currentData.current.buttonState) {
                    if (needsShipping) {
                        setError(i18n.order_missing_address);
                    } else {
                        setError(i18n.order_missing_billing_address);
                    }
                }
            }
        }
        options.onInit = (data, actions) => {
            if (!isExpress) {
                currentData.current.actions[fundingSource] = actions;
            }
        }
        if (isCheckoutFlow()) {
            options.createOrder = createOrder;
            if (isExpress && needsShipping && fundingSource !== 'venmo') {
                options.onShippingChange = onShippingChange;
            }
        } else {
            options.createBillingAgreement = createBillingAgreement;
        }

        return options;
    }, [
        paypal,
        isExpress,
        onApprove,
        onError,
        createOrder,
        createBillingAgreement,
        onShippingChange,
        setError
    ]);

    const getButtonStyle = useCallback(fundingSource => {
        let styles = {};
        switch (fundingSource) {
            case paypal.FUNDING.PAYPAL:
                styles = buttonStyles.paypal;
                break;
            case paypal.FUNDING.PAYLATER:
                styles = buttonStyles.paylater;
                break;
            case paypal.FUNDING.CREDIT:
                const colors = ['black', 'white'];
                const color = colors.includes(buttonStyles.paylater.color) ? buttonStyles.paylater.color : 'darkblue';
                styles = {...buttonStyles.paylater, color};
                break;
            case paypal.FUNDING.CARD:
                styles = buttonStyles.card;
                break;
            case paypal.FUNDING.VENMO:
                styles = buttonStyles.venmo;
                break;
        }
        return styles;
    }, [paypal, buttonStyles]);

    const isCheckoutFlow = useCallback(() => !vault, [vault]);

    const handleBillingToken = useCallback(async (billingToken) => {
        try {
            return apiFetch({
                method: 'GET',
                path: `/wc-ppcp/v1/billing-agreement/token/${billingToken}`
            });
        } catch (error) {
            throw error;
        }
    }, []);

    const onApprove = useCallback(async (data, actions) => {
        const paymentData = {
            order: {},
            orderId: data.orderID,
            billingToken: data.billingToken || '',
            billingTokenData: null
        }
        if (data.billingToken) {
            try {
                paymentData.billingTokenData = await handleBillingToken(data.billingToken);
                setPaymentData(paymentData);
            } catch (error) {
                setError(error);
            }
        } else {
            actions.order.get().then(response => {
                setPaymentData({...paymentData, order: response});
            }).catch(error => {
                setError(error);
            });
        }
    }, [setError, handleBillingToken]);

    const onShippingChange = useCallback((data, actions) => {
        const shippingData = currentShippingData.current;
        const {setSelectedRates} = shippingData;
        const intermediateAddress = convertPayPalAddressToCart(data?.shipping_address || {}, true);
        const selectedShippingOption = data?.selected_shipping_option?.id || '';
        return apiFetch({
            method: 'POST',
            url: getRestPath('wc-ppcp/v1/cart/shipping'),
            data: {
                order_id: data.orderID,
                address: intermediateAddress,
                shipping_method: extractShippingMethod(selectedShippingOption),
                payment_method: 'ppcp'
            }
        }).then(response => {
            if (response.code) {
                return actions.reject();
            } else {
                return actions.resolve();
            }
        }).catch(error => {
            return actions.reject();
        }).finally(() => {
            if (selectedShippingOption) {
                setSelectedRates(...extractShippingRateParams(selectedShippingOption))
            }
        })
    }, []);

    const onError = useCallback(error => {
        if (error?.message?.indexOf('Window is closed') > -1) {
            return;
        }
        if (currentData.current.error) {
            if (currentData.current.error?.code === 'validation_errors') {
                return setError(currentData.current.error.data.errors[0]);
            } else {
                return setError(currentData.current.error.message);
            }
        }
        if (error?.code === 'validation_errors') {
            setError(error.data.errors[0]);
        } else {
            setError(error);
        }
    }, [setError]);

    const createOrder = useCallback(async (data, actions) => {
        const {needsShipping, shippingAddress} = currentShippingData.current;
        const {billingAddress, email} = currentBilling.current;
        try {
            const response = await apiFetch({
                method: 'POST',
                url: getRestPath('wc-ppcp/v1/cart/order'),
                data: {
                    payment_method: 'ppcp',
                    address_provided: !isExpress && needsShipping,
                    checkout_blocks: true,
                    context: !isExpress ? 'checkout' : null,
                    ...(needsShipping ? {
                        shipping_first_name: shippingAddress.first_name,
                        shipping_last_name: shippingAddress.last_name,
                        shipping_address_1: shippingAddress.address_1,
                        shipping_address_2: shippingAddress.address_2,
                        shipping_postcode: shippingAddress.postcode,
                        shipping_city: shippingAddress.city,
                        shipping_state: shippingAddress.state,
                        shipping_country: shippingAddress.country
                    } : null),
                    ...{
                        billing_first_name: billingAddress.first_name,
                        billing_last_name: billingAddress.last_name,
                        billing_address_1: billingAddress.address_1,
                        billing_address_2: billingAddress.address_2,
                        billing_postcode: billingAddress.postcode,
                        billing_city: billingAddress.city,
                        billing_state: billingAddress.state,
                        billing_country: billingAddress.country,
                        billing_email: billingAddress.email,
                        billing_phone: billingAddress.phone,
                        billing_company: billingAddress.company
                    }
                }
            });
            return response;
        } catch (error) {
            throw error;
        }
    }, []);

    const createBillingAgreement = useCallback((data, actions) => {
        const {needsShipping, shippingAddress} = currentShippingData.current;
        const {billingAddress, email} = currentBilling.current;
        return apiFetch({
            method: 'POST',
            url: getRestPath('/wc-ppcp/v1/billing-agreement/token'),
            data: {
                context: !isExpress ? 'checkout' : null,
                payment_method: 'ppcp',
                ...(needsShipping ? {
                    shipping_first_name: shippingAddress.first_name,
                    shipping_last_name: shippingAddress.last_name,
                    shipping_address_1: shippingAddress.address_1,
                    shipping_address_2: shippingAddress.address_2,
                    shipping_postcode: shippingAddress.postcode,
                    shipping_city: shippingAddress.city,
                    shipping_state: shippingAddress.state,
                    shipping_country: shippingAddress.country
                } : null),
                ...{
                    billing_first_name: billingAddress.first_name,
                    billing_last_name: billingAddress.last_name,
                    billing_address_1: billingAddress.address_1,
                    billing_address_2: billingAddress.address_2,
                    billing_postcode: billingAddress.postcode,
                    billing_city: billingAddress.city,
                    billing_state: billingAddress.state,
                    billing_country: billingAddress.country,
                    billing_email: billingAddress.email,
                    billing_phone: billingAddress.phone,
                    billing_company: billingAddress.company
                }
            }
        }).then(token => {
            return token;
        }).catch(error => {
            currentData.current.error = error;
        });
    }, [isExpress, setError]);

    return {getOptions};
}