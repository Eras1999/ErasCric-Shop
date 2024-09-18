import {MiniCartGateway} from "../payments/classes";
import cart from '@ppcp/cart';

class PaypalMinicart extends MiniCartGateway {

}

new PaypalMinicart(cart, {id: 'ppcp', context: 'cart'});