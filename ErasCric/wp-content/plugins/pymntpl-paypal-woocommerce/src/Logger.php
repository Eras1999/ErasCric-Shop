<?php


namespace PaymentPlugins\WooCommerce\PPCP;


use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\APISettings;

class Logger {

	private $log;

	private $source;

	private $settings;

	/**
	 * Logger constructor.
	 *
	 * @param string $source
	 */
	public function __construct( $source ) {
		$this->source = $source;
	}

	protected function get_source() {
		return $this->source;
	}

	public function log( $lvl, $msg, ...$args ) {
		if ( ! $this->log ) {
			$this->log = wc_get_logger();
		}
		if ( ! $this->settings ) {
			$this->settings = wc_ppcp_get_container()->get( APISettings::class );
		}
		$write = $this->is_debug_context_enabled( null );
		if ( count( $args ) === 1 ) {
			list( $context ) = $args;
			$write = $this->is_debug_context_enabled( $context );
		} elseif ( count( $args ) === 2 ) {
			list( $msg2, $context ) = $args;
			$write = $this->is_debug_context_enabled( $context );
			if ( $write ) {
				$msg = $msg . $msg2;
			}
		}
		/**
		 * 1. If log should be written then proceed.
		 * 2. If not $write, then still write if $args are empty. That's because when args are empty, it's just a lone message.
		 * 3. If there is a message, and arg count is greater than one, then still write the lone message
		 */
		if ( $write || empty( $args ) || ( $msg && count( $args ) > 1 ) ) {
			$this->log->log( $lvl, $msg, [ 'source' => $this->get_source() ] );
		}
	}

	public function info( $msg, ...$args ) {
		$this->log( \WC_Log_Levels::INFO, $msg, ...$args );
	}

	public function error( $msg, ...$args ) {
		$this->log( \WC_Log_Levels::ERROR, $msg, ...$args );
	}

	public function warning( $msg, ...$args ) {
		$this->log( \WC_Log_Levels::WARNING, $msg, ...$args );
	}

	private function is_debug_context_enabled( $context ) {
		switch ( $context ) {
			case 'payment':
				$enabled = $this->settings->debug_payment_enabled();
				break;
			case 'webhook':
				$enabled = $this->settings->debug_webhook_enabled();
				break;
			default:
				$enabled = true;
		}

		return $enabled;
	}

}