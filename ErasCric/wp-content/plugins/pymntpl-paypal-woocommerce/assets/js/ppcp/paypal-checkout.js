import {CheckoutGateway} from '../payments/classes';
import cart from '@ppcp/cart';
import {isPluginConnected} from '@ppcp/utils';

if (isPluginConnected()) {
    new CheckoutGateway(cart, {id: 'ppcp', context: 'checkout'});
}