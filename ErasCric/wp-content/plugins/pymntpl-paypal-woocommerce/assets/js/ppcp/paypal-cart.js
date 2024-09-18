import {CartGateway} from '../payments/classes';
import cart from '@ppcp/cart';

new CartGateway(cart, {id: 'ppcp', context: 'cart'});
