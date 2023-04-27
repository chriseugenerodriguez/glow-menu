<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_Cart')) {

	class FPD_Cart {

		public function __construct() {

			//handler when a product is added to the cart
			add_action( 'woocommerce_add_to_cart', array( &$this, 'add_product_to_cart'), 10, 6 );

			//CART
			add_filter( 'woocommerce_add_cart_item', array(&$this, 'add_cart_item'), 10 );
			//add additional [fpd_data]([fpd_product],[fpd_price]) to cart item
			add_filter( 'woocommerce_add_cart_item_data', array(&$this, 'add_cart_item_data'), 10, 2 );
			//get cart item from session
			add_filter( 'woocommerce_get_cart_item_from_session', array(&$this, 'get_cart_item_from_session'), 10, 2 );
			//add some extra meta data
			add_filter( 'woocommerce_get_item_data', array(&$this, 'get_item_data'), 10, 2 );

			//reset cart item link so the customized product is loaded from the cart
			add_filter( 'woocommerce_cart_item_permalink', array(&$this, 'set_cart_item_permalink'), 100, 3 );
			add_filter( 'woocommerce_cart_item_name', array(&$this, 'reset_cart_item_link'), 100, 3 );

			//change cart item thumbnail
			add_filter( 'woocommerce_cart_item_thumbnail', array(&$this, 'change_cart_item_thumbnail'), 100, 3 );

			//no price when get quote is enabled
			add_filter( 'woocommerce_cart_item_price', array(&$this, 'cart_item_price'), 10, 3 );
			add_filter( 'woocommerce_cart_item_subtotal', array(&$this, 'cart_item_price'), 10, 3 );

		}

		public function cart_item_price( $price, $cart_item, $cart_item_key ) {

			if ( isset($cart_item['fpd_data']) && $cart_item['fpd_data'] ) {

				$product = $cart_item['data'];

				$product_settings = new FPD_Product_Settings( $product->get_id() );
				if( $product_settings->get_option('get_quote') ) {
					return '-';
				}

			}

			return $price;

		}

		public function add_product_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {

			if( isset($cart_item_data['fpd_data']) ) {

				//check if an old cart item exist
				if( !empty($cart_item_data['fpd_data']['fpd_remove_cart_item']) ) {

					global $woocommerce;
					$woocommerce->cart->set_quantity($cart_item_data['fpd_data']['fpd_remove_cart_item'], 0);

				}
			}

		}

		//hook into the cart
		public function add_cart_item( $cart_item ) {

			global $woocommerce;

			//check if data contains a product
	        if ( isset($cart_item['fpd_data']) && $cart_item['fpd_data'] ) {
		        $fpd_data = $cart_item['fpd_data'];
	            if (isset($fpd_data['fpd_product_price'])) {

		            $product = $cart_item['data'];
					$final_price = floatval($fpd_data['fpd_product_price']);

					//prices exclusive of tax
		           	/*if( !wc_prices_include_tax() ) {
			            $tax_rates 		= WC_Tax::get_rates( $product->get_tax_class() );
						$taxes      	= WC_Tax::calc_inclusive_tax( $fpd_data['fpd_product_price'], $tax_rates);
						$tax_amount 	= WC_Tax::get_tax_total( $taxes );
						$final_price 	= $final_price - $tax_amount;
		            }*/

					$product->set_price($final_price);

					$product_settings = new FPD_Product_Settings( $product->get_id() );
					if( $product_settings->get_option('get_quote') ) {
						$product->set_price(0);
					}

	            }

	        }

		    return $cart_item;

		}

		//store values from additional form fields
		public function add_cart_item_data( $cart_item_meta, $product_id ) {

			if( isset($_POST['fpd_product']) ) {

				$cart_item_meta['fpd_data'] = array();
				$cart_item_meta['fpd_data']['fpd_product'] = $_POST['fpd_product'];
				$cart_item_meta['fpd_data']['fpd_product_price'] = $_POST['fpd_product_price'];
				$cart_item_meta['fpd_data']['fpd_remove_cart_item'] = $_POST['fpd_remove_cart_item'];

				if( isset($_POST['fpd_product_thumbnail']) )
					$cart_item_meta['fpd_data']['fpd_product_thumbnail'] = $_POST['fpd_product_thumbnail'];

				//PLUS
				if( isset($_POST['fpd_bulk_variations_order']) && !empty($_POST['fpd_bulk_variations_order']))
					$cart_item_meta['fpd_data']['fpd_bulk_variations_order'] = $_POST['fpd_bulk_variations_order'];

			}

		    return $cart_item_meta;
		}

		public function get_cart_item_from_session( $cart_item, $values ) {

	        //check for fpd data in session
	        if (isset($values['fpd_data'])) {
	            $cart_item['fpd_data'] = $values['fpd_data'];
	        }

			//check if cart item is fancy product
	        if (isset($cart_item['fpd_data'])) {
	        	//add fpd data to cart item
	            $this->add_cart_item($cart_item);
	        }

	        return $cart_item;
	    }

		//meta data displayed after the title, key: value
	    public function get_item_data( $other_data, $cart_item ) {

		    if ( isset($cart_item['fpd_data']) && fpd_get_option('fpd_cart_show_element_props') ) {

				//get fpd data
				$fpd_data = $cart_item['fpd_data'];

				if( isset($fpd_data['fpd_product']) && $fpd_data['fpd_product'] ) {

					//get hex names, convert into array and make keys lowercase (hex values)
					$hex_names = json_decode(FPD_Settings_Advanced_Colors::get_hex_names_object_string(), true);
					$hex_names = array_change_key_case($hex_names, CASE_LOWER);

					$order = json_decode(stripslashes($fpd_data['fpd_product']), true);
					if( array_key_exists('product', $order) )
						$views = $order['product'];
					else
						$views = $order; //deprecated: getProduct() as used instead getOrder()

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
									array_push($other_data, array(
										'name' => '<span style="font-weight: normal;font-size:0.95em;">'.(strlen($title) > 20  ? substr($title, 0, 17) . '...' : $title).'</span>',
										'value' => implode(' ', $values)
									));

								}

							}

						}

					}

				}

			}

		    return $other_data;

	    }

	    public function set_cart_item_permalink( $permalink, $cart_item, $cart_item_key ) {

		    if ( isset($cart_item['fpd_data']) && $cart_item['fpd_data'] ) {

				 $permalink = add_query_arg( array('cart_item_key' => $cart_item_key), $permalink );

			}

			return $permalink;

	    }

		public function reset_cart_item_link( $link, $cart_item, $cart_item_key ) {

			if ( isset($cart_item['fpd_data']) && $cart_item['fpd_data'] ) {

				$url = '';


				//get href from a tag
				$dom = new DOMDocument;
				libxml_use_internal_errors(true);
				$dom->loadHTML( $link );
				$xpath = new DOMXPath( $dom );
				libxml_clear_errors();
				$doc = $dom->getElementsByTagName("a")->item(0);
				$href = $xpath->query(".//@href");
				foreach ( $href as $s ) {
					$url = $s->nodeValue;

				}

				if( !empty($url) ) {

					//set again for WPML
					$url = add_query_arg( array('cart_item_key' => $cart_item_key), $url );

					return sprintf( '<a href="%s">%s<br /><i style="opacity: 1; font-size: 0.9em;">%s</i></a>', $url, $cart_item['data']->get_title(), FPD_Settings_Labels::get_translation( 'misc', 'cart:_re-edit product' ) );
				}

			}

			return $link;

		}

		public function change_cart_item_thumbnail( $thumbnail, $cart_item = null ) {

			if( !is_null($cart_item) && isset($cart_item['fpd_data']) ) {

				$fpd_data = $cart_item['fpd_data'];

				//check if data contains the fancy product thumbnail
		        if ( isset($fpd_data['fpd_product_thumbnail']) && !empty($fpd_data['fpd_product_thumbnail']) ) {

		        	$dom = new DOMDocument;
					libxml_use_internal_errors(true);
					$dom->loadHTML( $thumbnail );
					$xpath = new DOMXPath( $dom );
					libxml_clear_errors();
					$doc = $dom->getElementsByTagName("img")->item(0);
					$src = $xpath->query(".//@src");
					$srcset = $xpath->query(".//@srcset");

					foreach ( $src as $s ) {
						$s->nodeValue = $fpd_data['fpd_product_thumbnail'];
					}

					foreach ( $srcset as $s ) {
						$s->nodeValue = $fpd_data['fpd_product_thumbnail'];
					}

					return $dom->saveXML( $doc );

		        }

			}

			return $thumbnail;

		}

	}
}

new FPD_Cart();

?>