import {useState, useEffect, useCallback} from '@wordpress/element';
import {PaymentMethodCard} from "../../components";
import {i18n} from "../../utils";
import {useProcessPaymentFailure} from "../../hooks";

const SimplePayPal = (props) => {
    return (
        <PayPalRedirectNotice {...props}/>
    )
}

const PayPalRedirectNotice = (
    {
        data,
        eventRegistration,
        activePaymentMethod,
        emitResponse,
        ...props
    }) => {
    const {onPaymentSetup, onCheckoutFail} = eventRegistration;
    const {responseTypes, noticeContexts} = emitResponse;
    const [paymentData, setPaymentData] = useState(data('paymentData'));

    const clearPaymentData = useCallback(() => {
        setPaymentData(null);
    }, []);

    useProcessPaymentFailure({
        event: onCheckoutFail,
        responseTypes,
        messageContext: noticeContexts.CHECKOUT,
        setPaymentData
    });

    useEffect(() => {
        if (activePaymentMethod === 'ppcp' && paymentData) {
            const unsubscribe = onPaymentSetup(() => {
                const {orderId = '', billingToken = ''} = paymentData;
                const response = {
                    meta: {
                        paymentMethodData: {
                            ppcp_paypal_order_id: orderId,
                            ppcp_billing_token: billingToken
                        }
                    }
                }
                return {
                    type: responseTypes.SUCCESS,
                    ...response
                };
            });
            return unsubscribe;
        }
    }, [
        paymentData,
        responseTypes,
        onPaymentSetup,
        activePaymentMethod
    ]);

    if (paymentData && paymentData?.order) {
        if (paymentData.billingTokenData) {
            return (
                <PaymentMethodCard
                    description={paymentData.billingTokenData.payer_info.email}
                    icon={data('icons').find(icon => icon.id === 'paypal_simple')}
                    label={i18n.cancel}
                    onCancel={clearPaymentData}/>
            );
        }
        return (
            <PaymentMethodCard
                description={paymentData.order.payer.email_address}
                icon={data('icons').find(icon => icon.id === 'paypal_simple')}
                label={i18n.cancel}
                onCancel={clearPaymentData}/>
        )
    }

    return (
        <div className="wc-ppcp-popup__container">
            <img src={data('redirectIcon')}/>
            <p dangerouslySetInnerHTML={{__html: data('i18n').redirectText}}/>
        </div>
    )
}

export default SimplePayPal;