import $ from 'jquery';
import cart from '@ppcp/cart';

cart.on('productButtonOnInit', () => {
    $('.wc-stripe-product-checkout-container').addClass('active');
});

cart.on('cartButtonOnInit', () => {
    $('.wc_stripe_cart_payment_methods').addClass('active');
})

cart.on('expressCheckoutButtonOnInit', () => {
    $('.banner_payment_method_ppcp').addClass('active');
    $('.banner_payment_method_ppcp').closest('.wc-stripe-banner-checkout').addClass('active');
})