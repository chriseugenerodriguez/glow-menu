<?php
/**
 * class-search-live-form.php
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

/**
 * Standard WordPress search form replacement.
 */
class Search_Live_Form {

	/**
	 * Adds the replacement filter if the standard search form
	 * should be replaced.
	 */
	public static function init() {
		$options = Search_Live::get_options();
		$standard_search_form_replace =
			isset( $options[Search_Live::STANDARD_SEARCH_FORM_REPLACE] ) ?
			$options[Search_Live::STANDARD_SEARCH_FORM_REPLACE] :
			Search_Live::STANDARD_SEARCH_FORM_REPLACE_DEFAULT;
		if ( $standard_search_form_replace ) {
			add_filter( 'get_search_form', array( __CLASS__,'get_search_form' ) );
		}
	}

	/**
	 * Returns our own search form instead of the standard search form.
	 * Hooked on the get_search_form filter if the option is activated.
	 * 
	 * @param string $form
	 * @return string
	 */
	public static function get_search_form( $form ) {
		return Search_Live_Shortcodes::search_live();
	}
}
Search_Live_Form::init();
