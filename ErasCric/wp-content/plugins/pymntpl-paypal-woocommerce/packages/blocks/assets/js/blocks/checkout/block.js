import {useState, useEffect, useCallback} from "@wordpress/element";
import {useSelect, dispatch} from '@wordpress/data';
import {PAYMENT_STORE_KEY} from '@woocommerce/block-data';
import {getSetting} from '@woocommerce/settings';

export const Block = ({checkoutExtensionData, extensions}) => {
    const {createErrorNotice} = dispatch('core/notices');
    const {
        __internalSetActivePaymentMethod: setActivePaymentMethod
    } = dispatch(PAYMENT_STORE_KEY);

    const data = getSetting('ppcp_data');

    useEffect(() => {
        if (setActivePaymentMethod && data.errorMessage) {
            createErrorNotice(data.errorMessage, {
                context: 'wc/checkout'
            });
            setActivePaymentMethod('ppcp');
        }
    }, []);

    return null;
}