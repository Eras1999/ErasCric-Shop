<?php


namespace PaymentPlugins\WooCommerce\PPCP;


class TemplateLoader {

	private $config;

	private $template_path;

	public function __construct( Config $config, $template_path ) {
		$this->config        = $config;
		$this->template_path = $template_path;
	}

	public function load_template( $template_name, $args = [] ) {
		\wc_get_template( $template_name, $args, $this->template_path(), $this->default_template_path() );
	}

	public function load_template_html( $template_name, $args = [] ) {
		return \wc_get_template_html( $template_name, $args, $this->template_path(), $this->default_template_path() );
	}

	private function template_path() {
		return $this->template_path;
	}

	private function default_template_path() {
		return $this->config->get_path( 'templates/' );
	}

}