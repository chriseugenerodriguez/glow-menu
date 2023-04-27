<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('FPD_Settings_Default_Element_Options') ) {

	class FPD_Settings_Default_Element_Options {

		public static function get_options() {

			return apply_filters('fpd_default_element_options_settings', array(

				'default-image-options' => array(

					array(
						'title' => __( 'X-Position', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_x',
						'css' 		=> 'width:70px;',
						'default'	=> '0',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Y-Position', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_y',
						'css' 		=> 'width:70px;',
						'default'	=> '0',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Z-Position', 'radykal' ),
						'description' 		=> __( '-1 means that the element will be added at the top. Any value higher than that will add the element to that z-position.', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_z',
						'css' 		=> 'width:70px;',
						'default'	=> '-1',
						'type' 		=> 'text',
						'custom_attributes' => array(
							'min' 	=> -1,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Minimum Scale Limit', 'radykal' ),
						'description' 		=> __( 'The minimum allowed scale value.', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_minScaleLimit',
						'css' 		=> 'width:70px;',
						'default'	=> '0.01',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0.01,
							'step' 	=> 0.01
						)
					),

					array(
						'title' => __( 'Colors', 'radykal' ),
						'description' 		=> __( 'Enter hex color(s) separated by comma.', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_colors',
						'css' 		=> 'width:300px;',
						'default'	=> '',
						'type' 		=> 'text'
					),

					array(
						'title' => __( 'Color Link Group', 'radykal' ),
						'description' 		=> __( 'You can set color links between elements.', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_colorLinkGroup',
						'css' 		=> 'width:300px;',
						'default'	=> '',
						'type' 		=> 'text'
					),

					array(
						'title' => __( 'Price', 'radykal' ),
						'description' 		=> __( 'Enter the additional price for a design element. Use always a dot as decimal separator!', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_price',
						'css' 		=> 'width:70px;',
						'default'	=> '0',
						'type' 		=> 'text',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Replace', 'radykal' ),
						'description' 		=> __( 'Elements with the same replace name will replace each other.', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_replace',
						'css' 		=> 'width:150px;',
						'default'	=> '',
						'type' 		=> 'text'
					),

					array(
						'title' => __( 'Replace In All Views', 'radykal' ),
						'description' 		=> __( 'Replace image elements with the same replace value in all views?', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_replaceInAllViews',
						'default'	=> 'no',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Auto-Center', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_autoCenter',
						'default'	=> 'yes',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Draggable', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_draggable',
						'default'	=> 'yes',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Rotatable', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_rotatable',
						'default'	=> 'yes',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Resizable', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_resizable',
						'default'	=> 'yes',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Z-Changeable', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_zChangeable',
						'default'	=> 'yes',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Auto-Select', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_autoSelect',
						'default'	=> 'no',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Stay On Top', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_topped',
						'default'	=> 'no',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Uni-Scaling Unlockable', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_uniScalingUnlockable',
						'default'	=> 'no',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Use another element as bounding box?', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_bounding_box_control',
						'class'		=> 'fpd-bounding-box-control',
						'default'	=> 'no',
						'type' 		=> 'checkbox',
						'relations' =>  array(
							'fpd_designs_parameter_bounding_box_by_other' => true,
							'fpd_designs_parameter_bounding_box_x' => false,
							'fpd_designs_parameter_bounding_box_y' => false,
							'fpd_designs_parameter_bounding_box_width' => false,
							'fpd_designs_parameter_bounding_box_height' => false,
						)

					),

					array(
						'title' 	=> __( 'Bounding Box Target', 'radykal' ),
						'description' 		=> __( 'Enter the title of another element that should be used as bounding box for design elements.', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_bounding_box_by_other',
						'css' 		=> 'width:150px;',
						'default'	=> '',
						'type' 		=> 'text'
					),

					array(
						'title'		=> __( 'Bounding Box X-Position', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_bounding_box_x',
						'css' 		=> 'width:70px;',
						'default'	=> '',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Bounding Box Y-Position', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_bounding_box_y',
						'css' 		=> 'width:70px;',
						'default'	=> '',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Bounding Box Width', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_bounding_box_width',
						'css' 		=> 'width:70px;',
						'default'	=> '',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Bounding Box Height', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_bounding_box_height',
						'css' 		=> 'width:70px;',
						'default'	=> '',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Bounding Box Mode', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_boundingBoxMode',
						'default'	=> 'inside',
						'type' 		=> 'select',
						'options'	=>  array(
							'inside' => __('Inside', 'radyal'),
							'clipping' => __('Clipping', 'radyal'),
							'limitModify' => __('Limit Modification', 'radyal'),
							'none' => __('None', 'radyal'),
						)
					),

					array(
						'title' 	=> __( 'Filters', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_filters',
						'css' 		=> 	'width: 200px;',
						'default'	=> '',
						'class'		=> 'radykal-select2',
						'type' 		=> 'multiselect',
						'options'	=>  array(
							'grayscale' => __('Grayscale', 'radyal'),
							'sepia' => __('Sepia', 'radyal'),
							'sepia2' => __('Sepia 2', 'radyal'),
						)

					),
					array(
						'title' 	=> __( 'Patterns', 'radykal' ),
						'description' 		=> __( 'Upload PNG or JPEG into wp-content/uploads/fpd_patterns_svg.', 'radykal' ),
						'id' 		=> 'fpd_designs_parameter_patterns',
						'css' 		=> 	'width: 100%;',
						'default'	=> '',
						'class'		=> 'radykal-select2',
						'type' 		=> 'multiselect',
						'options'	=> self::get_pattern_urls('svg')

					),

				), //default image options

				'default-custom-image-options' => array(

					array(
						'title' 	=> __( 'Minimum Width', 'radykal' ),
						'description' 		=> __( 'The minimum image width for uploaded designs from the customers.', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_minW',
						'css' 		=> 'width:70px;',
						'default'	=> '100',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Minimum Height', 'radykal' ),
						'description' 		=> __( 'The minimum image height for uploaded designs from the customers.', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_minH',
						'css' 		=> 'width:70px;',
						'default'	=> '100',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Maximum Width', 'radykal' ),
						'description' 		=> __( 'The maximum image width for uploaded designs from the customers.', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_maxW',
						'css' 		=> 'width:70px;',
						'default'	=> '1000',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Maximum Height', 'radykal' ),
						'description' 		=> __( 'The maximum image height for uploaded designs from the customers.', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_maxH',
						'css' 		=> 'width:70px;',
						'default'	=> '1000',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Resize To Width', 'radykal' ),
						'description' 		=> __( 'Resize the uploaded image to this width, when width is larger than height.', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_resizeToW',
						'css' 		=> 'width:70px;',
						'default'	=> '0',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Resize To Height', 'radykal' ),
						'description' 		=> __( 'Resize the uploaded image to this height, when height is larger than width.', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_resizeToH',
						'css' 		=> 'width:70px;',
						'default'	=> '0',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Maximum Image Size (MB)', 'radykal' ),
						'description' 		=> __( 'The maximum image size in Megabytes.', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_maxSize',
						'css' 		=> 'width:70px;',
						'default'	=> '10',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Minimum JPEG DPI', 'radykal' ),
						'description' 		=> __( 'The minimum allowed DPI for JPEG images.', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_minDPI',
						'css' 		=> 'width:70px;',
						'default'	=> '72',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Minimum Scale Limit', 'radykal' ),
						'description' 		=> __( 'The minimum allowed scale value.', 'radykal' ),
						'id' 		=> 'fpd_uploaded_designs_parameter_minScaleLimit',
						'css' 		=> 'width:70px;',
						'default'	=> '0.01',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0.01,
							'step' 	=> 0.01
						)
					),

				), //default custom images

				'default-custom-text-options' => array(

					array(
						'title' => __( 'X-Position', 'radykal' ),
						'description' 		=> __( 'The x-position of the custom text element.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_x',
						'css' 		=> 'width:70px;',
						'default'	=> '0',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Y-Position', 'radykal' ),
						'description' 		=> __( 'The y-position of the custom text element.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_y',
						'css' 		=> 'width:70px;',
						'default'	=> '0',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Z-Position', 'radykal' ),
						'description' 		=> __( '-1 means that the element will be added at the top. Any value higher than that will add the element to that z-position.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_z',
						'css' 		=> 'width:70px;',
						'default'	=> '-1',
						'type' 		=> 'text',
						'custom_attributes' => array(
							'min' 	=> -1,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Default Color', 'radykal' ),
						'description' 		=> __( 'The default color for custom added text elements.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_fill',
						'css' 		=> 'width:70px;',
						'default'	=> '',
						'type' 		=> 'text'
					),

					array(
						'title' => __( 'Colors', 'radykal' ),
						'description' 		=> __( 'Enter hex color(s) separated by comma.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_colors',
						'css' 		=> 'width:300px;',
						'default'	=> '#000000',
						'type' 		=> 'text'
					),

					array(
						'title' => __( 'Color Link Group', 'radykal' ),
						'description' 		=> __( 'You can set color links between elements.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_colorLinkGroup',
						'css' 		=> 'width:300px;',
						'default'	=> '',
						'type' 		=> 'text'
					),

					array(
						'title' => __( 'Price', 'radykal' ),
						'description' 		=> __( 'Enter the additional price for a text element. Always use a dot as decimal separator!', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_price',
						'css' 		=> 'width:70px;',
						'default'	=> '0',
						'type' 		=> 'text',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Replace', 'radykal' ),
						'description' 		=> __( 'Elements with the same replace name will replace each other.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_replace',
						'css' 		=> 'width:150px;',
						'default'	=> '',
						'type' 		=> 'text'
					),

					array(
						'title' => __( 'Replace In All Views', 'radykal' ),
						'description' 		=> __( 'Replace text elements with the same replace value in all views?', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_replaceInAllViews',
						'default'	=> 'no',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Auto-Center', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_autoCenter',
						'default'	=> 'yes',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Draggable', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_draggable',
						'default'	=> 'yes',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Rotatable', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_rotatable',
						'default'	=> 'yes',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Resizable', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_resizable',
						'default'	=> 'yes',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Z-Changeable', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_zChangeable',
						'default'	=> 'yes',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Auto-Select', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_autoSelect',
						'default'	=> 'no',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Stay On Top', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_topped',
						'default'	=> 'no',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					/*array(
						'title' => __( 'Patternable', 'radykal' ),
						'description' 		=> __( 'Let the customer choose a pattern?', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_patternable',
						'default'	=> 'no',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),*/

					array(
						'title' => __( 'Curvable', 'radykal' ),
						'description' 		=> __( 'Let the customer make the text curved?', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_curvable',
						'default'	=> 'yes',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Curve Spacing', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_curveSpacing',
						'default'	=> 10,
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 1,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Curve Radius', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_curveRadius',
						'default'	=> 80,
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 1,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Curve Reverse', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_curveReverse',
						'default'	=> 'no',
						'type' 		=> 'radio',
						'options'   => array(
							'yes'	 => __( 'Yes', 'radykal' ),
							'no'	 => __( 'No', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Use another element as bounding box?', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_bounding_box_control',
						'class'		=> 'fpd-bounding-box-control',
						'default'	=> 'no',
						'type' 		=> 'checkbox',
						'relations' =>  array(
							'fpd_custom_texts_parameter_bounding_box_by_other' => true,
							'fpd_custom_texts_parameter_bounding_box_x' => false,
							'fpd_custom_texts_parameter_bounding_box_y' => false,
							'fpd_custom_texts_parameter_bounding_box_width' => false,
							'fpd_custom_texts_parameter_bounding_box_height' => false,
						)
					),

					array(
						'title' 	=> __( 'Bounding Box Target', 'radykal' ),
						'description' 		=> __( 'Enter the title of another element that should be used as bounding box for design elements.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_bounding_box_by_other',
						'css' 		=> 'width:150px;',
						'default'	=> '',
						'type' 		=> 'text'
					),

					array(
						'title'		=> __( 'Bounding Box X-Position', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_bounding_box_x',
						'css' 		=> 'width:70px;',
						'default'	=> '',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Bounding Box Y-Position', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_bounding_box_y',
						'css' 		=> 'width:70px;',
						'default'	=> '',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Bounding Box Width', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_bounding_box_width',
						'css' 		=> 'width:70px;',
						'default'	=> '',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' 	=> __( 'Bounding Box Height', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_bounding_box_height',
						'css' 		=> 'width:70px;',
						'default'	=> '',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Bounding Box Mode', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_boundingBoxMode',
						'default'	=> 'inside',
						'type' 		=> 'select',
						'options'	=>  array(
							'inside' => __('Inside', 'radyal'),
							'clipping' => __('Clipping', 'radyal'),
							'limitModify' => __('Limit Modification', 'radyal'),
							'none' => __('None', 'radyal'),
						)
					),

					array(
						'title' => __( 'Default Text Size', 'radykal' ),
						'description' 		=> __( 'The default text size for all text elements.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_textSize',
						'css' 		=> 'width:70px;',
						'default'	=> '18',
						'type' 		=> 'number',
						'custom_attributes' => array(
							'min' 	=> 0,
							'step' 	=> 1
						)
					),

					array(
						'title' => __( 'Default Font', 'radykal' ),
						'description' 		=> __( 'Enter the default font. If you leave it empty, the first font from the fonts dropdown will be used.', 'radykal' ),
						'id' 		=> 'fpd_font',
						'css' 		=> 'width:300px;',
						'default'	=> '',
						'type' 		=> 'text'
					),

					array(
						'title' 	=> __( 'Maximum Characters', 'radykal' ),
						'description' 		=> __( 'You can limit the number of characters. 0 means unlimited characters.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_maxLength',
						'css' 		=> 'width:70px;',
						'default'	=> 0,
						'type' 		=> 'number'
					),

					array(
						'title' 	=> __( 'Maximum Lines', 'radykal' ),
						'description' 		=> __( 'You can limit the number of lines. 0 means unlimited lines.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_maxLines',
						'css' 		=> 'width:70px;',
						'default'	=> 0,
						'type' 		=> 'number'
					),

					array(
						'title' 	=> __( 'Alignment', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_textAlign',
						'css' 		=> 'min-width:350px;',
						'default'	=> 'left',
						'type' 		=> 'select',
						'class'		=> 'chosen_select',
						'options'   => array(
							'left' => __( 'Left', 'radykal' ),
							'center' => __( 'Center', 'radykal' ),
							'right' => __( 'Right', 'radykal' )
						)
					),
					array(
						'title' 	=> __( 'Patterns', 'radykal' ),
						'description' 		=> __( 'Upload PNG or JPEG into wp-content/uploads/fpd_patterns_text.', 'radykal' ),
						'id' 		=> 'fpd_custom_texts_parameter_patterns',
						'css' 		=> 	'width: 100%;',
						'default'	=> '',
						'class'		=> 'radykal-select2',
						'type' 		=> 'multiselect',
						'options'	=> self::get_pattern_urls()

					),
				), //default text options

				'default-common-options' => array(

					array(
						'title' => __( 'Origin-X Point', 'radykal' ),
						'id' 		=> 'fpd_common_parameter_originX',
						'css' 		=> 'min-width:350px;',
						'default'	=> 'center',
						'type' 		=> 'select',
						'class'		=> 'chosen_select',
						'options'   => array(
							'center'	 => __( 'Center', 'radykal' ),
							'left' => __( 'Left', 'radykal' ),
						)
					),

					array(
						'title' => __( 'Origin-Y Point', 'radykal' ),
						'id' 		=> 'fpd_common_parameter_originY',
						'css' 		=> 'min-width:350px;',
						'default'	=> 'center',
						'type' 		=> 'select',
						'class'		=> 'chosen_select',
						'options'   => array(
							'center'	 => __( 'Center', 'radykal' ),
							'top' => __( 'Top', 'radykal' ),
						)
					),

				), //default common options


			));
		}

		public static function get_bounding_box_modi() {

			return array(
				'inside'	 	=> __( 'Inside', 'radykal' ),
				'clipping'	 	=> __( 'Clipping', 'radykal' ),
				'limitModify'	=> __( 'Limit Modification', 'radykal' ),
				'none'	 		=> __( 'None', 'radykal' ),
			);

		}

		public static function get_pattern_urls($type='text') {

			$urls = array();

			$path = FPD_WP_CONTENT_DIR . '/uploads/fpd_patterns_'.$type.'/';

			if( file_exists($path) ) {

			  	$folder = opendir($path);

				$pic_types = array("jpg", "jpeg", "png");

				while ($file = readdir ($folder)) {

				  if(in_array(substr(strtolower($file), strrpos($file,".") + 1),$pic_types)) {
					  $urls[content_url('/uploads/fpd_patterns_'.$type.'/'.$file, FPD_PLUGIN_ROOT_PHP )] = $file;
				  }
				}

				closedir($folder);
			}

			return $urls;

		}

	}

}