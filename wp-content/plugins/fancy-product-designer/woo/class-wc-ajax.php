<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_WC_Ajax') ) {

	class FPD_WC_Ajax {

		public function __construct() {

			add_action( 'init', array( &$this, 'init') );

		}

		public function init() {

			//load a product via ajax (used for different product variation)
			add_action( 'wp_ajax_fpd_load_product', array( &$this, 'load_product' ) );
			add_action( 'wp_ajax_nopriv_fpd_load_product', array( &$this, 'load_product' ) );

		}

		public function load_product() {

			if( !isset($_POST['product_id']) )
				die;

			$product_id = $_POST['product_id'];

			$fancy_product = new FPD_Product( $product_id );
			echo $fancy_product->to_JSON();

			die;

		}

	}

}

new FPD_WC_Ajax();

?>