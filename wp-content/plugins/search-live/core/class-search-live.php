<?php
/**
 * class-search-live.php
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
 * Boots.
 * Essentials.
 */
class Search_Live {

	const OPTIONS                   = 'search-live';

	const DB_PREFIX                 = 'search_live_';

	const DESCRIPTION_LENGTH                   = 'description-length';
	const DESCRIPTION_LENGTH_DEFAULT           = 55;
	const STANDARD_SEARCH_FORM_REPLACE         = 'standard-search-form-replace';
	const STANDARD_SEARCH_FORM_REPLACE_DEFAULT = true;

	const ENABLE_CSS                = 'enable-css';
	const ENABLE_CSS_DEFAULT        = true;
	const ENABLE_INLINE_CSS         = 'enable-inline-css';
	const ENABLE_INLINE_CSS_DEFAULT = false;
	const INLINE_CSS                = 'inline-css';
	const INLINE_CSS_DEFAULT        = '';
	const DEFAULT_DELAY             = 500;
	const MIN_DELAY                 = 250;
	const DEFAULT_CHARACTERS        = 1;
	const MIN_CHARACTERS            = 1;

	const MANAGE_SEARCH_LIVE        = 'manage_search_live';

	private static $admin_messages = array();

	/**
	 * Put hooks in place and activate.
	 */
	public static function init() {
		register_activation_hook( SEARCH_LIVE_FILE, array( __CLASS__, 'activate' ) );
		//register_deactivation_hook( SEARCH_LIVE_FILE, array( __CLASS__, 'deactivate' ) );
		//register_uninstall_hook( SEARCH_LIVE_FILE, array( __CLASS__, 'uninstall' ) );
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
		require_once SEARCH_LIVE_CORE_LIB . '/class-search-live-service.php';
		require_once SEARCH_LIVE_VIEWS_LIB . '/class-search-live-shortcodes.php';
		require_once SEARCH_LIVE_VIEWS_LIB . '/class-search-live-widget.php';
		require_once SEARCH_LIVE_VIEWS_LIB . '/class-search-live-thumbnail.php';
		require_once SEARCH_LIVE_VIEWS_LIB . '/class-search-live-form.php';
		if ( is_admin() ) {
			require_once SEARCH_LIVE_ADMIN_LIB . '/class-search-live-admin.php';
		}
	}

	/**
	 * Loads translations.
	 * Loads the notice class in admin.
	 */
	public static function wp_init() {
		// translations
		load_plugin_textdomain( 'search-live', null, 'search-live/languages' );
		if ( is_admin() ) {
			if ( current_user_can( 'activate_plugins' ) ) { // important: after init hook
				if ( SEARCH_LIVE_PLUGIN == 'search-live' ) {
					require_once SEARCH_LIVE_ADMIN_LIB . '/class-search-live-notice.php';
				}
			}
		}
	}

	/**
	 * Activate plugin action.
	 * 
	 * Adds the plugin's capabilities to the administrator role, or
	 * in lack thereof, to those roles that have the activate_plugins
	 * capability.
	 * 
	 * @param boolean $network_wide
	 */
	public static function activate( $network_wide = false ) {
		global $wp_roles;
		if ( $administrator_role = $wp_roles->get_role( 'administrator' ) ) {
			$administrator_role->add_cap( self::MANAGE_SEARCH_LIVE );
		} else {
			foreach ( $wp_roles->role_objects as $role ) {
				if ($role->has_cap( 'activate_plugins' ) ) {
					$role->add_cap( self::MANAGE_SEARCH_LIVE );
				}
			}
		}
	}

	/**
	 * Deactivate plugin action.
	 * 
	 * Currently not used.
	 * 
	 * @param boolean $network_wide
	 */
	public static function deactivate( $network_wide = false ) {
	}

	/**
	 * Uninstall plugin action.
	 * 
	 * Currently not used.
	 */
	public static function uninstall() {
	}

	/**
	 * Prints admin notices.
	 */
	public static function admin_notices() {
		if ( !empty( self::$admin_messages ) ) {
			foreach ( self::$admin_messages as $msg ) {
				echo $msg;
			}
		}
	}

	/**
	 * Get plugin options.
	 * @return array
	 */
	public static function get_options() {
		$data = get_option( self::OPTIONS, null );
		if ( $data === null ) {
			if ( add_option( self::OPTIONS, array(), '', 'no' ) ) {
				$data = get_option( self::OPTIONS, null );
			}
		}
		return $data;
	}

	/**
	 * Set plugin options.
	 * @param array $data
	 */
	public static function set_options( $data ) {
		$current_data = get_option( self::OPTIONS, null );
		if ( $current_data === null ) {
			add_option( self::OPTIONS, $data, '', 'no' );
		} else {
			update_option( self::OPTIONS, $data );
		}
	}

	/**
	 * Returns the plugin name intended for humans to read.
	 * 
	 * @return string
	 */
	public static function get_plugin_name() {
		return ucwords( str_replace( '-', ' ', SEARCH_LIVE_PLUGIN ) );
	}
}
Search_Live::init();
