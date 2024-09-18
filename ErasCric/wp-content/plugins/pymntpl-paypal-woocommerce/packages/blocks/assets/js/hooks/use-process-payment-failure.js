import {useEffect} from '@wordpress/element';

export const useProcessPaymentFailure = ({event, responseTypes, messageContext = null, setPaymentData}) => {
    useEffect(() => {
        const unsubscribe = event((data) => {
            if (data?.processingResponse?.paymentDetails?.ppcpErrorMessage) {
                setPaymentData(null);
                const message = data.processingResponse.paymentDetails.ppcpErrorMessage;
                return {
                    type: responseTypes.ERROR,
                    retry: true,
                    message,
                    messageContext
                }
            }
            return null;
        });
        return () => {
            unsubscribe();
        };
    }, [event]);
}

export default useProcessPaymentFailure;