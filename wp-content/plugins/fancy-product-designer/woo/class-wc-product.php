<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_WC_Product') ) {

	class FPD_WC_Product {

		public function __construct() {

			//wp_head
			add_action( 'fpd_post_fpd_enabled', array( &$this, 'head_frontend'), 10, 2 );

			add_filter( 'post_class', array( &$this, 'product_css_class') );

			add_filter( 'woocommerce_product_single_add_to_cart_text', array( &$this, 'add_to_cart_text'), 20, 2 );
			//before product container
			add_action( 'woocommerce_before_single_product', array( &$this, 'before_product_container'), 1 );

			add_action( 'fpd_before_product_designer', array( &$this, 'before_product_designer'), 1 );
			add_action( 'fpd_after_product_designer', array( &$this, 'after_product_designer'), 1 );

			//add customize button
			$customize_btn_pos = fpd_get_option('fpd_start_customizing_button_position');
			if( $customize_btn_pos == 'under-short-desc' ) {
				add_action( 'woocommerce_single_product_summary', 'FPD_Frontend_Product::add_customize_button', 25 );
			}
			else if( $customize_btn_pos == 'before-add-to-cart-button') {
				add_action( 'woocommerce_before_add_to_cart_button', 'FPD_Frontend_Product::add_customize_button', 0 );
			}
			else {
				add_action( 'woocommerce_after_add_to_cart_button', 'FPD_Frontend_Product::add_customize_button', 0 );
			}

			//add additional form fields to cart form
			add_action( 'woocommerce_before_add_to_cart_button', array( &$this, 'add_product_designer_form') );

			//change product by variation
			add_filter( 'woocommerce_available_variation', array( &$this, 'set_variation_meta'), 20, 3 );
			add_action( 'woocommerce_after_variations_form', array( &$this, 'add_variation_handler') );

			//enable share for wc
			if( fpd_get_option('fpd_sharing') ) {
				add_action( 'woocommerce_share' , array( &$this, 'add_share' ) );
			}

		}

		public function head_frontend( $post, $product_settings ) {

			$product_settings = new FPD_Product_Settings( $post->ID );
			$main_bar_pos = $product_settings->get_option('main_bar_position');

			if( $main_bar_pos === 'after_product_title' ) {
				add_action( 'woocommerce_single_product_summary', array( &$this, 'add_main_bar_container'), 7 );
			}
			else if( $main_bar_pos === 'after_excerpt' ) {
				add_action( 'woocommerce_single_product_summary', array( &$this, 'add_main_bar_container'), 25 );
			}

		}

		public function product_css_class( $classes ) {

			global $post;

			$product_settings = new FPD_Product_Settings( $post->ID );
			$cb_var_needed = $product_settings->get_option('wc_customize_variation_needed');

			if( $cb_var_needed ) {
				$classes[] = 'fpd-variation-needed';
			}

			return $classes;

		}

		//add a main bar container
		public function add_main_bar_container() {

			echo '<div class="fpd-main-bar-position"></div>';

		}

		//custom text for the add-to-cart button in single page
		public function add_to_cart_text( $text, $product ) {

			if( is_fancy_product( $product->get_id() ) ) {

				$product_settings = new FPD_Product_Settings( $product->get_id() );

				if( is_product() ) { //only change text if on single product page and get quote is enabled
					if( $product_settings->get_option('get_quote') )
						return FPD_Settings_Labels::get_translation( 'misc', 'get_a_quote' );
				}

			}

			return $text;

		}

		public function before_product_container() {

			global $post;

			if( is_fancy_product( $post->ID ) ) {

				//add product designer
				$product_settings = new FPD_Product_Settings( $post->ID );
				$position = $product_settings->get_option('placement');

				if( $position  == 'fpd-replace-image') {
					add_action( 'woocommerce_before_single_product_summary', 'FPD_Frontend_Product::add_product_designer', 15 );
				}
				else if( $position  == 'fpd-under-title') {
					add_action( 'woocommerce_single_product_summary', 'FPD_Frontend_Product::add_product_designer', 6 );
				}
				else if( $position  == 'fpd-after-summary') {
					add_action( 'woocommerce_after_single_product_summary', 'FPD_Frontend_Product::add_product_designer', 1 );
				}
				else {
					add_action( 'fpd_product_designer', 'FPD_Frontend_Product::add_product_designer' );
				}

				//remove product image, there you gonna see the product designer
				if( $product_settings->get_option('hide_product_image') || ($position == 'fpd-replace-image' && (!$product_settings->customize_button_enabled)) ) {
					remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
				}

			}
		}

		public function before_product_designer( $post ) {

			if( get_post_type( $post ) !== 'product' )
				return;

			global $product, $woocommerce;

			//added to cart, recall added product
			if( isset($_POST['fpd_product']) ) {

				$views = $_POST['fpd_product'];
				FPD_Frontend_Product::$form_views = fpd_get_option('fpd_wc_add_to_cart_product_load') == 'customized-product' ? stripslashes($views) : null;

			}
			else if( isset($_GET['cart_item_key']) ) {

				//load from cart item
				$cart = $woocommerce->cart->get_cart();
				$cart_item = $cart[$_GET['cart_item_key']];
				if($cart_item) {
					if( isset($cart_item['fpd_data']) ) {
						$views = $cart_item['fpd_data']['fpd_product'];
						FPD_Frontend_Product::$form_views = stripslashes($views);
					}
				}
				else {
					//cart item could not be found
					echo '<p><strong>';
					_e('Sorry, but the cart item could not be found!', 'radykal');
					echo '</strong></p>';
					return;
				}

			}
			else if( isset($_GET['order']) && isset($_GET['item_id']) ) {

				//load ordered product in designer
				if( function_exists('wc_get_order_item_meta') ) { //WC 3.0
					$item_meta = wc_get_order_item_meta( $_GET['item_id'], 'fpd_data' );
					$order = wc_get_order( $_GET['order'] );
				}
				else {
					$order = new WC_Order( $_GET['order'] );
					$item_meta = $order->get_item_meta( $_GET['item_id'], 'fpd_data', true );
				}

				FPD_Frontend_Product::$form_views = $item_meta['fpd_product'];
				FPD_Frontend_Product::$remove_watermark = true;

				if( $product->is_downloadable()) {

					if( $order->is_download_permitted() ): ?>
					<p>
						<a href="#" id="fpd-extern-download-pdf" class="<?php echo trim(fpd_get_option('fpd_start_customizing_css_class')); ?>">
							<?php echo FPD_Settings_Labels::get_translation( 'actions', 'download' ); ?>
						</a>
					</p>
					<?php
					else:
						FPD_Frontend_Product::$remove_watermark = false;
					endif;
				}

			}
			else if( isset($_GET['start_customizing']) && isset($_GET['fpd_product']) ) {

				$get_fpd_product_id = intval($_GET['fpd_product']);

				if( FPD_Product::exists($get_fpd_product_id) ) {
					$fancy_product = new FPD_Product( $get_fpd_product_id );
					FPD_Frontend_Product::$form_views = $fancy_product->to_JSON();
				}

			}

		}

		public function after_product_designer( $post ) {

			if( get_post_type( $post ) !== 'product' )
				return;

			global $product;
			$product_settings = new FPD_Product_Settings( $product->get_id() );

			$product_price = 0;
			if( function_exists('wc_get_price_to_display') ) //WC 3.0
				$product_price = wc_get_price_to_display( $product );
			else
				$product_price = $product->get_display_price();

			$product_price = $product_price && is_numeric($product_price) ? $product_price : 0;

			?>
			<script type="text/javascript">

				//WOOCOMMERCE JS

				var wcPrice = <?php echo $product_price; ?>,
					currencySymbol = '<span class="woocommerce-Price-currencySymbol"><?php echo get_woocommerce_currency_symbol(); ?></span>',
					decimalSeparator = "<?php echo get_option('woocommerce_price_decimal_sep'); ?>",
					thousandSeparator = "<?php echo get_option('woocommerce_price_thousand_sep'); ?>",
					numberOfDecimals = <?php echo get_option('woocommerce_price_num_decimals'); ?>,
					currencyPos = "<?php echo get_option('woocommerce_currency_pos'); ?>",
					variationSet = false;

				jQuery(document).ready(function() {

					//check when variation has been selected
					jQuery(document)
					.on('found_variation', '.variations_form', function(evt, variation) {

						if(variation.display_price !== undefined) {
							wcPrice = variation.display_price;
						}

						_setTotalPrice();

						variationSet = true;

					})
					.on('reset_data', '.variations_form', function(evt, variation) {
						variationSet = false;
					});

					//calculate initial price
					$selector.on('productCreate', function() {

						fpdPrice = fancyProductDesigner.currentPrice;
						_setTotalPrice();

						if(<?php echo FPD_Frontend_Product::$form_views === null ? 0 : 1; ?>) {
							setTimeout(_setProductImage, 5);
						}

					});

					//listen when price changes
					$selector.on('priceChange', function(evt, elementPrice, truePrice) {

						fpdPrice = truePrice;
						_setTotalPrice();

					});

					//fill custom form with values and then submit
					$cartForm.on('click', ':submit', function(evt) {

						evt.preventDefault();

						//validate min quantity input
						$quantityInput = $cartForm.find('.quantity input');
						if($quantityInput.length > 0 && parseInt($quantityInput.val()) < parseInt($quantityInput.attr('min'))) {
							return;
						}

						//check if product is created and all variations are selected
						if(!fpdProductCreated || $( this ).is('.wc-variation-selection-needed')) { return false; }

						var order = fancyProductDesigner.getOrder({
								customizationRequired: <?php echo fpd_get_option('fpd_customization_required'); ?>
							});

						//PLUS
						var bulkVariations = null;
						if(fancyProductDesigner.bulkVariations) {

							bulkVariations = fancyProductDesigner.bulkVariations.getOrderVariations();
							if(bulkVariations === false) {
								FPDUtil.showModal("<?php echo FPD_Settings_Labels::get_translation( 'plus', 'bulk_add_variations_term' ); ?>");
								order.product = false;
							}

						}

						if(order.product != false) {

							_setTotalPrice();
							jQuery('.single_add_to_cart_button').addClass('fpd-disabled');


							var tempDevicePixelRation = fabric.devicePixelRatio,
								viewOpts = fancyProductDesigner.viewInstances[0].options,
								multiplier = FPDUtil.getScalingByDimesions(viewOpts.stageWidth, viewOpts.stageHeight, <?php echo fpd_get_option('fpd_wc_cart_thumbnail_width'); ?>, <?php echo fpd_get_option('fpd_wc_cart_thumbnail_height'); ?>);

							fabric.devicePixelRatio = 1;
							fancyProductDesigner.viewInstances[0].toDataURL(function(dataURL) {

								$cartForm.find('input[name="fpd_product"]').val(JSON.stringify(order));

								if(<?php echo fpd_get_option('fpd_cart_custom_product_thumbnail'); ?>) {
									$cartForm.find('input[name="fpd_product_thumbnail"]').val(dataURL);
								}

								if(bulkVariations) {
									$cartForm.find('input[name="fpd_bulk_variations_order"]')
									.val(JSON.stringify(bulkVariations));
								}

								$cartForm.submit();

								fabric.devicePixelRatio = tempDevicePixelRation;

							}, 'transparent', {format: 'png', multiplier: multiplier})

						}

					});

					jQuery('.fpd-modal-product-designer').on('click', '.fpd-done', function(evt) {

						evt.preventDefault();

						if($selector.parents('.woocommerce').length > 0) {
							_setProductImage();
						}

						if(<?php echo intval(fpd_get_option('fpd_lightbox_add_to_cart')); ?>) {
							$cartForm.find(':submit').click();
						}

					});

					jQuery('#fpd-extern-download-pdf').click(function(evt) {

						evt.preventDefault();
						if(fpdProductCreated) {
							fancyProductDesigner.actions.downloadFile('pdf');
						}
						else {
							FPDUtil.showModal("<?php _e('The product is not created yet, try again when the product has been fully loaded into the designer', 'fpd_label'); ?>");
						}


					});

					//set total price depending from wc and fpd price
					function _setTotalPrice() {

						//PLUS: order quantity
						var totalPrice = (parseFloat(wcPrice) *  fancyProductDesigner.orderQuantity) + parseFloat(fpdPrice),
							htmlPrice;

						totalPrice = totalPrice.toFixed(numberOfDecimals);
						htmlPrice = totalPrice.toString().replace('.', decimalSeparator);
						if(thousandSeparator.length > 0) {
							htmlPrice = FPDUtil.addThousandSep(htmlPrice);
						}

						if(!$priceElem || $priceElem.length == 0) {

							if(currencyPos == 'right') {
								htmlPrice = htmlPrice + currencySymbol;
							}
							else if(currencyPos == 'right_space') {
								htmlPrice = htmlPrice + ' ' + currencySymbol;
							}
							else if(currencyPos == 'left_space') {
								htmlPrice = currencySymbol + ' ' + htmlPrice;
							}
							else {
								htmlPrice = currencySymbol + htmlPrice;
							}

							//check if variations are used
							var $priceElem;
							if($productWrapper.find('.variations_form').length > 0) {
								//check if amount contains 2 prices or sale prices. If yes different prices are used
								if($productWrapper.find('.price:first > .amount').length == 2 || $productWrapper.find('.price:first ins > .amount').length == 2) {
									//different prices
									$priceElem = $cartForm.find('.woocommerce-Price-amount:first').length > 0 ? $cartForm.find('.woocommerce-Price-amount:last') : $productWrapper.find('.single_variation .price .amount:last');

								}
								else {
									//same price
									$priceElem = $productWrapper.find('.woocommerce-Price-amount:first').length > 0 ? $productWrapper.find('.price:first .woocommerce-Price-amount:last') : $productWrapper.find('.price:first .amount:last');
								}

							}
							//no variations are used
							else {
								$priceElem = $productWrapper.find('.woocommerce-Price-amount').length > 0 ? $productWrapper.find('.price:first .woocommerce-Price-amount:last') : $productWrapper.find('.price:first .amount:last');
							}

						}

						if($priceElem && $priceElem.length > 0) {
							$priceElem.html(htmlPrice);
						}
						else {
							FPDUtil.log('No price element could be found in the document!', 'info');
						}

						$cartForm.find('input[name="fpd_product_price"]').val(totalPrice);

						if($modalPrice) {
							$modalPrice.html(htmlPrice);
						}

					};

					function _setProductImage() {

						if(jQuery('.fpd-modal-product-designer').length > 0 && <?php echo fpd_get_option('fpd_lightbox_update_product_image'); ?>) {

							fancyProductDesigner.viewInstances[0].toDataURL(function(dataURL) {

								var $firstProductImage = $productWrapper.find('.images');

								var image = new Image();
								image.onload = function() {
									$firstProductImage.find('a img')
									.attr('data-large_image_width', this.width)
									.attr('data-large_image_height', this.height);
								};
								image.src = dataURL;

								$firstProductImage
								.find('img').attr('src', dataURL).attr('srcset', dataURL) //all images (display and zoom)
								.parent('a').attr('href', dataURL)
								.children('img').attr('data-large_image', dataURL); //photoswipe image

							}, 'transparent', {format: 'png'});

						}

					};

				});

			</script>
			<?php
		}

		//the additional form fields
		public function add_product_designer_form() {

			global $post;
			$product_settings = new FPD_Product_Settings($post->ID);

			if( $product_settings->show_designer() ) {
				?>
				<input type="hidden" value="<?php echo esc_attr( $post->ID ); ?>" name="add-to-cart" />
				<input type="hidden" value="" name="fpd_product" />
				<input type="hidden" value="" name="fpd_product_price" />
				<input type="hidden" value="" name="fpd_product_thumbnail" />
				<input type="hidden" value="<?php echo isset($_GET['cart_item_key']) ? $_GET['cart_item_key'] : ''; ?>" name="fpd_remove_cart_item" />
				<?php

				do_action('fpd_product_designer_form_end', $product_settings);
			}

		}

		//add variation product id to variation attributes
		public function set_variation_meta( $attrs, $instance, $variation ) {

			$variationProduct = get_post_meta( $variation->get_id(), 'fpd_variation_product', true );
			if( $variationProduct && !empty($variationProduct) )
				$attrs['fpd_variation_product_id'] = intval($variationProduct);

			return $attrs;


		}

		public function add_variation_handler() {

			global $product;
			$product_settings = new FPD_Product_Settings( $product->get_id() );

			?>
			<script type="text/javascript">

				jQuery(document).ready(function() {

					var $customizeButton = jQuery('.fpd-variation-needed #fpd-start-customizing-button');

					jQuery('[name="variation_id"]:first').parents('form:first')
					.on('show_variation', function(evt, variation) {

						$customizeButton.css('display', 'inline-block');

						if(variation.fpd_variation_product_id) {

							var fpdProductID = variation.fpd_variation_product_id;
							if(typeof fpdProductCreated !== 'undefined' && fpdProductCreated) {

								fancyProductDesigner.toggleSpinner(true, '<?php echo FPD_Settings_Labels::get_translation( 'misc', 'loading_product' ) ?>');

								var data = {
									action: 'fpd_load_product',
									product_id: fpdProductID
								};

								jQuery.post(
									'<?php echo admin_url('admin-ajax.php'); ?>',
									data,
									function(response) {

										if(typeof response === 'object') {
											fancyProductDesigner.loadProduct(
												response,
												<?php echo $product_settings->get_option('replace_initial_elements'); ?>,
												true
											);
										}
										else {
											FPDUtil.showMessage('<?php echo FPD_Settings_Labels::get_translation( 'misc', 'product_loading_fail' ) ?>');
										}


								}, 'json');

							}
							else { //customize button activated and product designer will load in own page
								$customizeButton.attr('href', function(_, href){
								    return href+'&fpd_product='+fpdProductID
								});
							}

						}

					})
					.on('reset_data', function() {
						$customizeButton.hide();
					});

				});
			</script>
			<?php

		}

		public function add_share() {

			global $post;

			$product_settings = new FPD_Product_Settings( $post->ID );

			if( $product_settings->show_designer() ) {

				echo FPD_Share::get_share_html();

			}

		}

	}

}

new FPD_WC_Product();

?>