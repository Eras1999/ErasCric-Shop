import $ from 'jquery';
import cart from '@ppcp/cart';

cart.addFilter('addToCartData', (data, cart) => {
    const extraData = $('form.cart .wc-pao-addon-field').serializeArray().reduce((carry, obj) => {
        if (/([\w\-\_]+)\[\]$/.test(obj.name)) {
            const name = obj.name.substring(0, obj.name.length - 2);
            return {
                ...carry,
                [name]: [
                    ...(carry[name] || []),
                    obj.value
                ]
            }
        }
        return {...carry, [obj.name]: obj.value};
    }, {});
    return {...data, ...extraData};
});