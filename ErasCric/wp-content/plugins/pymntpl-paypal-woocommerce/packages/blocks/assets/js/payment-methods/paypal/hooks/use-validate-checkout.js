import {useEffect} from '@wordpress/element';
import {i18n} from '../../../utils';

export const useValidateCheckout = (
    {
        isExpress,
        paymentData,
        onCheckoutValidation
    }) => {
    useEffect(() => {
        if (!isExpress) {
            const unsubscribe = onCheckoutValidation(() => {
                // validate that the order has been created.
                if (!paymentData?.orderId) {
                    return {
                        errorMessage: i18n.order_button_click
                    }
                }
                return true;
            });
            return unsubscribe;
        }
    }, [isExpress, paymentData]);
}