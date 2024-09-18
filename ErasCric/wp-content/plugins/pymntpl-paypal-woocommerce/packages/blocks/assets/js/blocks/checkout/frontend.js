import {registerCheckoutBlock} from '@woocommerce/blocks-checkout';

import metadata from './block.json';
import {Block} from './block';

registerCheckoutBlock({
    metadata,
    component: Block
});