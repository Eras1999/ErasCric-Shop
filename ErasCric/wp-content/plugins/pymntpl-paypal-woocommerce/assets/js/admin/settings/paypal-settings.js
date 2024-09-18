import BaseSettings from "./base-settings";
import {registerSettings} from "./register";
import $ from 'jquery';
import paypal from '@paypal/smartbutton';

class PaypalSettings extends BaseSettings {

    constructor(props) {
        super(props);
        this.buttons = {};
        this.sources = [paypal.FUNDING.PAYPAL, paypal.FUNDING.PAYLATER, paypal.FUNDING.CARD];
        $('.wc-ppcp-smartbutton-option').on('change', this.handleButtonOptionChange.bind(this));
        $('.ppcp-smartbutton').on('click', this.handleSmartbuttonClick.bind(this));

        this.initialize_button();
        this.initialize_slider();
    }

    handleSmartbuttonClick() {

    }

    handleButtonOptionChange() {
        this.initialize_button();
    }

    initialize_button() {
        for (let source in this.buttons) {
            if (this.buttons[source]?.close) {
                this.buttons[source].close();
            }
        }
        $('#ppcp_button_demo').empty();
        for (const fundingSource of this.getSources()) {
            const button = paypal.Buttons(this.getButtonOptions(fundingSource));
            if (button.isEligible()) {
                this.buttons[fundingSource] = button;
                $('#ppcp_button_demo').append(`<li class="wc-ppcp-button__demo paypal-${fundingSource}" data-funding="${fundingSource}"></li>`)
                this.buttons[fundingSource].render($('#ppcp_button_demo').find(`.paypal-${fundingSource}`)[0]);
            }
        }

        this.initSortable();
    }

    initSortable() {
        $('#ppcp_button_demo')?.sortable({
            items: 'li',
            axis: 'y',
            stop: (event, ui) => {
                $('[name^="woocommerce_ppcp_buttons_order"]').remove();
                $('#ppcp_button_demo').find('li').each((idx, e) => {
                    const funding = $(e).data('funding');
                    $('#mainform').append(`<input type="hidden" name="woocommerce_ppcp_buttons_order[]" value="${funding}"/>`);
                });
            }
        });
    }

    initialize_slider() {
        const $el = $('.wc-ppcp-slider');
        const options = {
            min: $el.data('height-min'),
            max: $el.data('height-max'),
            step: $el.data('height-step'),
            value: this.getFieldValue('button_height')
        };
        $el.slider(options);
        $el.on('slidechange', (e, ui) => {
            this.setFieldValue('button_height', ui.value);
            $el.closest('td').find('.wc-ppcp-slider-val').text(`${ui.value}px`);
            this.initialize_button();
        }).on('slide', (e, ui) => $el.closest('td').find('.wc-ppcp-slider-val').text(`${ui.value}px`));
    }

    getButtonOptions(fundingSource) {
        return {
            fundingSource,
            style: this.getButtonStyleOptions(fundingSource)
        }
    }

    getButtonStyleOptions(fundingSource) {
        const style = {
            layout: 'vertical',
            shape: this.getFieldValue('button_shape'),
            height: parseInt(this.getFieldValue('button_height'))
        }
        switch (fundingSource) {
            case paypal.FUNDING.PAYPAL:
                style.color = this.getFieldValue('paypal_button_color');
                style.label = this.getFieldValue('button_label');
                break;
            case paypal.FUNDING.PAYLATER:
            case paypal.FUNDING.CREDIT:
                style.color = this.getFieldValue('paylater_button_color');
                break;
            case paypal.FUNDING.CARD:
                style.tagline = this.getFieldValue('card_tagline_enabled');
                style.layout = 'horizontal';
                style.color = this.getFieldValue('card_button_color');
                if (style.tagline) {
                    style.layout = 'vertical';
                    delete style.tagline;
                }
                break;

        }
        return style;
    }

    getSources() {
        const sources = [paypal.FUNDING.PAYPAL];
        if (this.getFieldValue('paylater_enabled')) {
            sources.push(paypal.FUNDING.PAYLATER);
        }
        if (this.getFieldValue('venmo_enabled')) {
            sources.push(paypal.FUNDING.VENMO);
        }
        if (this.getFieldValue('card_enabled')) {
            sources.push(paypal.FUNDING.CARD);
        }
        const order = this.getSortOrder();
        sources.sort((a, b) => {
            const indexA = order.indexOf(a);
            const indexB = order.indexOf(b);
            return indexA < indexB ? -1 : 1;
        });
        return sources;
    }

    getSortOrder() {
        const elements = document.querySelectorAll('[name="woocommerce_ppcp_buttons_order[]"]');
        return [...elements].map(e => e.value);
    }

}

registerSettings('ppcp', PaypalSettings);
