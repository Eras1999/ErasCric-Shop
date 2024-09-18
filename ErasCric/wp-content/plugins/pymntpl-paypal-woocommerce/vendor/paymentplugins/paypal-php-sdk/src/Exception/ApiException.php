<?php


namespace PaymentPlugins\PayPalSDK\Exception;

use Throwable;

/**
 * Class APIException
 *
 * @package PaymentPlugins\PayPalSDK\Exception
 */
class ApiException extends \Exception {

	protected $errorCode;

	private $errorData;

	public function __construct( $code = 0, $data = [] ) {
		$this->code = $code;
		$this->initialize( $data );
	}

	private function initialize( $data = [] ) {
		$this->errorData = $data;
		$this->parseErrorMessage( $data );

		if ( isset( $data['name'] ) ) {
			$this->errorCode = $data['name'];
		} elseif ( isset( $data['error'] ) ) {
			$this->errorCode = $data['error'];
		} else {
			$this->errorCode = $this->getCode();
		}

		if ( isset( $data['details'] ) && \is_array( $data['details'] ) ) {
			foreach ( $data['details'] as $error ) {
				if ( isset( $error['issue'] ) ) {
					$this->errorCode = $error['issue'];
				} elseif ( isset( $error['name'] ) ) {
					$this->errorCode = $error['name'];
				}
			}
		}
	}

	protected function parseErrorMessage( $data ) {
		if ( isset( $data['error_description'] ) ) {
			$this->message = $data['error_description'];
		} elseif ( isset( $data['message'] ) ) {
			$this->message = $data['message'];
		} elseif ( isset( $data['errors'] ) ) {
			$this->parseErrorMessage( $data['errors'][0] );
		}
	}

	public function getErrorCode() {
		return $this->errorCode;
	}

	public function getData() {
		return $this->errorData;
	}

}