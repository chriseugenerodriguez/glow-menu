<?php
/**
 * class-search-live-shortcodes.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package search-live
 * @since 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !function_exists( 'search_live' ) ) {
	/**
	 * Renders a search form which is returned as HTML and loads
	 * required resources.
	 * 
	 * @param array $atts desired search facility options
	 * @return string form HTML
	 */
	function search_live( $atts = array() ) {
		return Search_Live_Shortcodes::search_live( $atts );
	}
}

/**
 * Shortcode definitions and renderers.
 */
class Search_Live_Shortcodes {

	/**
	 * Adds shortcodes.
	 */
	public static function init() {
		add_shortcode( 'search_live', array( __CLASS__, 'search_live' ) );
	}

	/**
	 * Enqueues scripts and styles needed to render our search facility.
	 */
	public static function load_resources() {
		$options = Search_Live::get_options();
		$enable_css = isset( $options[Search_Live::ENABLE_CSS] ) ? $options[Search_Live::ENABLE_CSS] : Search_Live::ENABLE_CSS_DEFAULT;
		wp_enqueue_script( 'typewatch' );
		wp_enqueue_script( 'search-live' );
		if ( $enable_css ) {
			wp_enqueue_style( 'search-live' );
		}
	}

	/**
	 * Shortcode handler, renders a search form.
	 * 
	 * Enqueues required scripts and styles.
	 * 
	 * @param array $atts
	 * @param array $content not used
	 * @return string form HTML
	 */
	public static function search_live( $atts = array(), $content = '' ) {

		self::load_resources();

		$atts = shortcode_atts(
			array(
				'order'               => null,
				'order_by'            => null,
				'title'               => null,
				'excerpt'             => null,
				'content'             => null,
				'limit'               => null,
				'thumbnails'          => null,
				'show_description'    => null,
				'placeholder'         => _x( 'Search &hellip;', 'placeholder', 'search-live' ),
				'field_title'         => _x( 'Search for:', 'label', 'search-live' ),
				'no_results'          => '',
				'blinker_timeout'     => null,
				'delay'               => Search_Live::DEFAULT_DELAY,
				'characters'          => Search_Live::DEFAULT_CHARACTERS,
				'dynamic_focus'       => 'yes',
				'floating'            => 'yes',
				'inhibit_enter'       => 'no',
				'submit_button'       => 'no',
				'submit_button_label' => _x( 'Search', 'submit button', 'search-live' ),
				'navigable'           => 'yes',
				'auto_adjust'         => 'yes',
				'wpml'                => 'no'
			),
			$atts
		);

		$url_params = array();
		foreach( $atts as $key => $value ) {
			if ( $value !== null ) {
				$add = true;
				$value = strip_tags( trim( $value ) );
				switch( $key ) {
					case 'order' :
					case 'order_by' :
						break;
					case 'title' :
					case 'excerpt' :
					case 'content' :
					case 'thumbnails' :
						$value = strtolower( $value );
						$value = $value == 'true' || $value == 'yes' || $value == '1';
						break;
					case 'limit' :
						$value = intval( $value );
						break;
					default :
						$add = false;
				}
				if ( $add ) {
					$url_params[$key] = urlencode( $value );
				}
			}
		}

		$params = array();
		foreach( $atts as $key => $value ) {
			if ( $value !== null ) {
				$add = true;
				$value = strip_tags( trim( $value ) );
				switch( $key ) {
					case 'dynamic_focus' :
					case 'floating' :
					case 'inhibit_enter' :
					case 'submit_button' :
					case 'navigable' :
					case 'auto_adjust' :
					case 'wpml' :
					case 'thumbnails' :
					case 'show_description' :
						$value = strtolower( $value );
						$value = $value == 'true' || $value == 'yes' || $value == '1';
						break;
					case 'delay' :
					case 'characters' :
					case 'blinker_timeout' :
						$value = intval( $value );
						break;
					case 'no_results' :
					case 'placeholder' :
					case 'field_title' :
					case 'submit_button_label' :
						$value = trim( $value );
						break;
					default :
						$add = false;
				}
				if ( $add ) {
					$params[$key] = $value;
				}
			}
		}

		$floating = $params['floating'] ? 'floating' : '';

		if ( $params['delay'] < Search_Live::MIN_DELAY ) {
			$params['delay'] = Search_Live::MIN_DELAY;
		}
		if ( $params['characters'] < Search_Live::MIN_CHARACTERS ) {
			$params['characters'] = Search_Live::MIN_CHARACTERS;
		}
		$params['placeholder'] = apply_filters( 'search_live_placeholder', $params['placeholder'] );
		$params['field_title'] = apply_filters( 'search_live_field_title', $params['field_title'] );
		$params['no_results']  = apply_filters( 'search_live_no_results', $params['no_results'] );

		$output = '';

		$search_live = true;

		$n          = rand();
		$search_id  = 'search-live-' . $n;
		$form_id    = 'search-live-form-' . $n;
		$field_id   = 'search-live-field-' . $n;
		$results_id = 'search-live-results-' .$n;

		$output .= self::inline_styles();

		$output .= sprintf(
			'<div id="%s" class="search-live %s">',
			esc_attr( $search_id ),
			esc_attr( $floating )
		);

		$output .= '<div class="search-live-form">';
		$output .= sprintf( '<form role="search" id="%s" class="search-live-form" action="%s" method="get">', esc_attr( $form_id ), esc_url( home_url( '/' ) ) );
		$output .= '<div>';
		$output .= '<span class="screen-reader-text">' . _x( 'Search for:', 'label', 'search-live' ) . '</span>';
		$output .= sprintf(
			'<input id="%s" name="s" type="text" class="search-live-field" placeholder="%s" autocomplete="off" title="%s" value="%s" />',
			esc_attr( $field_id ),
			esc_attr( $params['placeholder'] ),
			esc_attr( $params['field_title'] ),
			get_search_query() // this comes in escaped through esc_attr()
		);

		if ( isset( $url_params['limit'] ) ) { 
			$output .= sprintf( '<input type="hidden" name="limit" value="%d"/>', intval( $url_params['limit'] ) );
		}
		if ( $params['wpml'] && defined( 'ICL_LANGUAGE_CODE' ) ) {
			$output .= sprintf( '<input type="hidden" name="lang" value="%s"/>', ICL_LANGUAGE_CODE );
		}
		$output .= '<input type="hidden" name="ixsl" value="1"/>';
		if ( $params['submit_button'] ) {
			$output .= ' ';
			$output .= sprintf( '<button type="submit">%s</button>', esc_html( $params['submit_button_label'] ) );
		} else {
			$output .= '<noscript>';
			$output .= sprintf( '<button type="submit">%s</button>', esc_html( $params['submit_button_label'] ) );
			$output .= '</noscript>';
		}

		$output .= '</div>';
		$output .= '</form>';
		$output .= '</div>'; // .search-live-form

		$output .= sprintf( '<div id="%s" class="search-live-results">', $results_id );
		$output .= '</div>'; // .search-live-results

		$output .= '</div>'; // .search-live

		$js_args = array();
		$js_args[] = sprintf( 'no_results:"%s"', esc_js( $params['no_results'] ) );
		$js_args[] = $params['dynamic_focus'] ? 'dynamic_focus:true' : 'dynamic_focus:false';
		if ( isset( $params['blinker_timeout'] ) ) {
			$blinker_timeout =  max( array( 0, intval( $params['blinker_timeout'] ) ) );
			$js_args[] = 'blinkerTimeout:' . $blinker_timeout;
		}
		if ( $params['wpml'] && defined( 'ICL_LANGUAGE_CODE' ) ) {
			$js_args[] = sprintf( 'lang:"%s"', ICL_LANGUAGE_CODE );
		}
		if ( isset( $params['thumbnails'] ) ) {
			$js_args[] = 'thumbnails:' . ( $params['thumbnails'] ? 'true' : 'false' );
		}
		if ( isset( $params['show_description'] ) ) {
			$js_args[] = 'show_description:' . ( $params['show_description'] ? 'true' : 'false' );
		}
		$js_args = '{' . implode( ',', $js_args ) . '}';

		$post_target_url = add_query_arg( $url_params , admin_url( 'admin-ajax.php' ) ); 

		$output .= '<script type="text/javascript">';
		$output .= 'if ( typeof jQuery !== "undefined" ) {';
		$output .= 'jQuery(document).ready(function(){';
		$output .= sprintf(
			'jQuery("#%s").typeWatch( {
				callback: function (value) { ixsl.searchLive(\'%s\', \'%s\', \'%s\', \'%s\', value, %s); },
				wait: %d,
				highlight: true,
				captureLength: %d
			} );',
			esc_attr( $field_id ), // jQuery selector for the input field
			esc_attr( $field_id ), // jQuery selector for the input field passed to searchLive()
			esc_attr( $search_id ), // container selector
			esc_attr( $search_id . ' div.search-live-results' ), // results container selector
			$post_target_url,
			$js_args,
			$params['delay'],
			$params['characters']
		);
		if ( $params['inhibit_enter'] ) {
			$output .= sprintf( 'ixsl.inhibitEnter("%s");', $field_id );
		}
		if ( $params['navigable'] ) {
			$output .= sprintf( 'ixsl.navigate("%s","%s");', $field_id, $results_id );
		}
		if ( $params['dynamic_focus'] ) {
			$output .= sprintf( 'ixsl.dynamicFocus("%s","%s");', $search_id, $results_id );
		}
		if ( $params['auto_adjust'] ) {
			$output .= sprintf( 'ixsl.autoAdjust("%s","%s");', $field_id, $results_id );
		}
		$output .= '});'; // ready
		$output .= '}'; // if
		$output .= '</script>';

		return $output;
	}

	/**
	 * Renders search inline styles if defined (once only).
	 * 
	 * @return string
	 */
	public static function inline_styles() {
		global $search_live_inline_styles;
		$output = '';
		if ( !isset( $search_live_inline_styles ) ) {
			$options = Search_Live::get_options();
			$enable_inline_css = isset( $options[Search_Live::ENABLE_INLINE_CSS] ) ? $options[Search_Live::ENABLE_INLINE_CSS] : Search_Live::ENABLE_INLINE_CSS_DEFAULT;
			$inline_css        = isset( $options[Search_Live::INLINE_CSS] ) ? $options[Search_Live::INLINE_CSS] : Search_Live::INLINE_CSS_DEFAULT;
			if ( $enable_inline_css ) {
				if ( !empty( $inline_css ) ) {
					$output .= '<style type="text/css">';
					$output .= wp_strip_all_tags( stripslashes( $inline_css ), true );
					$output .= '</style>';
				}
			}
			$search_live_inline_styles = true;
		}
		return $output;
	}

}
Search_Live_Shortcodes::init();
