<?php
/**
 * Plugin Name: Custom PSD upload
 * Plugin URI: http://amplebrain.com
 * Description: Customization for upload psd file to fancy product designer.
 * Version: 1.0.0
 * Author: tusharkapdi
 * Author URI: https://profiles.wordpress.org/tusharkapdi
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /lang
 * Text Domain: psd-to-fpd
 */
function ab_add_js_css($hook)
{
	global $wpdb;
	if( $hook == 'fancy-product-designer_page_fpd_product_builder' ) {

		wp_register_script( 'product_builder_common', plugins_url( '/js/product_builder_common.js', __FILE__ ), array(
					'fpd-product-builder',
					'jquery-ui-progressbar',
					'jquery-ui-dialog'
				), '', true );

		wp_register_script('ab-admin', plugins_url( '/js/product_builder.js', __FILE__ ), array(
					'fpd-product-builder',
					'jquery-ui-progressbar',
					'jquery-ui-dialog',
					'product_builder_common',
				), '', true );

		$request_view_id = isset($_GET['view_id']) ? $_GET['view_id'] : NULL;

		if( $request_view_id == NULL ) {
			$fancy_products = array();
			if( fpd_table_exists( FPD_PRODUCTS_TABLE ) ) {
				$fancy_product = $wpdb->get_row( "SELECT * FROM ".FPD_PRODUCTS_TABLE." ORDER BY title ASC" );
				if( !empty( $fancy_product ) ) {
					$product_id = $fancy_product->ID;
				}
			}
		} else {

			$fancy_view = new FPD_View( $request_view_id );
			$product_id = $fancy_view->get_product_id();

		}
		
		if( !empty( $product_id ) ) {

			$product_settings = new FPD_Product_Settings( $product_id );
			$custom_options_array = array(
				'customTextParameters' => fpd_convert_obj_string_to_array( $product_settings->get_custom_text_parameters_string() ),
				'customImageParameters' => fpd_convert_obj_string_to_array( $product_settings->get_image_parameters_string() ),
			);
			wp_localize_script( 'ab-admin', 'custom_options', $custom_options_array );
		}


		wp_enqueue_script( 'ab-admin' );

		wp_enqueue_script('ab-psd', plugins_url( '/js/psd.js', __FILE__ ), array('jquery'), '', true );
		wp_enqueue_style( 'ab-p2f-css', plugins_url( '/css/style.css', __FILE__ ));

	}
}

add_action( 'admin_enqueue_scripts', 'ab_add_js_css', 20 );


function ab_front_script () {
    //wp_enqueue_script('ab-jquerypp', plugins_url( '/js/jquerypp.custom.js', __FILE__ ), array('jquery'), '', true );
    //wp_enqueue_script('stratified', plugins_url( '/js/stratifiedjs/stratified.js', __FILE__ ), array('jquery'), '', true );
    //wp_enqueue_script('ab-apollo', 'http://code.onilabs.com/apollo/0.13/oni-apollo.js', array('jquery'), '', true );
}
//add_action('wp_enqueue_scripts', 'ab_front_script');

function ajax_upload_images_from_psd() {
	$_POST['upload'] = file_get_contents($_FILES['upload']['tmp_name']);
	if(!empty($_POST['upload']))
	{
		set_time_limit(0);


		//file_put_contents("txt.txt", $_POST['upload']);//exit;
		
		
		//$data = substr($_POST['upload'], strpos($_POST['upload'], ",") + 1);
		//$decodedData = base64_decode($data);

		//echo 'http://localhost/fpd/wp-content/uploads/2016/11/front.jpg';
		echo $id = ab_upload_image_from_data($_POST['upload'], $_POST['name']);
		exit;

	}
}
add_action( 'wp_ajax_load_images', 'ajax_upload_images_from_psd' );

function ab_upload_image_from_data($imgobj, $name)
{
	// Need to require these files
	if ( !function_exists('media_handle_upload') ) {
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/media.php');
	}

	$filename = sanitize_file_name($name);

	$tmp = wp_tempnam($filename.'.png');
	if ( ! $tmp )
		return new WP_Error('http_no_file', __('Could not create Temporary file.'));
	file_put_contents($tmp, $imgobj);
	//$url = "http://s.wordpress.org/style/images/wp3-logo.png";
	//$url = $imgobj;
	//
	//$tmp = $imgobj;
	//
	//$tmp = download_url( $url );
	if( is_wp_error( $tmp ) ){
		// download failed, handle error
	}
	$post_id = 0;
	$desc = $name;
	$file_array = array();

	// Set variables for storage
	// fix file filename for query strings
	//preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $url, $matches);
	//$file_array['name'] = basename($matches[0]);
	$file_array['name'] = $filename.'.png';
	$file_array['tmp_name'] = $tmp;
	
	// If error storing temporarily, unlink
	if ( is_wp_error( $tmp ) ) {
		@unlink($file_array['tmp_name']);
		$file_array['tmp_name'] = '';
	}

	// do the validation and storage stuff
	$id = media_handle_sideload( $file_array, $post_id, $desc );

	// If error storing permanently, unlink
	if ( is_wp_error($id) ) {
		@unlink($file_array['tmp_name']);
		return $id;
	}

	return $src = wp_get_attachment_url( $id );
}

function ajax_remove_images_from_media() {
	if(!empty($_POST['unlink']))
	{
		set_time_limit(0);

		echo $id = ab_get_attachment_id_from_src($_POST['unlink']);
		if($id > 0)
			wp_delete_attachment( $id );
		exit;

	}
}
add_action( 'wp_ajax_remove_images', 'ajax_remove_images_from_media' );

function ab_get_attachment_id_from_src ($image_src) {
    global $wpdb;

    $query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
    $id = $wpdb->get_var($query);
    return $id;
}

function ajax_stage_height_width() {
	
	set_time_limit(0);
	
	//$fancy_view = new Fancy_View( $_POST['view'] );
	$fancy_view = new FPD_View( $_POST['view'] );

	//$fv_options = fpd_convert_obj_string_to_array($fancy_view->get_options());
	$fv_options = $fancy_view->get_options();
	$fv_options['stage_width'] = $_POST['width'];
	$fv_options['stage_height'] = $_POST['height'];
	
	//$fancy_view->update( array('options' => json_encode( $fv_options ) ) );
	$fancy_view->update( array('options' => $fv_options ) );

	echo $_POST['width'].'px * '.$_POST['height'].'px';
	exit;
}
add_action( 'wp_ajax_stage_size', 'ajax_stage_height_width' );

//add_action('wp_footer','my_custom_js', 99);
function my_custom_js() {
	?>
	<script type="text/javascript">
	/*setTimeout(function() {f.addElement(c.type,c.source,c.title,c.parameters)}, 100)*/
	jQuery(document).ready(function($) {
		var $selector = $('#fancy-product-designer-9893');
			//fancyProductDesigner = $selector.data('instance');
		//console.log(fancyProductDesigner.currentViewInstance);
		$selector.on('viewCreate', function(viewInstance) {
			//alert('hi');
			//viewInstance.stopImmediatePropagation();
			var fancyProductDesigner = $(this).data('instance');
			$selector.one('elementAdd', function(event, fabric_object) {
				/*event.pause();
				setTimeout(function() {
					event.resume();
					console.log('sleep');
				}, 5000);*/
				var time = new Date().getTime();
				function my_recursive(count) {
					//console.log('Time: '+time);
					var new_time = new Date().getTime(),
						time_gone = new_time - time;
					//console.log('New Time: '+new_time);
					if( time_gone <= 80 ) {
						//console.log('sleep');
						my_recursive(0);
					}
					/*if( count < 1000 ) {
						//console.log('sleep');
						my_recursive(count+1);
					}*/
				}
				my_recursive(0);
				/*console.log('Time: '+time);
				console.log('sleep 2');*/
			});
			//console.log(viewInstance);
			//viewInstance.gaurav();
			/*viewInstance.setup = function() {
				alert('hi');
			}*/
		});
	});
	</script>
	<?php
}

//add_action('fpd_before_js_fpd_init', 'fpd_before_js_fpd_init_custom');
function fpd_before_js_fpd_init_custom() {
	ob_start();
}
//add_action('fpd_after_product_designer', 'fpd_after_product_designer_custom');
function fpd_after_product_designer_custom() {
	$js_content = ob_get_clean();
	$new_js = file_get_contents( plugin_dir_path( __FILE__ ).'js/front-end-custom.js' );
	$js_content = str_replace( 'fancyProductDesigner = new FancyProductDesigner($selector, pluginOptions);', $new_js, $js_content );
	echo $js_content;
}

function ab_fpd_front_scripts() {

	if( class_exists('Fancy_Product_Designer') ){

		wp_register_script( 'psdjs', plugins_url( '/js/psd.min.js', __FILE__ ), array(
					'jquery-fpd',
				), '', true );
		wp_register_script( 'product_builder_common', plugins_url( '/js/product_builder_common.js', __FILE__ ), array(
					'jquery-fpd',
					'psdjs',
					'jquery-ui-progressbar',
					'jquery-ui-dialog',
				), '', true );
		wp_register_script( 'frontend_product_builder', plugins_url( '/js/frontend_product_builder.js', __FILE__ ), array(
					'product_builder_common',
					'jquery-fpd',
					'psdjs',
					'jquery-ui-progressbar',
					'jquery-ui-dialog',
				), '', true );

		global $wp_scripts;
		//echo '<pre>';
		//print_r($wp_scripts->registered);
		if( isset( $wp_scripts->registered['jquery-fpd'] ) ) {

			$filename = plugin_dir_path( __FILE__ ).'js/front-end-custom.js';

			$diff = time()-filemtime( $filename );
			$size = filesize($filename);
			$hours = round($diff/3600);
			if( $hours > 14 || $size == 0 )
			{
				$local_test = Fancy_Product_Designer::LOCAL;
	    		$code_no = 2; //1 : FancyProductDesigner.js 	2 : FancyProductDesigner-all.min.js
	    		if( $local_test || fpd_get_option('fpd_debug_mode') )
	    			$code_no = 1;

				$js_content = file_get_contents( $wp_scripts->registered['jquery-fpd']->src );

				if($code_no == 1)
					$js_content = preg_replace("/instance\.addElement\( element\.type, element\.source, element\.title, element\.parameters\);/", "setTimeout(function() { instance.addElement( element.type, element.source, element.title, element.parameters); }, 100);", $js_content, 1);
				else
					$js_content = preg_replace("/f\.addElement\(c\.type,c\.source,c\.title,c\.parameters\)/", "setTimeout(function() {f.addElement(c.type,c.source,c.title,c.parameters)}, 100)", $js_content, 1);

				//$js_content = "window.addEventListener('load', function() {" . $js_content . "});";

				file_put_contents( plugin_dir_path( __FILE__ ).'js/front-end-custom.js', $js_content );
				$wp_scripts->registered['jquery-fpd']->src = plugins_url( '/js/front-end-custom.js', __FILE__ );
				//wp_add_inline_script( 'jquery-fpd', $js_content );
	    		//echo '<script type="text/javascript">'.$js_content.'<script>';
	    	}else{
	    		$wp_scripts->registered['jquery-fpd']->src = plugins_url( '/js/front-end-custom.js', __FILE__ );
	    	}
		}
	}
	
}
add_action( 'wp_enqueue_scripts', 'ab_fpd_front_scripts'  );
function ab_fpd_admin_scripts() {

	if(class_exists('Fancy_Product_Designer') )
	{
		global $wp_scripts;
		//echo '<pre>';
		//print_r($wp_scripts->registered);exit;
		if( isset( $wp_scripts->registered['jquery-fpd'] ) ) {

			$filename = plugin_dir_path( __FILE__ ).'js/back-end-custom.js';

			$diff = time()-filemtime( $filename );
			$size = filesize($filename);
			$hours = round($diff/3600);
			if( $hours > 14 || $size == 0 )
			{
				$local_test = Fancy_Product_Designer::LOCAL;
	    		$code_no = 2; //1 : FancyProductDesigner.js 	2 : FancyProductDesigner-all.min.js
	    		if( $local_test || fpd_get_option('fpd_debug_mode') )
	    			$code_no = 1;

				$js_content = file_get_contents( $wp_scripts->registered['jquery-fpd']->src );

				if($code_no == 1)
					$js_content = preg_replace("/instance\.addElement\( element\.type, element\.source, element\.title, element\.parameters\);/", "setTimeout(function() { instance.addElement( element.type, element.source, element.title, element.parameters); }, 100);", $js_content, 1);
				else
					$js_content = preg_replace("/f\.addElement\(c\.type,c\.source,c\.title,c\.parameters\)/", "setTimeout(function() {f.addElement(c.type,c.source,c.title,c.parameters)}, 100)", $js_content, 1);

				file_put_contents( plugin_dir_path( __FILE__ ).'js/back-end-custom.js', $js_content );
				$wp_scripts->registered['jquery-fpd']->src = plugins_url( '/js/back-end-custom.js', __FILE__ );
				//wp_add_inline_script( 'jquery-fpd', $js_content );
	    		//echo '<script type="text/javascript">'.$js_content.'<script>';
			}else{
				$wp_scripts->registered['jquery-fpd']->src = plugins_url( '/js/back-end-custom.js', __FILE__ );
			}
		}
	}
}
add_action( 'admin_enqueue_scripts', 'ab_fpd_admin_scripts'  );

add_action( 'admin_print_footer_scripts', 'ab_fpd_admin_footer_scripts'  );
function ab_fpd_admin_footer_scripts() {

	if(!empty($_GET['page']) && $_GET['page'] == 'fpd_product_builder' && class_exists('Fancy_Product_Designer') ) {
		?>
		<script type="text/javascript">
		jQuery('input[name="letterSpacing"]').addClass('fpd-allow-dots');
		</script>
		<?php
	}
}

add_action('fpd_after_product_designer', 'fancy_colorpicker', 100);
function fancy_colorpicker() {
	?>
	<script type="text/javascript">
	var glow_counter = 0;
	jQuery(document).ready(function($) {
		$selector.on('productCreate', function() {
			var $uiElementToolbar = $('.fpd-element-toolbar'),
				$colorPicker = $uiElementToolbar.find('.fpd-color-wrapper');


			var _setElementColor = function(color) {

				$uiElementToolbar.find('.fpd-current-fill').css('background', color);
				fancyProductDesigner.currentViewInstance.changeColor(fancyProductDesigner.currentViewInstance.currentElement, color);
			};

			$uiElementToolbar.find('.fpd-row > div').click(function() {
				var $this = $(this),
					element = fancyProductDesigner.currentElement;
				if( $this.data('panel') && $this.data('panel') == 'fill' ) {

					if(FPDUtil.elementHasColorSelection(element)) {

						if(element.colorLinkGroup) {
							var availableColors = fancyProductDesigner.colorLinkGroups[element.colorLinkGroup].colors;
						}
						else {
							var availableColors = element.colors;
						}

						if( element.type != 'path-group' && availableColors.length > 1) {
							$colorPicker.append('<input type="text" value="'+(element.fill ? element.fill : availableColors[0])+'" />');

							$colorPicker.children('input').spectrum({
								flat: true,
								preferredFormat: "hex",
								showInput: true,
								showInitial: true,
								showPalette: fancyProductDesigner.mainOptions.colorPickerPalette && fancyProductDesigner.mainOptions.colorPickerPalette.length > 0,
								palette: fancyProductDesigner.mainOptions.colorPickerPalette,
								show: function(color) {
									element._tempFill = color.toHexString();
								},
								move: function(color) {

									//only non-png images are chaning while dragging
									if(colorDragging === false || FPDUtil.elementIsColorizable(element) !== 'png') {
										_setElementColor(color.toHexString());
									}

								},
								change: function(color) {

									$(document).unbind("click.spectrum"); //fix, otherwise change is fired on every click
									fancyProductDesigner.currentViewInstance.setElementParameters({fill: color.toHexString()}, element);

								}
							})
							.on('dragstart.spectrum', function() {
								colorDragging = true;
							})
							.on('dragstop.spectrum', function(evt, color) {
								colorDragging = false;
								_setElementColor(color.toHexString());
							});

							$colorPicker.find('.fpd-grid .fpd-item').click( function() {
								var color = tinycolor($(this).css('backgroundColor'));
								$colorPicker.children('input').spectrum("set", color.toHexString());
							});
						}
					}

					if( element.type == 'i-text' || element.type == 'text' || element.type == 'textbox' ) {
			            // Glow Switch
			            var color = $('.single-product #theme-wrapper .product_info .tm-extra-product-options #tmcp_select_2').val();
			            var item = $('.fpd-color-picker > .fpd-color-palette > .fpd-item');

	 					//if (!$('.fpd-color-picker > .fpd-color-palette .switch-wrapper').length && color != 'None_4') {
	 					if ( !$( '.switch-wrapper', $colorPicker ).length && color != 'None_4') {
			                $('<div class="switch-wrapper"><span>Add Glow.</span><label class="switch"><input type="checkbox"><div class="slider round"></div></label></div>').prependTo('.fpd-sub-panel .fpd-color-picker');
			            	$('.fpd-element-toolbar .switch-wrapper .switch .slider').addClass(color);

			            	var slider = $('.fpd-element-toolbar .switch-wrapper .switch .slider');

			            	if (slider.hasClass('Blue_0')) {
					            $('.fpd-color-picker > .fpd-color-palette > .fpd-item:not(:nth-child(1))').remove();
					        } 
					        if (slider.hasClass('Green_2')) {
					            $('.fpd-color-picker > .fpd-color-palette > .fpd-item:not(:nth-child(3))').remove();
					        } 
					        if (slider.hasClass('Red_3')) {
					            $('.fpd-color-picker > .fpd-color-palette > .fpd-item:not(:nth-child(2))').remove();
					        } 
					        if (slider.hasClass('Orange_1')) {
					            $('.fpd-color-picker > .fpd-color-palette > .fpd-item:not(:nth-child(4))').remove();
					        }
			            }
			        }
			        
		            $('.fpd-filters > .fpd-grid, .fpd-color-picker > .fpd-color-palette').hide();
					
					$( '.fpd-sub-panel .fpd-color-picker .switch input' ).change(function() {
						var c = this.checked ? 'none' : 'block';

		            	$('.fpd-sub-panel .fpd-color-picker .sp-container.sp-flat').css('display', c);

		                if ($(this).is(':checked')) {
		                	fancyProductDesigner.currentViewInstance.setElementParameters({glow: true}, element);
		                    $('.fpd-color-picker > .fpd-color-palette > .fpd-item:first-of-type').trigger('click');
		                    glow_counter++;
		                } else {
		                	fancyProductDesigner.currentViewInstance.setElementParameters({glow: false}, element);
		                	_setElementColor( element.originParams.fill );
		                	$colorPicker.children('input').spectrum("set", element.originParams.fill);
		                	glow_counter--;
		                }
					});

					if( typeof element.glow !== 'undefined' && element.glow == true ) {
						$( '.fpd-sub-panel .fpd-color-picker .switch input' ).attr( "checked", "checked" );
						$('.fpd-sub-panel .fpd-color-picker .sp-container.sp-flat').css('display', 'none');
					}

				}
			});
		});
	});
	</script>
	<?php
}

add_action('fpd_after_product_designer', 'fancy_preview_before_cart', 100);
function fancy_preview_before_cart() {
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		/*$('body').on('click', '#preview_customize', function() {
			//$('.fpd-modal-close').trigger('click');
			$('.product.container-fluid').show();
			$('.single-product #theme-wrapper .product_info .options').show();
            $('.single-product #theme-wrapper .product_info .pricing_info').hide();
            $('.preview_container').remove();
		});*/
		$('body').on('click', '#preview_add_to_cart', function() {
			var glow_color = $('.single-product #theme-wrapper .product_info .tm-extra-product-options #tmcp_select_2').val();
			if( glow_color.indexOf('None') < 0 && glow_counter == 0 ) {
				//alert('You have selected glow color but not selected glow for any of the text layer. Please customize and add glow to required text layers.');
				$('.preview_container:first .woocommerce-error').remove();
				$('.preview_container:first').prepend('<div class="container woocommerce-error">You have selected glow color but not selected glow for any of the text layer. Please customize and add glow to required text layers.</div>');
				var glow_error_top = $('.preview_container:first .glow-error').offset().top - 10;
				$('body').animate( {scrollTop : glow_error_top }, 400, function() {
					$('.preview_container:first .glow-error').animate( { borderWidth : '4px' }, 400, function() {
						$(this).animate( { borderWidth : '2px' }, 400 );
					});
				});
			} else if( $('#approve_design').prop('checked') == true ) {
				//$('.product.container-fluid').show();
				$('.single_add_to_cart_button').click();
				/*$('#preview_btn').off('click');
				$('#preview_btn').text('Adding to cart...');*/
				//$('.fpd-modal-close').trigger('click');
			} else {
				alert('Please review and approve your design to continue. Thanks!');
			}
		});
		$('body').on('click', '.preview_edit', function(event) {
			event.preventDefault();
			var preview_edit_index = $('.preview_edit').index(this);
			$('.product.container-fluid').show();
			$('.fpd-product-designer-wrapper .sides').find('span:eq('+preview_edit_index+')').trigger('click');
			//$('.fpd-modal-close').trigger('click');
			$('.single-product #theme-wrapper .product_info .options').show();
            $('.single-product #theme-wrapper .product_info .pricing_info').hide();
            $('.preview_container').remove();
		});
		$('body').on('click', '.preview_larger', function(event) {
			event.preventDefault();
			FPDUtil.showModal('<div class="container-fluid"><div class="col-lg-12 text-center"><img src="'+$(this).attr('href')+'"></div></div>', true);
		});

		$selector.on('productCreate', function() {

			var color_codes = $.map(fancyProductDesigner.mainOptions.customTextParameters.colors.split(','), $.trim);
			
			var allElements = fancyProductDesigner.getElements();
			for( var j = 0; j < allElements.length; j++ ) {
				var viewElements = allElements[j];
				for(var i = 0; i < viewElements.length; i++ ) {
					var element = viewElements[i];
					if( element.type == 'i-text' || element.type == 'text' || element.type == 'textbox' ) {
						var element_fill = element.fill;
						element_fill = element_fill.toUpperCase();
						if( $.inArray( element_fill, color_codes ) != -1 ) {
							fancyProductDesigner.viewInstances[j].setElementParameters({glow: true}, element);
							glow_counter++;
						}
					}
				}
			}

			$('.single_add_to_cart_button').hide();
			var $preview_btn = $('<button type="button" id="preview_btn" class="button alt">Approve Design</button>');
			$preview_btn.insertAfter('.single_add_to_cart_button');
			$preview_btn.on('click', function() {
				var modal_html = '<div class="littlepaddingtop preview_container"><div class="container-fluid"><div class="col-lg-9">';
				fancyProductDesigner.getViewsDataURL(function(dataURLs) {
					jQuery.each(dataURLs, function(index, data_image) {
						var image = new Image(), side = '';
						image.src = data_image;
						if( index == 0 ) {
							side = 'Front side';
						} else {
							side = 'Back side';
						}
						modal_html += '<div class="col-lg-6"><div class="row side"><div class="col-lg-6 text-left"><a href="'+image.src+'" target="_blank" class="preview_larger">Preview</a> <span>|</span> <a href="#" class="preview_edit">Edit</a></div><div class="col-lg-6 text-right">'+side+'</div></div><img src="'+image.src+'" /></div>';
					});
				});
				//modal_html += '<div class="col-lg-12 text-center"><button type="button" id="preview_customize" class="button alt">Customize</button> <button type="button" id="preview_add_to_cart" class="button alt">Confirm</button></div>';
				modal_html += '</div>';
				
				modal_html += '<div class="col-lg-3 sidebar-content-review littlepaddingtop"><h3>Things to check for:</h3><ol><li>Information is accurate and spelled correctly.</li><li>Text is legible and contrasts against background.</li><li>Images are clear and don’t appear blurry.</li><li>Nothing is overlapping or too close to bleed.</li></ol>';
				modal_html += '<p><label><input type="checkbox" id="approve_design" /> <strong>I have reviewed and approve my design</strong>.</label></p><p><button type="button" id="preview_add_to_cart" class="button alt">Next</button></p></div>';

				modal_html += '</div>';
				$('#theme-wrapper').append(modal_html);
				$('.product.container-fluid').fadeOut(500);
				
				setTimeout(function(){
					$('.preview_container').addClass('active');
				}, 2500);
				$('.preview_container .sidebar-content-review').theiaStickySidebar({
		            additionalMarginTop: 30
		        })
			});
		});
	});
	</script>
	<?php		
}

add_action('fpd_after_product_designer', 'add_required_custom_footer_action', 100);
function add_required_custom_footer_action( $post ) {
	$product_settings = new FPD_Product_Settings( $post->ID );
	$custom_options_array = array(
		'customTextParameters' => fpd_convert_obj_string_to_array( $product_settings->get_custom_text_parameters_string() ),
		'customImageParameters' => fpd_convert_obj_string_to_array( $product_settings->get_image_parameters_string() ),
	);
	wp_localize_script( 'frontend_product_builder', 'custom_options', $custom_options_array );
	add_action( 'wp_footer', 'psd_to_fpd_footer_handler' );
}
function psd_to_fpd_footer_handler() {
	wp_enqueue_script( 'frontend_product_builder' );
}