import $ from 'jquery';
import apiFetch from '@wordpress/api-fetch';
import OrderMetaBoxModal from "../order-modal";

const spinner = `<div class="wc-ppcp-loader">
            <div></div>
            <div></div>
            <div></div>
        </div>`;

class OrderMetaBox {

    constructor() {
        this.initialize();
    }

    initialize() {
        $(document.body).on('click', '.wc-ppcp-order-actions.button', this.handleClickActions.bind(this));
    }

    handleClickActions(e) {
        e.preventDefault();
        // fetch order and payment data
        let order_id = $(e.currentTarget).data('order');
        const $el = $(e.currentTarget);
        const data = $el.data('response');
        if (data) {
            this.renderTemplate(data, order_id);
        } else {
            $el.prop('disabled', true);
            this.displayLoader($(e.currentTarget));
            apiFetch({
                method: 'GET',
                path: `/wc-ppcp/v1/admin/order/${order_id}`
            }).then(response => {
                this.hideLoader();
                $el.prop('disabled', false);
                if (response.code) {
                    return alert(response.message);
                }
                $el.data('response', response);
                // render the template
                this.renderTemplate(response, order_id);
            }).catch(error => {
                this.hideLoader();
                window.alert(error?.message);
            })
        }
    }

    renderTemplate(data, order_id) {
        new OrderMetaBoxModal({
            target: 'wc-ppcp-order-actions',
            string: data,
            props: {
                order_id,
                displayLoader: this.displayLoader,
                hideLoader: this.hideLoader,
                handleCapture: (amount, order_id) => {
                    return apiFetch({
                        method: 'POST',
                        path: `/wc-ppcp/v1/admin/order/${order_id}`,
                        data: {amount}
                    }).then(response => {
                        return response;
                    }).catch(error => {
                        return error;
                    });
                },
                handleVoid: (order_id) => {
                    return apiFetch({
                        method: 'DELETE',
                        path: `/wc-ppcp/v1/admin/order/${order_id}`
                    }).then(response => {
                        return response;
                    }).catch(error => {
                        return error;
                    });
                },
                submitShipping: (data) => {
                    return apiFetch({
                        method: 'POST',
                        path: `/wc-ppcp/v1/admin/order/${order_id}/tracking`,
                        data
                    })
                }
            }
        });
    }

    displayLoader($el) {
        $el.append(spinner);
    }

    hideLoader() {
        $('.wc-ppcp-loader').remove();
    }
}

new OrderMetaBox();