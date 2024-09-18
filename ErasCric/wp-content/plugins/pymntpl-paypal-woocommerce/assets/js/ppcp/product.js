import $ from 'jquery';
import {getSetting} from "@ppcp/utils";
import {isEqual} from 'lodash';
import Event from './event';

class Product extends Event {

    constructor() {
        super();
        this.data = this.default_data = getSetting('product');
        this.variation = false;
        $(document.body).on('change', '[name="quantity"]', this.onQuantityChange.bind(this));
        $(document.body).on('found_variation', this.foundVariation.bind(this));
        $(document.body).on('reset_data', this.resetVariationData.bind(this));
    }

    needsShipping() {
        return this.data?.needsShipping;
    }

    isVariationSelected() {
        return !!this.variation;
    }

    onQuantityChange(e) {
        if (e?.isTrigger) {
            //cause a short delay so this won't execute before foundVariation
            setTimeout(() => {
                this.trigger('quantityChange', this.getQuantity(), this);
            }, 50);
        } else {
            this.trigger('quantityChange', this.getQuantity(), this);
        }
    }

    foundVariation(e, variation) {
        this.variation = variation;
        const prevData = {...this.data};
        this.data = {
            ...this.data, ...{
                price: variation.display_price,
                needsShipping: !variation.is_virtual
            }
        };
        this.trigger('foundVariation', !isEqual(this.data, prevData), this);
    }

    resetVariationData() {
        this.variation = null;
        this.data = this.default_data;
        this.trigger('resetVariation', this);
    }

    getQuantity() {
        return parseInt($('[name="quantity"]').val());
    }

    getPrice() {
        return this.data?.price;
    }

    getTotal() {
        return this.getQuantity() * this.getPrice();
    }

    isVariableProduct() {
        return $('[name="variation_id"]').length > 0;
    }

    isVariableProductSelected() {
        const val = $('input[name="variation_id"]').val();
        return !!val && "0" !== val;
    }

    getVariationData() {
        if (this.isVariableProduct()) {
            const attributes = this.variation ? this.variation.attributes : {};
            const elements = document.querySelectorAll('.variations [name^="attribute_"]');
            if (elements) {
                elements.forEach((element) => {
                    if (!(element.name in attributes)) {
                        attributes[element.name] = element.value;
                    }
                });
            }
            return attributes;
        }
        return null;
    }

    getId() {
        return parseInt(this.data?.id);
    }

    getVariationId() {
        if (this.variation) {
            return this.variation.variation_id;
        }
        return $('[name="variation_id"]').val();
    }
}

export {Product};
export default Product;