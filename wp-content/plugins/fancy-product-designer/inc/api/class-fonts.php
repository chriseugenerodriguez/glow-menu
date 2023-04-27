<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_Fonts') ) {

	class FPD_Fonts {

		//returns an array with all active fonts
		public static function get_enabled_fonts() {

			$all_fonts = array();

			$common_fonts = get_option( 'fpd_common_fonts' );
			if( !empty($common_fonts) ) {
				$all_fonts = explode(",", $common_fonts);
			}

			//google webfonts
			$google_webfonts = get_option( 'fpd_google_webfonts' );

			if( !empty($google_webfonts) && is_array($google_webfonts) ) {
				foreach($google_webfonts as $google_webfont) {

					$google_webfont = strpos($google_webfont, ':') === false ? $google_webfont : substr($google_webfont, 0, strpos($google_webfont, ':'));
					$google_webfont = str_replace('+', ' ', $google_webfont);

					if(!in_array($google_webfont, $all_fonts))
						$all_fonts['https://fonts.googleapis.com/css?family='.$google_webfont] = $google_webfont;
				}
			}

			//directory fonts
			$directory_fonts = get_option( 'fpd_fonts_directory' );
			if( !empty($directory_fonts) && is_array($directory_fonts) ) {
				foreach($directory_fonts as $directory_font) {
					$customFontName = str_replace('_', ' ', preg_replace("/\\.[^.\\s]{3,4}$/", "", $directory_font) );
					$all_fonts[content_url('/uploads/fpd_fonts/'.$directory_font)] = $customFontName;
				}
			}

			asort($all_fonts);

			return $all_fonts;

		}

		public static function to_json( $fonts ) {

			$json_fonts = array();
			foreach($fonts as $key => $value) {

				$font_obj = new stdClass();
				$font_obj->name = $value;

				if( gettype($key) === 'string' ) {
					$font_url = strpos($key, 'fonts.googleapis.com') == false ? $key : 'google';
					$font_obj->url = $font_url;
				}

				array_push($json_fonts, $font_obj);

			}

			return json_encode($json_fonts);

		}

	}

}

?>