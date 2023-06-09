<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_View') ) {

	class FPD_View {

		public $id;
		public $options = array(
			'customImagePrice' => null,
			'customTextPrice' => null,
			'addImage' => null,
			'addText' => null,
			'addFacebook' => null,
			'addInstagram' => null,
			'addDesigns' => null
		);

		public function __construct( $id ) {

			$this->id = $id;

		}

		public static function create() {

			global $wpdb, $charset_collate;

			//create views table if necessary
			if( !fpd_table_exists(FPD_VIEWS_TABLE) ) {
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				//create table
				$views_sql = "ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
							  product_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
				              title TEXT COLLATE utf8_general_ci NOT NULL,
				              thumbnail TEXT COLLATE utf8_general_ci NOT NULL,
				              elements LONGTEXT COLLATE utf8_general_ci NULL,
				              view_order INT COLLATE utf8_general_ci NULL,
				              options TEXT COLLATE utf8_general_ci NULL,
							  PRIMARY KEY (ID)";

				$sql = "CREATE TABLE ".FPD_VIEWS_TABLE." ($views_sql) $charset_collate;";

				dbDelta($sql);
			}

		}

		public function get_elements() {

			global $wpdb;
			$result = $wpdb->get_var("SELECT elements FROM ".FPD_VIEWS_TABLE." WHERE ID=".$this->id."");
			//V2 - views are serialized
			return maybe_unserialize( $result );

		}

		public function get_data() {

			global $wpdb;
			return $wpdb->get_row("SELECT * FROM ".FPD_VIEWS_TABLE." WHERE ID=".$this->id."");

		}

		public function get_product_id() {

			global $wpdb;
			return $wpdb->get_var("SELECT product_id FROM ".FPD_VIEWS_TABLE." WHERE ID=".$this->id."");

		}

		public function get_options() {

			global $wpdb;

			self::columns_exist();

			$options = $wpdb->get_var("SELECT options FROM ".FPD_VIEWS_TABLE." WHERE ID=".$this->id."");

			if( empty($options) )
				return array();

			json_decode($options);
			if( json_last_error() !== JSON_ERROR_NONE ) { //V3.4.2 or lower, options are stored as HTML string
				$options = fpd_convert_obj_string_to_array($options);
			}
			else {
				$options = json_decode($options, true);
			}

			return $options;

		}

		public function update( $data_array = array() ) {

			global $wpdb;

			//all available columns with format that can be updated
			$data_keys = array(
				'product_id' => '%d',
				'title' => '%s',
				'thumbnail' => '%s',
				'elements' => '%s',
				'view_order' => '%d',
				'options' => '%s'
			);

			//the data and formats arrays that will be used in the sql
			$data = array();
			$formats = array();

			//loop through all available keys and check if the key exist in the passed data_array
			foreach( $data_keys as $key => $value ) {

				if( array_key_exists( $key, $data_array ) ) {

					if($key == 'options') {
						$options = $data_array[$key];
						$data[$key] = empty($options) ? '' : json_encode($options);
					}
					else {
						$data[$key] = $data_array[$key];
					}
					$formats[] = $data_keys[$key];

				}

			}

			self::columns_exist();

			//update view with the passed data and return number of updated columns
			return $wpdb->update(
				FPD_VIEWS_TABLE,
				$data,
				array('ID' => $this->id),
				$formats,
				'%d'
			);

		}

		public function duplicate( $new_title ) {

			global $wpdb;

			$data = $this->get_data();
			$count = $wpdb->get_var("SELECT COUNT(*) FROM ".FPD_VIEWS_TABLE." WHERE product_id=".$data->product_id."");

			$inserted = $wpdb->insert(
				FPD_VIEWS_TABLE,
				array(
					'product_id' => $data->product_id,
					'title' => $new_title,
					'thumbnail' => $data->thumbnail,
					'elements' => $data->elements,
					'view_order' => intval($count)
				),
				array( '%d', '%s', '%s', '%s', '%d')
			);

			return $inserted ? $wpdb->get_row("SELECT * FROM ".FPD_VIEWS_TABLE." WHERE ID=".$wpdb->insert_id."") : false;

		}

		public function delete() {

			global $wpdb;

			try {
				$wpdb->query( $wpdb->prepare("DELETE FROM ".FPD_VIEWS_TABLE." WHERE ID=%d", $this->id) );
				return 1;
			}
			catch(Exception $e) {
				return 0;
			}

		}

		public function get_html_attrs( $title='', $thumbnail='', $product_options = array() ) {

			$options = $this->get_options();
			$options = array_merge((array) $product_options, (array) $options);

			$attrs_str = "title='" . esc_attr( $title ) . "'";
			$attrs_str .= " data-thumbnail='" . esc_attr( $thumbnail ) . "'";
			$attrs_str .= " data-options='". self::setup_options($options, true)."'";

			if( isset($options['mask']) && fpd_not_empty($options['mask']) )
				$attrs_str .= " data-mask='" . json_encode( $options['mask'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."'";

			return $attrs_str;

		}

		public static function setup_options( $options, $to_JSON = false ) {

			$options_arr = array();

			foreach($options as $key => $value) {

				if( fpd_not_empty($value) ) {

					switch($key) {
						case 'stage_width':
							$options_arr['stageWidth'] = floatval($value);
						break;
						case 'stage_height':
							$options_arr['stageHeight'] = floatval($value);
						break;
						case 'designs_parameter_price':
							$options_arr['customImageParameters'] = array( 'price' => floatval($value) );
						break;
						case 'custom_texts_parameter_price':
							$options_arr['customTextParameters'] = array( 'price' => floatval($value) );
						break;
						case 'max_price':
							$options_arr['maxPrice'] = floatval($value);
						break;
					}

				}

			}

			$options_arr['customAdds'] = array(
				'uploads' => !isset($options['disable_image_upload']),
				'texts' => !isset($options['disable_custom_text']),
				'designs' => !isset($options['disable_designs'])
			);

			return $to_JSON ? json_encode($options_arr , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $options_arr;

		}

		public static function columns_exist() {

			global $wpdb;

			$options_col_exist = $wpdb->query( "SHOW COLUMNS FROM ".FPD_VIEWS_TABLE." LIKE 'options'" );
			if( empty($options_col_exist) ) {
				$wpdb->query("ALTER TABLE ".FPD_VIEWS_TABLE." ADD options TEXT COLLATE utf8_general_ci NULL;");
			}

		}

	}

}

?>