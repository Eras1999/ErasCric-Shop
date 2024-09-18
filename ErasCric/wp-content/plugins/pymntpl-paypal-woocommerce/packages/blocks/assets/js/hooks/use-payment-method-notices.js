import {useState, useCallback} from '@wordpress/element';

export const usePaymentMethodNotices = () => {
    const [notices, setNotices] = useState([]);

    const addNotice = useCallback((message, options, type) => {
        setNotices(prevNotices => {
            const {isDismissable = true} = options;
            const notice = {
                message,
                options,
                type
            }
            return [...prevNotices, notice];
        });
    }, []);

    const addErrorNotice = useCallback((message, options = {}) => {
        addNotice(message, options, 'error');
    }, [addNotice]);

    const addSuccessNotice = useCallback((message, options = {}) => {
        addNotice(message, options, 'success');
    }, [addNotice]);

    const addInfoNotice = useCallback((message, options = {}) => {
        addNotice(message, options, 'info');
    }, [addNotice]);

    return {
        addErrorNotice,
        addSuccessNotice,
        addInfoNotice
    }
}