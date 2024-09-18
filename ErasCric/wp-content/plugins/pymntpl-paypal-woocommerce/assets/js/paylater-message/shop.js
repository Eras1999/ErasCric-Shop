import $ from 'jquery';
import {loadPayPalSdk, getPayPalQueryParams, getSetting} from "@ppcp/utils";
import PayLaterBaseMessage from "./base";


class PayLaterMessageShop extends PayLaterBaseMessage {

    constructor() {
        super('shop', {queryParams: getPayPalQueryParams()});
        this.data = getSetting('payLaterMessage');
        this.initialize();
    }

    initialize() {
        loadPayPalSdk(getPayPalQueryParams());
    }

    getValidProductTypes() {
        return this.data.shop.product_types;
    }

    isSupportedProduct(type) {
        return this.getValidProductTypes().includes(type);
    }

    createMessage() {
        for (const product of this.data.shop.products) {
            if (this.isSupportedProduct(product.product_type)) {
                this.currentProduct = product;
                super.createMessage();
            }
        }
    }

    getMessageContainer() {
        const el = document.getElementById(`wc-ppcp-paylater-msg-${this.currentProduct.id}`);
        if (!el) {
            if (this.isBelowPrice()) {
                $(`.post-${this.currentProduct.id} .price`).after(`<div class="wc-ppcp-paylater-msg-shop-container" id="wc-ppcp-paylater-msg-${this.currentProduct.id}"></div>`);
            } else {
                $(`.post-${this.currentProduct.id} a.button`).after(`<div class="wc-ppcp-paylater-msg-shop-container" id="wc-ppcp-paylater-msg-${this.currentProduct.id}"></div>`);
            }
        }
        return document.getElementById(`wc-ppcp-paylater-msg-${this.currentProduct.id}`);
    }

    getTotal() {
        return this.currentProduct.total;
    }

    getPlacement() {
        return 'category';
    }

    isBelowPrice() {
        return this.data.shop.msgLocation === 'below_price';
    }

    showContainer(container) {
        $(container).show();
    }

    hideMessage(container) {
        $(container).hide();
    }

}

new PayLaterMessageShop();

