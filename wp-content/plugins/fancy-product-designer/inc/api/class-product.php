<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_Product')) {

	class FPD_Product {

		public $id;

		public function __construct( $id ) {

			$this->id = $id;

		}

		public static function create( $title, $options = '', $thumbnail = '' ) {

			if( empty($title) )
				return false;

			global $wpdb, $charset_collate;

			//create products table if necessary
			if( !fpd_table_exists(FPD_PRODUCTS_TABLE) ) {
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

				//create products table
				$products_sql_string = "ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				              title TEXT COLLATE utf8_general_ci NOT NULL,
				              options TEXT COLLATE utf8_general_ci NULL,
				              thumbnail TEXT COLLATE utf8_general_ci NULL,
							  PRIMARY KEY (ID)";

				$sql = "CREATE TABLE ".FPD_PRODUCTS_TABLE." ($products_sql_string) $charset_collate;";
				dbDelta($sql);

			}

			self::columns_exist();

			$inserted = $wpdb->insert(
				FPD_PRODUCTS_TABLE,
				array(
					'title' => $title,
					'options' => $options,
					'thumbnail' => $thumbnail
				),
				array( '%s', '%s', '%s' )
			);

			return $inserted ? $wpdb->insert_id : false;

		}

		public static function exists( $id ) {

			if( fpd_table_exists(FPD_PRODUCTS_TABLE) ) {

				global $wpdb;
				$count = $wpdb->get_var("SELECT COUNT(*) FROM ".FPD_PRODUCTS_TABLE." WHERE ID=$id");
				return $count === "1";

			}
			else {
				return false;
			}

		}

		public function add_view( $title, $elements = '', $thumbnail = '', $order = NULL, $options = NULL ) {

			global $wpdb;

			FPD_View::create();

			//check if an order value is set
			if($order === NULL) {
				//count views of a fancy product
				$count = $wpdb->get_var("SELECT COUNT(*) FROM ".FPD_VIEWS_TABLE." WHERE product_id=".$this->id."");
				//count is the order value
				$order = intval($count);
			}

			FPD_View::columns_exist();

			$elements = is_array($elements) ? json_encode($elements) : $elements;
			$options = is_object($options) ? json_encode($options) : $options;

			$inserted = $wpdb->insert(
				FPD_VIEWS_TABLE,
				array(
					'product_id' => $this->id,
					'title' => $title,
					'elements' => $elements ? $elements : '',
					'thumbnail' => $thumbnail ? $thumbnail : '',
					'view_order' => $order,
					'options' => $options ? $options : ''
				),
				array( '%d', '%s', '%s', '%s', '%d', '%s')
			);

			return $inserted ? $wpdb->insert_id : false;

		}

		public function update( $title, $options=null, $thumbnail=null ) {

			global $wpdb;

			$columns = array();
			$colum_formats = array();

			if( !empty($title) ) {

				$columns['title'] = $title;
				array_push($colum_formats, '%s');
			}

			if( !is_null( $options ) ) {

				$columns['options'] = empty($options) ? '' : json_encode($options);
				array_push($colum_formats, '%s');
			}

			if( !is_null( $thumbnail ) ) {

				$columns['thumbnail'] = $thumbnail;
				array_push($colum_formats, '%s');
			}

			if( !empty($columns) ) {

				self::columns_exist();

				$wpdb->update(
				 	FPD_PRODUCTS_TABLE,
				 	$columns, //what
				 	array('ID' => $this->id), //where
				 	$colum_formats, //format what
				 	array('%d') //format where
				);

			}

			return $columns;

		}

		public function duplicate( $new_product_id ) {

			$new_fp = new FPD_Product( $new_product_id );

			try {

				$html = '';
				foreach( $this->get_views() as $view ) {

					$view_id = $new_fp->add_view($view->title, $view->elements, $view->thumbnail, $view->view_order, $view->options);

					$html .= FPD_Admin_Manage_Products::get_view_item_html(
						$view_id,
						$view->thumbnail,
						$view->title
					);

				}

				return array(
					'html'	  => $html,
					'message' => __('Views successfully created!', 'radykal')
				);

			}
			catch(Exception $e) {

				return array(
					'error' => 1,
					'message' => __('Product could not be stored. Please try again!', 'radykal'),
					'exception' => $e
				);

			}

		}

		public function delete() {

			global $wpdb;

			try {

				$wpdb->query( $wpdb->prepare("DELETE FROM ".FPD_PRODUCTS_TABLE." WHERE ID=%d", $this->id) );
				$wpdb->query( $wpdb->prepare("DELETE FROM ".FPD_VIEWS_TABLE." WHERE product_id=%d", $this->id) );

				return 1;
			}
			catch(Exception $e) {
				return 0;
			}

		}

		public function get_category_ids() {

			global $wpdb;

			$category_ids = array();

			if( fpd_table_exists(FPD_CATEGORY_PRODUCTS_REL_TABLE) ) {

				$categories = $wpdb->get_results("SELECT category_id FROM ".FPD_CATEGORY_PRODUCTS_REL_TABLE." WHERE product_id=".$this->id."");

				foreach($categories as $category) {
					array_push($category_ids, $category->category_id);
				}

			}

			return $category_ids;

		}

		public function get_data() {

			global $wpdb;

			$product_array = array();
			$views = $wpdb->get_results("SELECT * FROM ".FPD_VIEWS_TABLE." WHERE product_id=".$this->id." ORDER BY view_order ASC");
			foreach($views as $view) {

				$view_array = array(
					'title' => $view->title,
					'thumbnail' => $view->thumbnail,
					'elements' => $view->elements,
					'options' => $view->options
				);

				$product_array[] = $view_array;

			}

			return $product_array;

		}

		public function get_thumbnail() {

			global $wpdb;

			self::columns_exist();

			return $wpdb->get_var("SELECT thumbnail FROM ".FPD_PRODUCTS_TABLE." WHERE ID=".$this->id."");

		}

		public function get_title() {

			global $wpdb;

			return $wpdb->get_var("SELECT title FROM ".FPD_PRODUCTS_TABLE." WHERE ID=".$this->id."");

		}

		public function get_options() {

			global $wpdb;

			$options = $wpdb->get_var("SELECT options FROM ".FPD_PRODUCTS_TABLE." WHERE ID=".$this->id."");

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

		public function get_views( $serialize_elements = false ) {

			global $wpdb;

			$views = array();

			if( fpd_table_exists(FPD_VIEWS_TABLE) ) {

				$views = $wpdb->get_results("SELECT * FROM ".FPD_VIEWS_TABLE." WHERE product_id=".$this->id." ORDER BY view_order ASC");
				//updates the image sources to the current domain and protocol
				foreach($views as $view_key => $view) {

					//update thumbnail source
					$view->thumbnail = $this->reset_image_source($view->thumbnail);

					//V2 - views are serialized
					$elements = is_serialized( $view->elements ) ? @unserialize($view->elements) : json_decode($view->elements, true);
					if( is_array($elements) ) {

						foreach( $elements as $key => $element ) {
							if($element['type'] == 'image') {
								$updated_image = $this->reset_image_source($element['source']);
								$element['source'] = $updated_image;
							}

							$elements[$key] = $element;
						}

						$view->elements = $serialize_elements ? serialize($elements) : $elements;

					}

				}

			}

			return $views;

		}

		public function to_JSON() {

			$product_array = array();

			$product_options = $this->get_options();

			$views = $this->get_views();

			//get first view to push product options into
			$first_view_obj = $views[0];
			$first_view = new FPD_View($first_view_obj->ID);
			$first_view_options = $first_view->get_options();
			//merge product options into first view options
			$first_view_options = array_merge((array) $product_options, (array) $first_view_options);
			$first_view_options = FPD_View::setup_options($first_view_options);

			$view_count = 0;
			foreach($views as $view) {

				foreach($view->elements as $key => $element) {
					$parameters_string = FPD_Parameters::to_json($element['parameters'], $element['type']);
					$view->elements[$key]['parameters'] = json_decode($parameters_string, true);
				}

				$fancy_view = new FPD_View( $view->ID );
				$view_array = array(
					'title' => $view->title,
					'thumbnail' => $view->thumbnail,
					'elements' => $view->elements,
					'options' => $view_count == 0 ? $first_view_options : $fancy_view->get_options()
				);

				$product_array[] = $view_array;
				$view_count++;

			}

			return json_encode($product_array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		}

		private function reset_image_source($string) {

			return preg_replace("/(http|https):\/\/(.*?)\/wp-content/i", content_url(), $string);

		}

		private static function columns_exist() {

			global $wpdb;

			$thumbnail_col_exists = $wpdb->get_var( "SHOW COLUMNS FROM ".FPD_PRODUCTS_TABLE." LIKE 'thumbnail'" );
			if( empty($thumbnail_col_exists) ) {
				$wpdb->query( "ALTER TABLE ".FPD_PRODUCTS_TABLE." ADD COLUMN thumbnail TEXT COLLATE utf8_general_ci NULL" );
			}

		}

	}

}

?>