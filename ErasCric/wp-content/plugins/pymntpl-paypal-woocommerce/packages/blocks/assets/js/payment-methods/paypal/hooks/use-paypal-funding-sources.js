import {useEffect, useState} from '@wordpress/element';

export const usePayPalFundingSources = ({data, paypal, context, vault = false}) => {
    const [sources, setSources] = useState(false);
    useEffect(() => {
        if (paypal) {
            let sources = [];
            const buttonOrder = data('buttonOrder', []);
            if (context === 'express_checkout') {
                if (data('paypalSections', []).includes(context)) {
                    sources.push(paypal.FUNDING.PAYPAL);
                }
            } else {
                sources.push(paypal.FUNDING.PAYPAL);
            }
            if (data('payLaterEnabled') && data('payLaterSections', []).includes(context)) {
                if (vault) {
                    sources.push(paypal.FUNDING.CREDIT)
                } else {
                    sources.push(paypal.FUNDING.PAYLATER)
                }
            }
            if (data('cardEnabled') && data('creditCardSections', []).includes(context)) {
                sources.push(paypal.FUNDING.CARD)
            }
            if (data('venmoEnabled') && data('venmoSections', []).includes(context)) {
                sources.push(paypal.FUNDING.VENMO);
            }
            sources.sort((a, b) => {
                return buttonOrder.indexOf(a) < buttonOrder.indexOf(b) ? -1 : 1;
            });
            setSources([...sources]);
        }
    }, [paypal]);

    return sources;
}