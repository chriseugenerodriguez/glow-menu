<?php
/**
 * class-search-live-admin.php
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
 * Settings.
 */
class Search_Live_Admin {

	const NONCE                 = 'search-live-admin-nonce';
	const SECTION_SETTINGS      = 'settings';
	const SECTION_THUMBNAILS    = 'thumbnails';
	const SECTION_CSS           = 'css';
	const SECTION_HELP          = 'help';
	const HELP_POSITION         = 999;
	const MENU_SLUG             = 'search-live';
	const MENU_SLUG_THUMBNAILS  = 'search-live-thumbnails';
	const MENU_SLUG_APPEARANCE  = 'search-live-appearance';
	const MENU_POSITION         = '37.017113';

	/**
	 * Register a hook on the init action.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
	}

	/**
	 * Registers the updater script and styles.
	 */
	public static function admin_enqueue_scripts() {
		wp_register_style( 'sl-admin', SEARCH_LIVE_PLUGIN_URL . '/css/admin.css', array(), SEARCH_LIVE_PLUGIN_VERSION );
		wp_register_style( 'sl-admin-menu', SEARCH_LIVE_PLUGIN_URL . '/css/admin-menu.css', array(), SEARCH_LIVE_PLUGIN_VERSION );
	}

	/**
	 * Enqueues our admin stylesheet.
	 */
	public static function admin_print_styles() {
		wp_enqueue_style( 'sl-admin' );
	}

	/**
	 * Enqueues our admin menu stylesheet (on all admin pages,
	 * as it sets the icon for our menu).
	 */
	public static function admin_print_styles_menu() {
		wp_enqueue_style( 'sl-admin-menu' );
	}

	/**
	 * Register our menu and its sections.
	 */
	public static function admin_menu() {
		$pages = array();
		$pages[] = add_menu_page(
			'Search Live', // don't translate
			'Search Live', // don't translate, also note core bug http://core.trac.wordpress.org/ticket/18857
			Search_Live::MANAGE_SEARCH_LIVE,
			self::MENU_SLUG,
			array( __CLASS__, 'search_live' ),
			'none', // we use our admin-menu.css to set the icon as a background image
			self::MENU_POSITION
		);
		$pages[] = add_submenu_page(
			self::MENU_SLUG,
			__( 'Thumbnails', 'search-live' ),
			__( 'Thumbnails', 'search-live' ),
			Search_Live::MANAGE_SEARCH_LIVE,
			self::MENU_SLUG_THUMBNAILS,
			array( __CLASS__, 'search_live_thumbnails' )
		);
		$pages[] = add_submenu_page(
			self::MENU_SLUG,
			__( 'Appearance', 'search-live' ),
			__( 'Appearance', 'search-live' ),
			Search_Live::MANAGE_SEARCH_LIVE,
			self::MENU_SLUG_APPEARANCE,
			array( __CLASS__, 'search_live_appearance' )
		);
		foreach( $pages as $page ) {
			add_action( 'admin_print_styles-' . $page, array( __CLASS__, 'admin_print_styles' ) );
		}
		// this one is for all admin pages, sets the icon menu
		add_action( 'admin_print_styles', array( __CLASS__, 'admin_print_styles_menu' ) );
	}

	/**
	 * Admin setup.
	 */
	public static function wp_init() {
		add_filter( 'plugin_action_links_'. plugin_basename( SEARCH_LIVE_FILE ), array( __CLASS__, 'admin_settings_link' ) );
		add_action( 'current_screen', array( __CLASS__, 'current_screen' ), self::HELP_POSITION );
	}

	/**
	 * Adds plugin links.
	 *
	 * @param array $links
	 * @param array $links with additional links
	 */
	public static function admin_settings_link( $links ) {
		if ( current_user_can( Search_Live::MANAGE_SEARCH_LIVE ) ) {
			$url = self::get_admin_section_url();
			$links[] = '<a href="' . esc_url( $url ) . '">' . __( 'Settings', 'search-live' ) . '</a>';
			$links[] = '<a href="http://docs.itthinx.com/document/search-live/">' . __( 'Documentation', 'search-live' ) . '</a>';
		}
		return $links;
	}

	/**
	 * Adds the help sections.
	 */
	public static function current_screen() {

		$screen = get_current_screen();
		if ( $screen && stripos( $screen->id,'search-live' ) !== false ) {

			$screen->add_help_tab( array(
				'id'      => 'search-live-documentation',
				'title'   => __( 'Search Live', 'search-live' ),
				'content' =>
					'<div class="search-live-help">' .
					'<h3>' . __( 'Search Live', 'search-live' ) . '</h3>' .
					'<h4>' . __( 'Documentation', 'search-live' ) . '</h4>' .
					'<p>' .
					__( 'Please refer to the <a href="http://docs.itthinx.com/document/search-live/">Search Live</a> documentation page for more details.', 'search-live' ) .
					'</p>' .
					'</div>'
			) );

			$screen->add_help_tab( array(
				'id'      => 'search-live-setup',
				'title'   => __( 'Setup', 'search-live' ),
				'content' =>
					'<div class="search-live-help">' .
					'<h4>' . __( 'Setup', 'search-live' ) . '</h4>' .
					'<p>' .
					__( 'If the option is enabled, the Search Live form replaces the standard WordPress search form.', 'search-live' ) .
					'</p>' .
					'<p>' .
					__( 'You can also place the <code>[search_live]</code> shortcode on a page or use the <em>Search Live</em> widget in a sidebar.', 'search-live' ) .
					'</p>' .
					'</div>'
			) );

			$screen->add_help_tab( array(
				'id'      => 'search-live-shortcodes',
				'title'   => __( 'Shortcodes', 'search-live' ),
				'content' =>
					'<div class="search-live-help">' .
					'<h4>' . __( 'Search Live on a Page', 'search-live' ) . '</h4>' .
					'<p>' . __( 'Here is how you can place a Search Live field on a page.', 'search-live' ) . '</p>' .
					'<p>' . __( 'From your WordPress Dashboard go to <strong>Pages > Add New</strong>. Place the following shortcode on the page:', 'search-live' ) . '</p>' .
					'<p>' . __( '<code>[search_live]</code>', 'search-live' ) . '</p>' .
					'<p>' . __( 'Please make sure that the spelling is correct, all letters must be in lower case.', 'search-live' ) . '</p>' .
					'<p>' . __( 'Click <em>Publish</em> to save the page content and publish the page.', 'search-live' ) . '</p>' .
					'<p>' . __( 'Now click <em>View Page</em> which will show you the search field on your newly created page.', 'search-live' ) . '</p>' .
					'<p>' . __( 'To test the field, at least one post must be published. Start typing a search keyword, search results will show up below the field after you stop typing for an instant.', 'search-live' ) . '</p>' .
					'<p>' . __( 'To refine the settings used, please refer to the advanced configuration options and the shortcode attributes described in the documentation.', 'search-live' ) . '</p>' .
					'</div>'
			) );

			$screen->add_help_tab( array(
				'id'      => 'search-live-widgets',
				'title'   => __( 'Widgets', 'search-live' ),
				'content' =>
					'<div class="search-live-help">' .
					'<h4>' . __( 'Search Live Widget', 'search-live' ) . '</h4>' .
					'<p>' . __( 'To use the widget, go to <strong>Appearance > Widgets</strong> and locate the <em>Search Live</em> widget in the <em>Available Widgets</em> section.', 'search-live' ) . '</p>' .
					'<p>' . __( 'Click the widget, then click one of the sidebar options that appear below and then click <em>Add Widget</em>. You can also drag and drop the widget onto an available sidebar.', 'search-live' ) . '</p>' .
					'<p>' . __( 'Visit a page on your site where the sidebar appears and type in a keyword related to one or more of your posts.', 'search-live' ) . '</p>' .
					'<p>' . __( 'To fine-tune the widget, click the widget after placing it in one of your sidebars and review the available options. Please refer to the documentation for details on the advanced options.', 'search-live' ) . '</p>' .
					'<p>' . __( 'Note that you can place more than one widget in one or more sidebars and that each widget can use its individual settings.', 'search-live' ) . '</p>' .
					'</div>'
			) );
		}
	}

	/**
	 * Returns the admin URL for the default or given section.
	 * 
	 * @param string $section
	 */
	public static function get_admin_section_url( $section = '' ) {
		switch( $section ) {
			case self::SECTION_SETTINGS :
				$page = self::MENU_SLUG;
				break;
			case self::SECTION_THUMBNAILS :
				$page = self::MENU_SLUG_THUMBNAILS;
				break;
			case self::SECTION_CSS :
				$page = self::MENU_SLUG_APPEARANCE;
				break;
			default :
				$page = self::MENU_SLUG;
		}
		$path = add_query_arg( array( 'page' => $page ), admin_url( 'admin.php' ) );
		return $path;
	}

	/**
	 * Capture the request to save our settings and invoke our handler.
	 * Nonce is not checked here.
	 */
	public static function admin_init() {
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'search-live-save-settings' ) {
			self::save();
		}
	}

	/**
	 * Records changes made to the settings if the request is deemed valid and authorized.
	 * Checks nonce and authorization.
	 */
	public static function save() {

		if ( !empty( $_POST['current_section'] ) ) {
			$current_section = $_POST['current_section'];
		} else {
			$current_section = self::SECTION_SETTINGS;
		}

		if ( !current_user_can( Search_Live::MANAGE_SEARCH_LIVE ) ) {
			wp_die( __( 'Access denied.', 'search-live' ) );
		}

		$options = Search_Live::get_options();

		if ( isset( $_POST['submit'] ) ) {
			if ( wp_verify_nonce( $_POST[self::NONCE], 'set' ) ) {

				switch( $current_section ) {

					case self::SECTION_SETTINGS :
						$description_length = isset( $_POST[Search_Live::DESCRIPTION_LENGTH] ) ? intval( $_POST[Search_Live::DESCRIPTION_LENGTH] ) : Search_Live::DESCRIPTION_LENGTH_DEFAULT;
						if ( ( $description_length < 0 ) ) {
							$description_length = Search_Live::DESCRIPTION_LENGTH_DEFAULT;
						}
						$options[Search_Live::DESCRIPTION_LENGTH] = $description_length;
						$options[Search_Live::STANDARD_SEARCH_FORM_REPLACE] =
							isset( $_POST[Search_Live::STANDARD_SEARCH_FORM_REPLACE] );
						Search_Live::set_options( $options );
						break;

					case self::SECTION_THUMBNAILS :
						$thumbnail_width = isset( $_POST[Search_Live_Thumbnail::THUMBNAIL_WIDTH] ) ? intval( $_POST[Search_Live_Thumbnail::THUMBNAIL_WIDTH] ) : Search_Live_Thumbnail::THUMBNAIL_DEFAULT_WIDTH;
						if ( ( $thumbnail_width < 0 ) || $thumbnail_width > Search_Live_Thumbnail::THUMBNAIL_MAX_DIM ) {
							$thumbnail_width = Search_Live_Thumbnail::THUMBNAIL_DEFAULT_WIDTH;
						}
						$options[Search_Live_Thumbnail::THUMBNAIL_WIDTH] = $thumbnail_width;

						$thumbnail_height = isset( $_POST[Search_Live_Thumbnail::THUMBNAIL_HEIGHT] ) ? intval( $_POST[Search_Live_Thumbnail::THUMBNAIL_HEIGHT] ) : Search_Live_Thumbnail::THUMBNAIL_DEFAULT_HEIGHT;
						if ( ( $thumbnail_height < 0 ) || $thumbnail_height > Search_Live_Thumbnail::THUMBNAIL_MAX_DIM ) {
							$thumbnail_height = Search_Live_Thumbnail::THUMBNAIL_DEFAULT_HEIGHT;
						}
						$options[Search_Live_Thumbnail::THUMBNAIL_HEIGHT] = $thumbnail_height;

						$options[Search_Live_Thumbnail::THUMBNAIL_CROP] = isset( $_POST[Search_Live_Thumbnail::THUMBNAIL_CROP] );
						$options[Search_Live_Thumbnail::THUMBNAIL_USE_PLACEHOLDER] = isset( $_POST[Search_Live_Thumbnail::THUMBNAIL_USE_PLACEHOLDER] );
						Search_Live::set_options( $options );
						break;

					case self::SECTION_CSS :
						$options[Search_Live::ENABLE_CSS]        = isset( $_POST[Search_Live::ENABLE_CSS] );
						$options[Search_Live::ENABLE_INLINE_CSS] = isset( $_POST[Search_Live::ENABLE_INLINE_CSS] );
						$options[Search_Live::INLINE_CSS]        = isset( $_POST[Search_Live::INLINE_CSS] ) ? trim( strip_tags( $_POST[Search_Live::INLINE_CSS] ) ) : Search_Live::INLINE_CSS_DEFAULT;
						Search_Live::set_options( $options );
						break;

				}

			}
		}
	}

	public static function search_live_settings() {
		self::search_live( self::SECTION_SETTINGS );
	}

	public static function search_live_thumbnails() {
		self::search_live( self::SECTION_THUMBNAILS );
	}

	public static function search_live_appearance() {
		self::search_live( self::SECTION_CSS );
	}

	/**
	 * Renders the admin section.
	 */
	public static function search_live( $current_section = '' ) {

		global $wpdb;

		if ( empty( $current_section ) ) {
			$current_section = self::SECTION_SETTINGS;
		}

		if ( !current_user_can( Search_Live::MANAGE_SEARCH_LIVE ) ) {
			wp_die( __( 'Access denied.', 'search-live' ) );
		}

		$options = Search_Live::get_options();

		$description_length = isset( $options[Search_Live::DESCRIPTION_LENGTH] ) ?
			$options[Search_Live::DESCRIPTION_LENGTH] :
			Search_Live::DESCRIPTION_LENGTH_DEFAULT;
		$standard_search_form_replace = isset( $options[Search_Live::STANDARD_SEARCH_FORM_REPLACE] ) ?
			$options[Search_Live::STANDARD_SEARCH_FORM_REPLACE] :
			Search_Live::STANDARD_SEARCH_FORM_REPLACE_DEFAULT;

		$thumbnail_width   = isset( $options[Search_Live_Thumbnail::THUMBNAIL_WIDTH] ) ? $options[Search_Live_Thumbnail::THUMBNAIL_WIDTH] : Search_Live_Thumbnail::THUMBNAIL_DEFAULT_WIDTH;
		$thumbnail_height  = isset( $options[Search_Live_Thumbnail::THUMBNAIL_HEIGHT] ) ? $options[Search_Live_Thumbnail::THUMBNAIL_HEIGHT] : Search_Live_Thumbnail::THUMBNAIL_DEFAULT_HEIGHT;
		$thumbnail_crop    = isset( $options[Search_Live_Thumbnail::THUMBNAIL_CROP] ) ? $options[Search_Live_Thumbnail::THUMBNAIL_CROP] : Search_Live_Thumbnail::THUMBNAIL_DEFAULT_CROP;
		$thumbnail_use_placeholder = isset( $options[Search_Live_Thumbnail::THUMBNAIL_USE_PLACEHOLDER] ) ? $options[Search_Live_Thumbnail::THUMBNAIL_USE_PLACEHOLDER] : Search_Live_Thumbnail::THUMBNAIL_USE_PLACEHOLDER_DEFAULT;

		$enable_css        = isset( $options[Search_Live::ENABLE_CSS] ) ? $options[Search_Live::ENABLE_CSS] : Search_Live::ENABLE_CSS_DEFAULT;
		$enable_inline_css = isset( $options[Search_Live::ENABLE_INLINE_CSS] ) ? $options[Search_Live::ENABLE_INLINE_CSS] : Search_Live::ENABLE_INLINE_CSS_DEFAULT;
		$inline_css        = isset( $options[Search_Live::INLINE_CSS] ) ? $options[Search_Live::INLINE_CSS] : Search_Live::INLINE_CSS_DEFAULT;

		echo '<div class="search-live">';

		echo '<form action="" name="options" method="post">';
		echo '<div>';

		switch( $current_section ) {

			case self::SECTION_SETTINGS :
				echo '<div id="search-live-settings-tab" class="search-live-tab">';
				echo '<h1 class="section-heading">' . __( 'Search Live', 'search-live' ) . '</h1>';

				echo '<h2>' . __( 'General Settings', 'search-live' ) . '</h2>';

				echo '<h3>' . __( 'Descriptions', 'search-live' ) . '</h3>';

				echo '<p>';
				echo '<label>';
				_e( 'Length of descriptions', 'search-live');
				echo ' ';
				printf( '<input name="%s" style="width:5em;text-align:right;" type="text" value="%d" />', Search_Live::DESCRIPTION_LENGTH, esc_attr( $description_length ) );
				echo ' ';
				_e( 'words', 'search-live');
				echo '</label>';
				echo '</p>';

				echo '<p class="description">';
				echo __( 'When descriptions are shown, this option determines up to how many words will be included.', 'search-live' );
				echo ' ';
				echo __( 'A description will be derived automatically from the start of a post if the manual excerpt is empty.', 'search-live' );
				echo ' ';
				echo __( 'The length is limited through this option in both cases.', 'search-live' );
				echo '</p>';

				echo '<h2>' . __( 'Standard Search', 'search-live' ) . '</h2>';

				echo '<h3>' . __( 'Search Form', 'search-live' ) . '</h3>';
				echo '<p>';
				echo '<label>';
				printf( '<input name="%s" type="checkbox" %s />', Search_Live::STANDARD_SEARCH_FORM_REPLACE, $standard_search_form_replace ? ' checked="checked" ' : '' );
				echo ' ';
				_e( 'Replace the standard WordPress search form', 'search-live');
				echo '</label>';
				echo '</p>';
				echo '<p class="description">';
				printf( __( 'If enabled and where possible, the %s form replaces the standard WordPress search form.', 'search-live' ), Search_Live::get_plugin_name() );
				echo '</p>';

				echo '</div>'; // #search-live-settings-tab
				break;

			case self::SECTION_THUMBNAILS :
				echo '<div id="search-live-thumbnails-tab" class="search-live-tab">';
				echo '<h1 class="section-heading">' . __( 'Thumbnails', 'search-live' ) . '</h1>';

				echo '<p>';
				echo __( 'These settings are related to the <code>[search_live]</code> shortcode, the <em>Search Live</em> widget and the <em>Search Live</em> API functions.', 'search-live' );
				echo '</p>';

				echo '<h2>' . __( 'Presentation', 'search-live' ) . '</h2>';

				echo '<p class="description">';
				_e( 'Width and height in pixels used for thumbnails displayed in search results.', 'search-live');
				echo '</p>';

				echo '<p>';
				echo '<label>';
				_e( 'Width', 'search-live');
				echo ' ';
				printf( '<input name="%s" style="width:5em;text-align:right;" type="text" value="%d" />', Search_Live_Thumbnail::THUMBNAIL_WIDTH, esc_attr( $thumbnail_width ) );
				echo ' ';
				_e( 'px', 'search-live');
				echo '</label>';
				echo '</p>';

				echo '<p>';
				echo '<label>';
				_e( 'Height', 'search-live');
				echo ' ';
				printf( '<input name="%s" style="width:5em;text-align:right;" type="text" value="%d" />', Search_Live_Thumbnail::THUMBNAIL_HEIGHT, esc_attr( $thumbnail_height ) );
				echo ' ';
				_e( 'px', 'search-live');
				echo '</label>';
				echo '</p>';

				echo '<p>';
				echo '<label>';
				printf( '<input name="%s" type="checkbox" %s />', Search_Live_Thumbnail::THUMBNAIL_CROP, $thumbnail_crop ? ' checked="checked" ' : '' );
				echo ' ';
				_e( 'Crop thumbnails', 'search-live');
				echo '</label>';
				echo '</p>';
				echo '<p class="description">';
				_e( 'If enabled, the thumbnail images are cropped to match the dimensions exactly. Otherwise the thumbnails will be adjusted in size while matching the aspect ratio of the original image.', 'search-live');
				echo '</p>';

				echo '<p>';
				echo '<label>';
				printf( '<input name="%s" type="checkbox" %s />', Search_Live_Thumbnail::THUMBNAIL_USE_PLACEHOLDER, $thumbnail_use_placeholder ? ' checked="checked" ' : '' );
				echo ' ';
				_e( 'Placeholder thumbnails', 'search-live');
				echo '</label>';
				echo '</p>';
				echo '<p class="description">';
				_e( 'If enabled, posts without a featured image will show a default placeholder thumbnail image.', 'search-live');
				echo '</p>';

				echo '</div>'; #search-live-thumbnails-tab
				break;

			case self::SECTION_CSS :
				echo '<div id="search-live-css-tab" class="search-live-tab">';
				echo '<h1 class="section-heading">' . __( 'CSS', 'search-live' ) . '</h1>';

				echo '<p>';
				echo __( 'These settings are related to the <code>[search_live]</code> shortcode, the <em>Search Live</em> widget and the <em>Search Live</em> API functions.', 'search-live' );
				echo '</p>';

				echo '<h2>' . __( 'Standard Stylesheet', 'search-live' ) . '</h2>';

				echo '<p>';
				echo '<label>';
				printf( '<input name="%s" type="checkbox" %s />', Search_Live::ENABLE_CSS, $enable_css ? ' checked="checked" ' : '' );
				echo ' ';
				_e( 'Use the standard stylesheet', 'search-live');
				echo '</label>';
				echo '</p>';
				echo '<p class="description">';
				_e( 'If this option is enabled, the standard stylesheet is loaded when the search is displayed.', 'search-live');
				echo '</p>';

				echo '<h2>' . __( 'Inline Styles', 'search-live' ) . '</h2>';

				echo '<p>';
				echo '<label>';
				printf( '<input name="%s" type="checkbox" %s />', Search_Live::ENABLE_INLINE_CSS, $enable_inline_css ? ' checked="checked" ' : '' );
				echo ' ';
				_e( 'Use inline styles', 'search-live');
				echo '</label>';
				echo '</p>';
				echo '<p class="description">';
				_e( 'If this option is enabled, the inline styles are used when the search is displayed.', 'search-live');
				echo '</p>';

				echo '<p>';
				echo '<label>';
				_e( 'Inline styles', 'search-live');
				echo '<br/>';
				printf( '<textarea style="font-family:monospace;width:50%%;height:25em;" name="%s">%s</textarea>', Search_Live::INLINE_CSS, stripslashes( esc_textarea( $inline_css ) ) );
				echo '</label>';
				echo '</p>';

				echo '</div>'; // #search-live-css-tab
				break;

		}

		global $hide_save_button;
		$hide_save_button = true;

		wp_nonce_field( 'set', self::NONCE );
		echo '<input type="hidden" name="action" value="search-live-save-settings" />';
		printf( '<input type="hidden" name="current_section" value="%s" />', esc_attr( $current_section ) );

		echo '<p>';
		echo '<input class="button button-primary" type="submit" name="submit" value="' . __( 'Save changes', 'search-live' ) . '"/>';
		echo '</p>';
		echo '</div>';

		echo '</form>';

		echo '</div>';

		self::footer();

	}

	/**
	 * Returns or renders the footer.
	 * 
	 * @param boolean $render
	 */
	public static function footer( $render = true ) {
		$footer =
			'<div class="search-live-admin-footer">' .
			__( 'Thank you for using <a href="http://www.itthinx.com/plugins/search-live/" target="_blank">Search Live</a> by <a href="http://www.itthinx.com" target="_blank">itthinx</a>.', 'search-live' ) .
			'</div>';
		$footer = apply_filters( 'search_live_admin_footer', $footer );
		if ( $render ) {
			echo $footer;
		} else {
			return $footer;
		}
	}
}
Search_Live_Admin::init();
