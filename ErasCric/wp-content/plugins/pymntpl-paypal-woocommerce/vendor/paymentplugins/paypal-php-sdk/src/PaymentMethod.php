<?php

namespace PaymentPlugins\PayPalSDK;

/**
 * @property string $payer_selected
 * @property string $payee_preferred
 * @property string $standard_entry_class_codeenum
 */
class PaymentMethod extends AbstractObject {

	const UNRESTRICTED = 'UNRESTRICTED';

	const IMMEDIATE_PAYMENT_REQUIRED = 'IMMEDIATE_PAYMENT_REQUIRED';

	const TEL = 'TEL';

	const WEB = 'WEB';

	const CCD = 'CCD';

	const PPD = 'PPD';

}