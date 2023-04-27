<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('FPD_Settings_WooCommerce') ) {

	class FPD_Settings_WooCommerce {

		public static function get_options() {

			return apply_filters('fpd_woocommerce_settings', array(

				'wc-product-page' => array(

					array(
						'title' 	=> __( 'Product Designer Position', 'radykal' ),
						'description' 		=> __( 'The position of the product designer in the product page.', 'radykal' ),
						'id' 		=> 'fpd_placement',
						'css' 		=> 'min-width:350px;',
						'default'	=> 'fpd-replace-image',
						'type' 		=> 'radio',
						'options'   => self::get_product_designer_positions()
					),

					array(
						'title' 	=> __( 'Customization Button Position', 'radykal' ),
						'description' 		=> __( 'When the customization button is enabled, set the position in the product page of it.', 'radykal' ),
						'id' 		=> 'fpd_start_customizing_button_position',
						'default'	=> 'under-short-desc',
						'type' 		=> 'radio',
						'options'   => array(
							'under-short-desc'	 => __( 'After Short Description', 'radykal' ),
							'before-add-to-cart-button'	 => __( 'Before Add-to-Cart Button', 'radykal' ),
							'after-add-to-cart-button'	 => __( 'After Add-to-Cart Button', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Hide Product Image', 'radykal' ),
						'description' 		=> __( 'Hide product image in the product page.', 'radykal' ),
						'id' 		=> 'fpd_hide_product_image',
						'default'	=> 'no',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Fullwidth Summary', 'radykal' ),
						'description' 		=> __( 'Forces the summary (includes i.e. product title, price, add-to-cart button) to be fullwidth.', 'radykal' ),
						'id' 		=> 'fpd_fullwidth_summary',
						'default'	=> 'no',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' 	=> __( 'Lightbox: Update Product Image', 'radykal' ),
						'description'	 	=> __( 'When "Done" button is clicked, update the WooCommerce product image.', 'radykal' ),
						'id' 		=> 'fpd_lightbox_update_product_image',
						'default'	=> 'yes',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' 	=> __( 'Lightbox: Add to cart', 'radykal' ),
						'description'	 	=> __( 'When "Done" button is clicked in the lightbox, add designed product directly into cart.', 'radykal' ),
						'id' 		=> 'fpd_lightbox_add_to_cart',
						'default'	=> 'no',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Get a quote', 'radykal' ),
						'description' 		=> __( 'No price will be displayed, the customized product will be sent to the shop owner and he makes a quote.', 'radykal' ),
						'id' 		=> 'fpd_get_quote',
						'default'	=> 'no',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Cart: Element Properties', 'radykal' ),
						'description' 		=> __( 'Show properties(Color, Font Family, Textsize) of editable elements in the cart.', 'radykal' ),
						'id' 		=> 'fpd_cart_show_element_props',
						'default'	=> 'no',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Cart: Customized Product Thumbnail', 'radykal' ),
						'description' 		=> __( 'Show the thumbnail of the customized product in the cart.', 'radykal' ),
						'id' 		=> 'fpd_cart_custom_product_thumbnail',
						'default'	=> 'yes',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Customize Button: Variation Needed', 'radykal' ),
						'description' 		=> __( 'The customize button will appear after a variation is selected.', 'radykal' ),
						'id' 		=> 'fpd_wc_customize_variation_needed',
						'default'	=> 'no',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Add-to-Cart: Load...', 'radykal' ),
						'description' 		=> __( 'Control which product is loaded after the user adds it to the cart.', 'radykal' ),
						'id' 		=> 'fpd_wc_add_to_cart_product_load',
						'default'	=> 'customized-product',
						'type' 		=> 'radio',
						'options'   => array(
							'customized-product'	 => __( 'customized product', 'radykal' ),
							'default'	 => __( 'default product', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Cart Thumbnail Width', 'radykal' ),
						'description' 		=> __( 'In pixel.', 'radykal' ),
						'id' 		=> 'fpd_wc_cart_thumbnail_width',
						'css' 		=> 'width:70px;',
						'default'	=> '100',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Cart Thumbnail Height', 'radykal' ),
						'description' 		=> __( 'In pixel.', 'radykal' ),
						'id' 		=> 'fpd_wc_cart_thumbnail_height',
						'css' 		=> 'width:70px;',
						'default'	=> '100',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Order: Element Properties', 'radykal' ),
						'description' 		=> __( 'Show properties(Color, Font Family, Textsize) of editable elements in the order details(Account and E-Mail).', 'radykal' ),
						'id' 		=> 'fpd_order_show_element_props',
						'default'	=> 'no',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),


				), //product page

				'wc-catalog-listing' => array(

					array(
						'title' 	=> __( 'Customize Button Position', 'radykal' ),
						'description' 		=> __( 'The position of the button in the catalog listing.', 'radykal' ),
						'id' 		=> 'fpd_catalog_button_position',
						'css' 		=> 'min-width:350px;',
						'default'	=> 'fpd-replace-add-to-cart',
						'type' 		=> 'radio',
						'options'   => array(
							"fpd-replace-add-to-cart" => 'Replace Add-to-Cart button',
							"fpd-item-end" => 'End of catalog item',
						)
					),

				), //catalog listing

				'wc-global-product-designer' => array(

					array(
						'title' => __( 'Enable Global Product Designer', 'radykal' ),
						'description' 		=> __( 'Enable a product designer across all WooCommerce products.', 'radykal' ),
						'id' 		=> 'fpd_global_product_designer',
						'default'	=> 'no',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' 		=> __( 'Source Type', 'radykal' ),
						'id' 			=> 'fpd_global_source_type',
						'default'		=> 'category',
						'type' 			=> 'radio',
						'options'   	=> array(
							'category'	 => __( 'Category', 'radykal' ),
							'product'	 => __( 'Product', 'radykal' ),
						),
						'relations' => array(
							'category' => array(
								'fpd_global_fancy_product_cats' => true,
								'fpd_global_fancy_products' => false,
							),
							'product' => array(
								'fpd_global_fancy_product_cats' => false,
								'fpd_global_fancy_products' => true,
							)
						)
					),

					array(
						'title' 	=> __( 'Product Categories', 'radykal' ),
						'id' 		=> 'fpd_global_fancy_product_cats',
						'default'	=> '',
						'type' 		=> 'select-sortable',
						'css'		=> 'width: 400px',
						'placeholder' => __('Add categories to selection.', 'radykal'),
						'options'   => fpd_admin_get_all_fancy_product_categories()
					),

					array(
						'title' 	=> __( 'Products', 'radykal' ),
						'id' 		=> 'fpd_global_fancy_products',
						'default'	=> '',
						'type' 		=> 'select-sortable',
						'css'		=> 'width: 400px',
						'placeholder' => __('Add products to selection.', 'radykal'),
						'options'   => fpd_admin_get_all_fancy_products()
					),

				)

			));
		}

		/**
		 * Get the available positions.
		 *
		 */
		public static function get_product_designer_positions() {

			return  array(
				'fpd-replace-image'	 => __( 'Replace Product Image', 'radykal' ),
				'fpd-under-title'	 => __( 'After Product Title', 'radykal' ),
				'fpd-after-summary'	 => __( 'After Summary', 'radykal' ),
				'fpd-custom-hook' => __( 'Custom Hook', 'radykal' ),
			);

		}

	}
}

?>