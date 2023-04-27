<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('FPD_Parameters')) {

	class FPD_Parameters {

		//deprecated
		public static function convert_parameters_to_string( $parameters, $type = '' ) {

			return FPD_Parameters::to_json($parameters, $type);

		}

		public static function to_json( $parameters, $type = '' ) {

			if( empty($parameters) ) { return '{}'; }

			$json_data = array();

			foreach($parameters as $key => $value) {

				if( fpd_not_empty($value) ) {

					switch($key) {
						case 'x': //old
							$json_data['left'] = floatval($value);
						break;
						case 'left':
							$json_data['left'] = floatval($value);
						break;
						case 'y': //old
							$json_data['y'] = floatval($value);
						break;
						case 'top':
							$json_data['top'] = floatval($value);
						break;
						case 'originX':
							$json_data['originX'] = $value;
						break;
						case 'originY':
							$json_data['originY'] = $value;
						break;
						case 'z':
							$json_data['z'] = intval($value);
						break;
						case 'colors':
							$json_data['colors'] = (is_array($value) ? implode(", ", $value) : ($value == "0" ? '' : $value));
						break;
						case 'draggable':
							$json_data['draggable'] = (bool)$value;
						break;
						case 'rotatable':
							$json_data['rotatable'] = (bool)$value;
						break;
						case 'resizable':
							$json_data['resizable'] = (bool)$value;
						break;
						case 'removable':
							$json_data['removable'] = (bool)$value;
						break;
						case 'zChangeable':
							$json_data['zChangeable'] = (bool)$value;
						break;
						case 'scale': //old
							$json_data['scaleX'] = floatval($value);
							$json_data['scaleY'] = floatval($value);
						break;
						case 'scaleX':
							$json_data['scaleX'] = floatval($value);
						break;
						case 'scaleY':
							$json_data['scaleY'] = floatval($value);
						break;
						case 'angle':
							$json_data['degree'] = intval($value);
						break;
						case 'price':
							$json_data['price'] = floatval($value);
						break;
						case 'autoCenter':
							$json_data['autoCenter'] = (bool)$value;
						break;
						case 'replace':
							$json_data['replace'] = $value;
						break;
						case 'autoSelect':
							$json_data['autoSelect'] = (bool)$value;
						break;
						case 'topped':
							$json_data['topped'] = (bool)$value;
						break;
						case 'boundingBoxMode':
							$json_data['boundingBoxMode'] = $value;
						break;
						case 'opacity':
							$json_data['opacity'] = floatval($value);
						break;
						case 'minW':
							$json_data['minW'] = floatval($value);
						break;
						case 'minH':
							$json_data['minH'] = floatval($value);
						break;
						case 'maxW':
							$json_data['maxW'] = floatval($value);
						break;
						case 'maxH':
							$json_data['maxH'] = floatval($value);
						break;
						case 'resizeToW':
							$json_data['resizeToW'] = floatval($value);
						break;
						case 'resizeToH':
							$json_data['resizeToH'] = floatval($value);
						break;
						case 'maxSize':
							$json_data['maxSize'] = floatval($value);
						break;
						case 'minDPI':
							$json_data['minDPI'] = intval($value);
						break;
						case 'currentColor'://old
							$json_data['fill'] = $value;
						break;
						case 'fill':
							$json_data['fill'] = ($value == "0" ? false : $value);
						break;
						case 'uploadZone':
							$json_data['uploadZone'] = $value;
						break;
						case 'filters':
							$json_data['filters'] = explode(',', str_replace('"', '', $value));
						break;
						case 'filter':
							$json_data['filter'] = ($value == "0" ? false : $value);
						break;
						case 'replaceInAllViews':
							$json_data['replaceInAllViews'] = (bool)$value;
						break;
						case 'lockUniScaling':
							$json_data['lockUniScaling'] = (bool)$value;
						break;
						case 'uniScalingUnlockable':
							$json_data['uniScalingUnlockable'] = (bool)$value;
						break;
						case 'colorLinkGroup':
							$json_data['colorLinkGroup'] = ($value == "0" ? false : $value);
						break;
						case 'uploadZoneScaleMode':
							$json_data['scaleMode'] = $value;
						break;
						case 'scaleMode':
							$json_data['scaleMode'] = $value;
						break;
						case 'sku':
							$json_data['sku'] = $value;
						break;
						case 'excludeFromExport':
							$json_data['excludeFromExport'] = (bool)$value;
						break;
						case 'minScaleLimit':
							$json_data['minScaleLimit'] = floatval($value);
						break;
					}

					if( $type == 'text' ) {

						switch($key) {
							case 'font': //old
								$json_data['fontFamily'] = $value;
							break;
							case 'fontFamily':
								$json_data['fontFamily'] = $value;
							break;
							case 'textSize': //old
								$json_data['fontSize'] = intval($value);
							break;
							case 'fontSize':
								$json_data['fontSize'] = intval($value);
							break;
							case 'editable':
								$json_data['editable'] = (bool)$value;
							break;
							case 'lineHeight':
								$json_data['lineHeight'] = floatval($value);
							break;
							case 'textDecoration':
								$json_data['textDecoration'] = $value;
							break;
							case 'maxLength':
								$json_data['maxLength'] = intval($value);
							break;
							case 'fontWeight':
								$json_data['fontWeight'] = $value;
							break;
							case 'fontStyle':
								$json_data['fontStyle'] = $value;
							break;
							case 'textAlign':
								$json_data['textAlign'] = $value;
							break;
							case 'curvable':
								$json_data['curvable'] = (bool)$value;
							break;
							case 'curved':
								$json_data['curved'] = (bool)$value;
							break;
							case 'curveSpacing':
								$json_data['curveSpacing'] = intval($value);
							break;
							case 'curveRadius':
								$json_data['curveRadius'] = intval($value);
							break;
							case 'curveReverse':
								$json_data['curveReverse'] = (bool)$value;
							break;
							case 'stroke':
								$json_data['stroke'] = $value;
							break;
							case 'strokeWidth':
								$json_data['strokeWidth'] = intval($value);
							break;
							case 'maxLines':
								$json_data['maxLines'] = intval($value);
							break;
							case 'textBox':
								$json_data['textBox'] = (bool)$value;
							break;
							case 'width':
								$json_data['width'] = floatval($value);
							break;
							case 'textNumberPlaceholder':

								if($value === 'text')
									$json_data['textPlaceholder'] = true;
								else if($value === 'number')
									if( isset($parameters['numberPlaceholderMin']) && isset($parameters['numberPlaceholderMax']) ) {
										$json_data['numberPlaceholder'] = array(
											$parameters['numberPlaceholderMin'], $parameters['numberPlaceholderMax']
										);
									}
									else
										$json_data['numberPlaceholder'] = true;

							break;
							case 'letterSpacing':
								$json_data['letterSpacing'] = floatval($value);
							break;
							case 'chargeAfterEditing':
								$json_data['chargeAfterEditing'] = (bool)$value;
							break;
						}
					}

				}
			}

			if( isset($parameters['uploadZone'])  ) {

				$json_data['customAdds'] = array();

				if( isset($parameters['adds_uploads']) )
					$json_data['customAdds']['uploads'] = boolval($parameters['adds_uploads']);

				if( isset($parameters['adds_texts']) )
					$json_data['customAdds']['texts'] = boolval($parameters['adds_texts']);

				if( isset($parameters['adds_designs']) )
					$json_data['customAdds']['designs'] = boolval($parameters['adds_designs']);

				if( isset($parameters['adds_facebook']) )
					$json_data['customAdds']['facebook'] = boolval($parameters['adds_facebook']);

				if( isset($parameters['adds_instagram']) )
					$json_data['customAdds']['instagram'] = boolval($parameters['adds_instagram']);

			}


			//bounding box
			if( empty($parameters['bounding_box_control']) ) {

				//use custom bounding box
				if(isset($parameters['bounding_box_x']) &&
				   isset($parameters['bounding_box_y']) &&
				   isset($parameters['bounding_box_width']) &&
				   isset($parameters['bounding_box_height'])
				   ) {

					if( fpd_not_empty($parameters['bounding_box_x']) &&
						fpd_not_empty($parameters['bounding_box_y']) &&
						fpd_not_empty($parameters['bounding_box_width']) &&
						fpd_not_empty($parameters['bounding_box_height'])
						) {

						$json_data['boundingBox'] = array(
							'x' => floatval($parameters['bounding_box_x']),
							'y' => floatval($parameters['bounding_box_y']),
							'width' => floatval($parameters['bounding_box_width']),
							'height' => floatval($parameters['bounding_box_height']),
						);

					}
				}

			}
			else if ( isset($parameters['bounding_box_by_other']) && fpd_not_empty(trim($parameters['bounding_box_by_other'])) ) {
				$json_data['boundingBox'] = $parameters['bounding_box_by_other'];
			}

			return json_encode($json_data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

		}

	}

}


?>