import $ from 'jquery';
import {getRestRoute} from '@ppcp/utils';

let bucket;

$(document).on('wfocuBucketCreated', async (e, Bucket) => {
    bucket = Bucket;

    if (window?.wfocu_vars?.wcPPCPSettings) {
        window.wcPPCPSettings = window.wfocu_vars.wcPPCPSettings
    }
});

$(document).on('wfocu_external', (e, Bucket) => {
    bucket = Bucket;
    bucket.inOfferTransaction = true;
    createOrder().then(({redirect}) => {
        window.location = redirect;
    }).catch(error => {
        bucket.swal.show({'text': wfocu_vars?.messages?.offer_msg_pop_failure, 'type': 'warning'});
        window.location = wfocu_vars.order_received_url;
    })
});

const createOrder = () => {
    return new Promise((resolve, reject) => {
        $.ajax({
            method: 'post',
            url: getRestRoute('funnelkit/upsell/order'),
            data: bucket.getBucketSendData()
        }).done(response => {
            // redirect to the PayPal URL
            resolve(response);
        }).fail(error => {
            reject(error);
        })
    })
}


