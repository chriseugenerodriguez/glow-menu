<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_Order')) {

	class FPD_Order {

		public function __construct() {

			global $woocommerce;

			if( version_compare($woocommerce->version, '3.0.0', '>=') ) //WC 3.0
				add_action( 'woocommerce_new_order_item', array( &$this, 'add_order_item_meta'), 10, 2 );
			else
				add_action( 'woocommerce_add_order_item_meta', array( &$this, 'add_order_item_meta'), 10, 2 );

			//edit order item permalink, so it loads the customized product
			add_filter( 'woocommerce_order_item_permalink', array(&$this, 'change_order_item_permalink') , 10, 3 );

			//add additional links to order item
			add_action( 'woocommerce_order_item_meta_end', array(&$this, 'add_order_item_links') , 10, 4 );

		}

		//add order meta from the cart
		public function add_order_item_meta( $item_id, $item ) {

			$fpd_data = null;
			if( isset( $item->legacy_values['fpd_data'] ) )  // WC 3.0+
				$fpd_data = $item->legacy_values['fpd_data'];
			else if( isset( $item['fpd_data'] ) )  // WC <3.0
				$fpd_data = $item['fpd_data'];

			if( !is_null($fpd_data) ) {
				wc_add_order_item_meta( $item_id, 'fpd_data', $fpd_data );
			}

		}

		public function change_order_item_permalink( $permalink, $item, $order ) {

			if(isset($item['fpd_data'])) {

				$order_items = $order->get_items();
				$item_id = array_search($item, $order_items);

				if($item_id !== false) {

					$permalink = add_query_arg( array(
						'order' => method_exists($order,'get_id') ? $order->get_id() : $order->id,
						'item_id' => $item_id),
					$permalink );

				}
			}

			return $permalink;

		}

		public function add_order_item_links( $item_id, $item, $order, $plain_text=null ) {

			$product = $order->get_product_from_item( $item );

			//download button
			if( isset($item['fpd_data']) &&  $product->is_downloadable() && $order->is_download_permitted() ) {

				$url = add_query_arg( array(
					'order' => method_exists($order,'get_id') ? $order->get_id() : $order->id,
					'item_id' => $item_id),
				$product->get_permalink() );

				echo '<a href="'.esc_url( $url ).'" class="fpd-order-item-download" style="font-size: 0.85em;">Download</a>' ;
			}

			//view customized product link
			if( isset($item['fpd_data']) && fpd_get_option('fpd_order_show_element_props') ) {

				$url = add_query_arg( array(
					'order' => method_exists($order,'get_id') ? $order->get_id() : $order->id,
					'item_id' => $item_id),
				$product->get_permalink() );

				echo sprintf( '<a href="%s" style="display: block;font-size: 0.9em;">%s</a>', $url, FPD_Settings_Labels::get_translation('misc', 'woocommerce_order:_email_view_customized_product') );

				$fpd_data = $item['fpd_data'];
				$order = json_decode(stripslashes($fpd_data['fpd_product']), true);
				$views = $order['product'];
				foreach($views as $view) {

					$viewElements = $view['elements'];
					foreach($viewElements as $viewElement) {

						$elementParams = $viewElement['parameters'];
						if( isset($elementParams['isEditable']) && @$elementParams['isEditable'] ) {

							$values = array();

								//check if fill is set and if yes, look for a hex name
								if( isset($elementParams['fill']) && @$elementParams['fill'] && is_string($elementParams['fill']) ) {

									$hex =  strtolower(str_replace('#', '', $elementParams['fill']));
									$hex_title = isset($hex_names[$hex]) ? $hex_names[$hex] : $elementParams['fill'];
									array_push($values, '<span style="border:1px solid #f2f2f2;font-size:11px;margin-right:2px;padding:2px 3px;color:#fff;background: '.$elementParams['fill'].'">'.strtoupper($hex_title).'</span>' );

								}

								//get font family and text size
								if( isset($elementParams['fontFamily']) && @$elementParams['fontFamily'] )
									array_push($values, $elementParams['fontFamily'].', '.$elementParams['fontSize'].'px' );

								if( sizeof($values) > 0 ) {

									$title = isset($elementParams['text']) ? $elementParams['text'] : $viewElement['title'];
									echo '<div style="margin: 10px 0;"><p style="font-weight: bol;font-size:0.95em; margin: 10px 0 0px;">'.(strlen($title) > 20  ? substr($title, 0, 17) . '...' : $title).':</p>';
									echo implode(' ', $values).'</div>';

								}

						}

					}

				}


			}


		}
	}
}

new FPD_Order();

?>