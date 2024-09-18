import $ from 'jquery';

export const OrderMetaBoxModal = $.WCBackboneModal.View.extend({
    events: {
        ...$.WCBackboneModal.View.prototype.events, ...{
            'click .ppcp-capture': 'handleCapture',
            'click .ppcp-void': 'handleVoid',
            'click .wc-ppcp-nav-item': 'toggleSection',
            'change .shipping-carrier': 'onShippingCarrierChange',
            'click .ppcp-shipping-submit': 'onShippingSubmit'
        }
    },
    initialize(data) {
        this.props = data.props;
        $.WCBackboneModal.View.prototype.initialize.call(this, data);
        this.captureButton = this.$el.find('.ppcp-capture');
        this.voidButton = this.$el.find('.ppcp-void');
        this.$el.find('.wc-enhanced-select').selectWoo();
    },
    disableButtons() {
        this.captureButton.prop('disabled', true);
        this.voidButton.prop('disabled', true);
    },
    enableButtons() {
        this.captureButton.prop('disabled', false);
        this.voidButton.prop('disabled', false);
    },
    setProcessingText($button) {
        $button.data('previous-text', $button.text());
        $button.text($button.data('processing-text'));
    },
    resetButtonText($button) {
        $button.text($button.data('previous-text'));
    },
    handleCapture(e) {
        const $button = $(e.currentTarget);
        const amount = this.$el.find('[name="ppcp_capture_amount"]').val();
        this.setProcessingText($button);
        this.disableButtons();
        this.props.handleCapture(amount, this.props.order_id).then((response) => {
            if (response.code) {
                this.enableButtons();
                this.props.hideLoader();
                this.submitError(response.message);
            } else {
                window.location.reload();
            }
        }).catch(error => {
            this.props.hideLoader();
            this.submitError(error?.message);
        }).finally(() => {
            this.resetButtonText($button);
        });
    },
    handleVoid(e) {
        const $button = $(e.currentTarget);
        this.setProcessingText($button);
        this.disableButtons();
        this.props.handleVoid(this.props.order_id).then((response) => {
            this.props.hideLoader();
            if (response.code) {
                this.enableButtons();
                this.submitError(response.message);
            } else {
                window.location.reload();
            }
        }).catch(error => {
            this.props.hideLoader();
            this.submitError(error?.message);
        }).finally(() => {
            this.resetButtonText($button);
        });
    },
    submitMessage(msg, type) {
        this.$el.find('.ppcp-order-actions-notices').empty();
        this.$el.find('.ppcp-order-actions-notices').prepend(`<div class="ppcp-order-actions-notice ${type}">${msg}</div>`);
        setTimeout(() => {
            this.$el.find('.ppcp-order-actions-notices').empty();
        }, 4000);
    },
    submitError(msg) {
        this.submitMessage(msg, 'error');
    },
    submitSuccess(msg) {
        this.submitMessage(msg, 'success');
    },
    toggleSection(e) {
        const section = $(e.currentTarget).data('section');
        this.$el.find('.wc-ppcp-nav-item').removeClass('selected');
        $(e.currentTarget).addClass('selected');
        this.$el.find('.wc-ppcp-actions__actions').each((idx, el) => {
            if ($(el).data('section') === section) {
                $(el).show();
            } else {
                $(el).hide();
            }
        });
        this.$el.find('footer [data-section]').hide();
        this.$el.find(`footer [data-section="${section}"]`).show();
    },
    onShippingCarrierChange(e) {
        const carrier = $('#ppcp_carrier').val();
        if (carrier === this.$el.find('.carrier-other').data('show-if')) {
            this.$el.find('.carrier-other').show();
        } else {
            this.$el.find('.carrier-other').hide();
        }
    },
    onShippingSubmit(e) {
        // get all the values and submit to the server
        const data = {
            tracking: this.$el.find('#ppcp_tracking').val(),
            tracking_type: this.$el.find('#ppcp_tracking_type').val(),
            shipping_status: this.$el.find('#ppcp_shipping_status').val(),
            carrier: this.$el.find('#ppcp_carrier').val(),
            carrier_other: this.$el.find('#ppcp_carrier_other').val(),
            notify_buyer: this.$el.find('#ppcp_notify_buyer').is(':checked')
        }
        const $button = $(e.currentTarget);
        const text = $button.text();
        $button.prop('disabled', true).text($button.data('processing-text'));
        $(e.currentTarget).prop('disabled', true);
        this.props.submitShipping(data).then(response => {
            if (response.code) {
                this.submitError(response.message);
            } else {
                this.submitSuccess(response.message);
            }
        }).catch(error => {
            if (error?.data?.params) {
                const key = Object.keys(error.data.params)[0];
                return this.submitError(error.data.params[key]);
            }
            this.submitError(error?.message);
        }).finally(() => {
            $button.prop('disabled', false).text(text);
        })

    }
});

export default OrderMetaBoxModal;