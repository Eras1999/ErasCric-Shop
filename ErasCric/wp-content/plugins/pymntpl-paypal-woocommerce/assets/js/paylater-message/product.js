import $ from 'jquery';
import PayLaterBaseMessage from "./base";
import Product from '@ppcp/product';
import {loadPayPalSdk, getPayPalQueryParams} from "@ppcp/utils";

class PayLaterMessageProduct extends PayLaterBaseMessage {

    constructor(paypal, product, props) {
        super('product', props);
        this.paypal = paypal;
        this.product = product;
        this.initialize();
    }

    initialize() {
        this.product.on('quantityChange', this.onQuantityChange.bind(this));
        this.product.on('foundVariation', this.onFoundVariation.bind(this));
        this.product.on('resetVariation', this.onResetVariation.bind(this));
        super.initialize();
    }

    onQuantityChange(qty) {
        this.createMessage();
    }

    onFoundVariation(changed) {
        this.createMessage();
    }

    onResetVariation(product) {
        this.createMessage();
    }

    getMessageContainer() {
        const el = document.getElementById('wc-ppcp-paylater-msg-product');
        if (!el) {
            $('form.checkout .shop_table').after('<div id="wc-ppcp-paylater-msg-product"></div>');
        }
        return document.getElementById('wc-ppcp-paylater-msg-product');
    }

    getTotal() {
        return this.product.getTotal();
    }

    getPlacement() {
        return 'product';
    }
}

const queryParams = getPayPalQueryParams();
loadPayPalSdk(queryParams).then(paypal => {
    new PayLaterMessageProduct(paypal, new Product(), {queryParams});
}).catch(error => {

})

export default PayLaterMessageProduct;
export {PayLaterMessageProduct};