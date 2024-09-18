import {useEffect, useRef, useMemo} from '@wordpress/element';
import {ExperimentalOrderMeta, TotalsWrapper} from '@woocommerce/blocks-checkout';
import {registerPlugin} from '@wordpress/plugins';
import {getSetting} from '@woocommerce/settings';
import {useLoadPayPalScript} from "../hooks";

const data = getSetting('paylaterParams');

const PayLaterMessaging = ({cart, extensions, context}) => {
    const isEnabled = data.enabled;
    const el = useRef(null);
    const {cartTotals} = cart;
    const {currency_code: currency, total_price} = cartTotals;
    const paypal = useLoadPayPalScript(getSetting('paypalQueryParams'));

    const options = useMemo(() => {
        return {
            amount: total_price / (10 ** cartTotals.currency_minor_unit),
            currency,
            placement: 'payment',
            ...data.options
        }
    });

    useEffect(() => {
        if (paypal) {
            paypal.Messages(options).render(el.current);
        }
    }, [paypal, options]);
    if (isEnabled) {
        return (
            <TotalsWrapper>
                <div className='wc-block-components-totals-item'>
                    <div ref={el} className='wc-ppcp-paylater-msg__container'/>
                </div>
            </TotalsWrapper>
        )
    }
    return null;
}

const render = () => {
    return (
        <ExperimentalOrderMeta>
            <PayLaterMessaging/>
        </ExperimentalOrderMeta>
    )
}

registerPlugin('wc-ppcp', {render, scope: 'woocommerce-checkout'});
