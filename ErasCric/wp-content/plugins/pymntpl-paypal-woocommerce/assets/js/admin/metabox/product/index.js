import $ from 'jquery';

const initialize = () => {
    $(document.body).on('change', '#ppcp_product_data input, #ppcp_product_data select', handleOptionChange);
}

const handleOptionChange = (e) => {
    const $parent = $(e.currentTarget).closest('.wc-ppcp-options-panel');
    const payment_method = $parent.data('payment-method');
    if (payment_method) {
        $(`[name="wc_${payment_method}_options_change"]`).val('yes');
    }
}

initialize();