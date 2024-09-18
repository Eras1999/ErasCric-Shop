import $ from 'jquery';
import {getPaymentMethod, getSetting, loadPayPalSdk} from "@ppcp/utils";
import {defaultHooks} from "@wordpress/hooks";

class PayLaterBaseMessage {

    constructor(context, {queryParams}) {
        this.context = context;
        this.queryParams = queryParams;
        defaultHooks.addAction('paypalInstanceCreated', 'wcPPCP', this.paypalInstanceCreated.bind(this));
    }

    paypalInstanceCreated(paypal, queryParams) {
        if (this.paypal !== paypal) {
            this.paypal = paypal;
            this.queryParams = queryParams;
            this.createMessage();
        }
    }

    isCheckoutFlow() {
        return this.queryParams?.vault !== 'true';
    }

    initialize() {
        this.createMessage();
    }

    createMessage() {
        const container = this.getMessageContainer();
        if (container) {
            if (this.isCheckoutFlow() && this.isActive()) {
                this.showContainer(container);
                const params = {
                    amount: this.getTotal(),
                    currency: this.getCurrency(),
                    placement: this.getPlacement(),
                    style: this.getStyleOptions(this.context),
                    onRender: () => {
                        this.onRender(container);
                    }
                };
                this.paypal.Messages(params).render(container);
            } else {
                this.hideMessage(container);
            }
        }
    }

    showContainer(container) {
        $(container).closest('.wc-ppcp-paylater-msg__container').show();
    }

    hideMessage(container) {
        $(container).closest('.wc-ppcp-paylater-msg__container').hide();
    }

    getMessageContainer() {

    }

    getStyleOptions(context) {
        return getSetting('payLaterMessage')?.[context]?.style;
    }

    getSetting(key) {
        return getSetting('payLaterMessage')?.[key];
    }

    getTotal() {
        return 0;
    }

    getPlacement() {
        return '';
    }

    getCurrency() {
        return this.queryParams?.currency;
    }

    isActive() {
        return getSetting('payLaterMessage').enabled;
    }

    onRender(container) {
        const show = () => {
            const iframe = container.querySelector('[name^="__zoid__paypal_message"]');
            if (iframe) {
                iframe.style.width = '100%';
                iframe.style.opacity = '1';
            }
        }
        show();
        setTimeout(show, 1000);
    }
}

export default PayLaterBaseMessage;