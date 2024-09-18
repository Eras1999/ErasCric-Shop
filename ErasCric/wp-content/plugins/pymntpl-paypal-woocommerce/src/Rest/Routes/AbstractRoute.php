<?php


namespace PaymentPlugins\WooCommerce\PPCP\Rest\Routes;


abstract class AbstractRoute {

	public function get_namespace() {
		return 'wc-ppcp/v1';
	}

	public abstract function get_path();

	public abstract function get_routes();

	public function get_full_path() {
		return '/' . trim( $this->get_namespace() ) . '/' . trim( $this->get_path(), '/' );
	}

	/**
	 * @param \WP_REST_Request $request
	 */
	public function handle_request( \WP_REST_Request $request ) {
		$method = strtolower( $request->get_method() );
		$func   = "handle_{$method}_request";
		try {
			$result = $this->{$func}( $request );
			if ( \is_wp_error( $result ) ) {
				return $this->get_error_response( $result );
			}

			return rest_ensure_response( apply_filters( "wc_ppcp_{$method}_{$this->get_path()}", $result ) );
		} catch ( \Exception $e ) {
			return $this->get_error_response( $e );
		}
	}

	public function get_error_response( $error ) {
		if ( $error instanceof \Exception ) {
			return new \WP_Error( 'rest-error', $error->getMessage(), [ 'status' => $error->getCode() ] );
		} elseif ( is_wp_error( $error ) ) {
			return $error;
		}
	}


	public function handle_get_request( \WP_REST_Request $request ) {
		throw new \Exception( 'Method not implemented', 405 );
	}

	public function handle_post_request( \WP_REST_Request $request ) {
		throw new \Exception( 'Method not implemented', 405 );
	}

	public function handle_put_request( \WP_REST_Request $request ) {
		throw new \Exception( 'Method not implemented', 405 );
	}

	public function handle_delete_request( \WP_REST_Request $request ) {
		throw new \Exception( 'Method not implemented', 405 );
	}

}