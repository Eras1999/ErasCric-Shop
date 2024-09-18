import $ from 'jquery';
import apiFetch from '@wordpress/api-fetch';
import {getSetting} from '@ppcp/utils';
import Event from './event';

class MiniCart extends Event {

    constructor() {
        super();
        this.data = getSetting('cart');
        this.initialize();
    }

    initialize() {
        $(document.body).on('wc_fragments_refreshed wc_fragments_loaded', this.onFragmentsChanged.bind(this));
    }

    onFragmentsChanged() {
        // fetch updated cart data
        setTimeout(() => {
            this.trigger('fragmentsChanged', this);
        }, 250);
    }

    needsShipping() {
        return this.data?.needsShipping;
    }
}

export default new MiniCart();