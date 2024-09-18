import $ from 'jquery';
import cart from '@ppcp/cart';

cart.addFilter('addToCartData', (data, cart) => {
    const extraData = $('form.cart [name^="wapf["]').serializeArray().reduce((carry, obj) => {
        const match = obj.name.match(/^wapf\[(field_[\w]+)\]$/);
        if (match) {
            return {
                ...carry,
                wapf: {
                    [match[1]]: obj.value,
                    ...(carry.wapf || {})
                }
            };
        }
    }, {wapf_field_groups: $('[name="wapf_field_groups"]').val()});
    return {...data, ...extraData};
});