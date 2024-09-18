import {useEffect, useState} from "@wordpress/element";
import {loadPayPalSdk} from "@ppcp/utils";

/*
'client-id': this.getClientId(),
'components': this.settings?.components,
'currency': this.getOption('currency'),
'intent': this.getOption('intent'),
'vault': this.getOption('vault')
 */

/**
 *
 * @param params - client-id, components, currency, intent, vault
 * @returns {*}
 */
export const useLoadPayPalScript = (params) => {
    const [paypal, setPayPal] = useState(null);

    useEffect(() => {
        if (!paypal) {
            loadPayPalSdk(params).then(paypal => setPayPal(paypal));
        }
    }, [paypal]);

    return paypal;
}