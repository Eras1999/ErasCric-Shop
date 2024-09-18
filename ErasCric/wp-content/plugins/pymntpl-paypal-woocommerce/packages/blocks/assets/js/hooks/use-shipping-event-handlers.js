import {useEffect, useCallback, useRef} from '@wordpress/element';
import {hasShippingOptions} from '../utils';

export const useShippingEventHandlers = (
    {
        billing,
        shippingData,
        eventRegistration
    }) => {
    const {onShippingRateSuccess, onShippingRateFail, onShippingRateSelectSuccess} = eventRegistration;
    const currentShippingData = useRef(shippingData);
    const currentBillingData = useRef(billing);
    const callbacks = useRef([]);

    const onShippingChangeEventHandler = useCallback(callback => {
        callbacks.current.push(callback);
    });

    useEffect(() => {
        currentShippingData.current = shippingData;
        currentBillingData.current = billing;
    });

    const handleShippingChange = useCallback(() => {
        const {isSelectingRate, shippingRatesLoading, shippingRates} = currentShippingData.current;
        if (!isSelectingRate && !shippingRatesLoading) {
            while (callbacks.current.length) {
                const callback = callbacks.current.pop();
                callback({
                    success: hasShippingOptions(shippingRates),
                    data: {
                        billing: currentBillingData.current,
                        shippingData: currentShippingData.current
                    }
                });
            }
        }
    }, []);

    const handleShippingFail = useCallback(() => {
        while (callbacks.current.length) {
            const callback = callbacks.current.pop();
            callback({
                success: false,
                data: {
                    billing: currentBillingData.current,
                    shippingData: currentShippingData.current
                }
            });
        }
    }, []);

    useEffect(() => {
        const unsubscribeShippingRateSuccess = onShippingRateSuccess(handleShippingChange);
        const unsubscribeShippingRateSelectSuccess = onShippingRateSelectSuccess(handleShippingChange);
        const unsubscribeShippingRateFail = onShippingRateFail(handleShippingFail);

        return () => {
            unsubscribeShippingRateSuccess();
            unsubscribeShippingRateSelectSuccess();
            unsubscribeShippingRateFail();
        }
    }, [
        onShippingRateSuccess,
        onShippingRateFail,
        onShippingRateSelectSuccess,
        handleShippingChange,
        handleShippingFail
    ]);

    return {onShippingChangeEventHandler};
}