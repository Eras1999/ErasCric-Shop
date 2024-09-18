import {ProductGateway} from "../payments/classes";
import Product from '@ppcp/product';
import cart from '@ppcp/cart';

new ProductGateway(new Product(), cart, {id: 'ppcp', context: 'product'});