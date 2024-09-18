import $ from 'jquery';
import cart from '@ppcp/cart';

cart.addFilter('addToCartData', (data, cart) => {
    const extraData = $('form.cart .tmcp-field').serializeArray().reduce((carry, obj) => {
        return {...carry, [obj.name]: obj.value};
    }, {});
    return {...data, ...extraData};
});