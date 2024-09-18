import cart from '@ppcp/cart';

let isMondialSelected = false;

cart.on('checkout_on_shipping_change', (data, shippingMethod) => {
    isMondialSelected = false;
    if (shippingMethod) {
        // check if Mondial shipping method
        for (let index of Object.keys(shippingMethod)) {
            const method = shippingMethod[index];
            if (method.includes("mondialrelay_official_shipping")) {
                isMondialSelected = true;
            }
        }
    }
})

cart.addFilter('checkout_submit_form', (bool, order, gateway) => {
    if (bool && isMondialSelected) {
        bool = false;
    }
    return bool;
});