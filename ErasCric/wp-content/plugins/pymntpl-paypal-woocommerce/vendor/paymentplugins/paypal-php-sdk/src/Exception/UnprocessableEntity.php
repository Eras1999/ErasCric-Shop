<?php


namespace PaymentPlugins\PayPalSDK\Exception;


class UnprocessableEntity extends ApiException {

	protected function parseErrorMessage( $data ) {
		if ( isset( $data['details'] ) ) {
			foreach ( $data['details'] as $error ) {
				$this->message = $error['description'];
			}
		} else {
			parent::parseErrorMessage( $data );
		}
	}
}