<?php
/**
 * class-search-live-widget.php
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
 * Live search widget.
 */
class Search_Live_Widget extends WP_Widget {

	static $the_name = '';

	/**
	 * @var string cache id
	 */
	static $cache_id = 'search_live_widget';

	/**
	 * @var string cache flag
	 */
	static $cache_flag = 'widget';

	static $defaults = array();

	/**
	 * Initialize.
	 */
	static function init() {
		add_action( 'widgets_init', array( __CLASS__, 'widgets_init' ) );
		self::$the_name = __( 'Search Live', 'search-live' );
	}

	/**
	 * Registers the widget.
	 */
	static function widgets_init() {
		register_widget( 'Search_Live_Widget' );
	}

	/**
	 * Creates the widget.
	 */
	function __construct() {
		parent::__construct(
			self::$cache_id,
			self::$the_name,
			array(
				'description' => __( 'The Search Live Widget', 'search-live' )
			)
		);
	}

	/**
	 * Clears cached widget.
	 */
	static function cache_delete() {
		wp_cache_delete( self::$cache_id, self::$cache_flag );
	}

	/**
	 * Widget output
	 * 
	 * @see WP_Widget::widget()
	 * @link http://codex.wordpress.org/Class_Reference/WP_Object_Cache
	 */
	function widget( $args, $instance ) {

		// This is done within the shortcode but the required scripts can
		// go missing if we don't do it here, too.
		Search_Live_Shortcodes::load_resources();

		$cache = wp_cache_get( self::$cache_id, self::$cache_flag );
		if ( ! is_array( $cache ) ) {
			$cache = array();
		}
		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );

		$output = '';

		$output .= $before_widget;
		if ( !empty( $title ) ) {
			$output .= $before_title . $title . $after_title;
		}
		$instance['title'] = $instance['query_title'];

		// WPML
		if ( !empty( $instance['placeholder'] ) ) {
			$instance['placeholder'] = apply_filters( 'wpml_translate_single_string', $instance['placeholder'], 'search-live', 'Search Live Widget: placeholder' );
		}
		if ( !empty( $instance['field_title'] ) ) {
			$instance['field_title'] = apply_filters( 'wpml_translate_single_string', $instance['field_title'], 'search-live', 'Search Live Widget: field_title' );
		}
		if ( !empty( $instance['no_results'] ) ) {
			$instance['no_results'] = apply_filters( 'wpml_translate_single_string', $instance['no_results'], 'search-live', 'Search Live Widget: no_results' );
		}
		if ( !empty( $instance['submit_button_label'] ) ) {
			$instance['submit_button_label'] = apply_filters( 'wpml_translate_single_string', $instance['submit_button_label'], 'search-live', 'Search Live Widget: submit_button_label' );
		}

		$output .= Search_Live_Shortcodes::search_live( $instance );
		$output .= $after_widget;

		echo $output;

		$cache[$args['widget_id']] = $output;
		wp_cache_set( self::$cache_id, $cache, self::$cache_flag );

	}

	/**
	 * Save widget options
	 * 
	 * @see WP_Widget::update()
	 */
	function update( $new_instance, $old_instance ) {

		global $wpdb;

		$settings = $old_instance;

		// widget title
		$settings['title'] = trim( strip_tags( $new_instance['title'] ) );

		// search in titles, excerpt, content
		$settings['query_title']   = !empty( $new_instance['query_title'] ) ? 'yes' : 'no';
		$settings['excerpt'] = !empty( $new_instance['excerpt'] ) ? 'yes' : 'no';
		$settings['content'] = !empty( $new_instance['content'] ) ? 'yes' : 'no';

		$settings['order']    = !empty( $new_instance['order'] ) ? $new_instance['order'] : 'DESC';
		$settings['order_by'] = !empty( $new_instance['order_by'] ) ? $new_instance['order_by'] : 'date';

		$limit = !empty( $new_instance['limit'] ) ? intval( $new_instance['limit'] ) : Search_Live_Service::DEFAULT_LIMIT;
		if ( $limit < 0 ) {
			$limit = Search_Live_Service::DEFAULT_LIMIT;
		}
		$settings['limit'] = $limit;

		$settings['thumbnails'] = !empty( $new_instance['thumbnails'] ) ? 'yes' : 'no';

		$settings['show_description'] = !empty( $new_instance['show_description'] ) ? 'yes' : 'no';

		$delay = !empty( $new_instance['delay'] ) ? intval( $new_instance['delay'] ) : Search_Live::DEFAULT_DELAY;
		if ( $delay < Search_Live::MIN_DELAY ) {
			$delay = Search_Live::MIN_DELAY;
		}
		$settings['delay'] = $delay;

		$characters = !empty( $new_instance['characters'] ) ? intval( $new_instance['characters'] ) : Search_Live::DEFAULT_CHARACTERS;
		if ( $characters < Search_Live::MIN_CHARACTERS ) {
			$characters = Search_Live::MIN_CHARACTERS;
		}
		$settings['characters'] = $characters;

		$settings['placeholder'] = trim( strip_tags( $new_instance['placeholder'] ) );
		$settings['field_title'] = trim( strip_tags( $new_instance['field_title'] ) );

		$settings['dynamic_focus'] = !empty( $new_instance['dynamic_focus'] ) ? 'yes' : 'no';
		$settings['floating']      = !empty( $new_instance['floating'] ) ? 'yes' : 'no';
		$settings['inhibit_enter'] = !empty( $new_instance['inhibit_enter'] ) ? 'yes' : 'no';
		$settings['submit_button'] = !empty( $new_instance['submit_button'] ) ? 'yes' : 'no';
		$settings['submit_button_label'] = strip_tags( $new_instance['submit_button_label'] );
		$settings['navigable']     = !empty( $new_instance['navigable'] ) ? 'yes' : 'no';
		$settings['no_results']    = trim( strip_tags( $new_instance['no_results'] ) );
		$settings['auto_adjust']   = !empty( $new_instance['auto_adjust'] ) ? 'yes' : 'no';

		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$settings['wpml']   = !empty( $new_instance['wpml'] ) ? 'yes' : 'no';
		}

		// WPML
		// https://wpml.org/documentation/support/translation-for-texts-by-other-plugins-and-themes/
		do_action( 'wpml_register_single_string', 'search-live', 'Search Live Widget: placeholder', $settings['placeholder'] );
		do_action( 'wpml_register_single_string', 'search-live', 'Search Live Widget: field_title', $settings['field_title'] );
		do_action( 'wpml_register_single_string', 'search-live', 'Search Live Widget: no_results', $settings['no_results'] );
		do_action( 'wpml_register_single_string', 'search-live', 'Search Live Widget: submit_button_label', $settings['submit_button_label'] );

		$this->cache_delete();

		return $settings;
	}

	/**
	 * Output admin widget options form
	 * 
	 * @see WP_Widget::form()
	 */
	function form( $instance ) {

		extract( self::$defaults );

		// title
		$widget_title = isset( $instance['title'] ) ? $instance['title'] : "";
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'The widget title.', 'search-live' ) ) );
		echo __( 'Title', 'search-live' );
		echo '<input class="widefat" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . esc_attr( $widget_title ) . '" />';
		echo '</label>';
		echo '</p>';

		echo '<h5>' . __( 'Search Results', 'search-live' ) . '</h5>';

		$title = isset( $instance['query_title'] ) ? $instance['query_title'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'Search results should include matching titles.', 'search-live' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'query_title' ),
			$this->get_field_name( 'query_title' ),
			$title == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo __( 'Search in titles', 'search-live' );
		echo '</label>';
		echo '</p>';

		$excerpt = isset( $instance['excerpt'] ) ? $instance['excerpt'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'Search results should include matches in excerpts.', 'search-live' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'excerpt' ),
			$this->get_field_name( 'excerpt' ),
			$excerpt == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo __( 'Search in excerpts', 'search-live' );
		echo '</label>';
		echo '</p>';

		$content = isset( $instance['content'] ) ? $instance['content'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'Search results should include matches in contents.', 'search-live' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'content' ),
			$this->get_field_name( 'content' ),
			$content == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo __( 'Search in contents', 'search-live' );
		echo '</label>';
		echo '</p>';

		$order_by = isset( $instance['order_by'] ) ? $instance['order_by'] : 'date';
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'Order the results by the chosen property.', 'search-live' ) ) );
		echo __( 'Order by ...', 'search-live' );
		echo ' ';
		printf(
			'<select id="%s" name="%s">',
			$this->get_field_id( 'order_by' ),
			$this->get_field_name( 'order_by' )
		);
		$options = array(
			'date'  => __( 'Date', 'search-live' ),
			'title' => __( 'Title', 'search-live' ),
			'ID'    => __( 'ID', 'search-live' ),
			'rand'  => __( 'Random', 'search-live' )
		);
		foreach( $options as $key => $value ) {
			printf( '<option value="%s" %s>%s</option>', $key, $order_by == $key ? ' selected="selected" ' : '', $value );
		}
		echo '</select>';
		echo '</label>';
		echo '</p>';

		$order = isset( $instance['order'] ) ? $instance['order'] : 'DESC';
		echo '<p>';
		echo '<label>';
		printf( '<input type="radio" name="%s" value="ASC" %s />', $this->get_field_name( 'order' ), $order == 'ASC' ? ' checked="checked" ' : '' );
		echo ' ';
		echo __( 'Ascending', 'search-live' );
		echo '</label>';
		echo ' ';
		echo '<label>';
		printf( '<input type="radio" name="%s" value="DESC" %s />', $this->get_field_name( 'order' ), $order == 'DESC' ? ' checked="checked" ' : '' );
		echo ' ';
		echo __( 'Descending', 'search-live' );
		echo '</label>';
		echo '</p>';

		// limit
		$limit = isset( $instance['limit'] ) ? intval( $instance['limit'] ) : Search_Live_Service::DEFAULT_LIMIT;
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'Limit the maximum number of results shown.', 'search-live' ) ) );
		echo __( 'Limit', 'search-live' );
		echo ' ';
		echo '<input id="' . $this->get_field_id( 'limit' ) . '" name="' . $this->get_field_name( 'limit' ) . '" type="text" value="' . esc_attr( $limit ) . '" />';
		echo '</label>';
		echo '</p>';

		$thumbnails = isset( $instance['thumbnails'] ) ? $instance['thumbnails'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'Show a thumbnail for each result.', 'search-live' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'thumbnails' ),
			$this->get_field_name( 'thumbnails' ),
			$thumbnails == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo __( 'Show thumbnails', 'search-live' );
		echo '</label>';
		echo '</p>';

		$show_description = isset( $instance['show_description'] ) ? $instance['show_description'] : 'no';
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'Show short descriptions.', 'search-live' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'show_description' ),
			$this->get_field_name( 'show_description' ),
			$show_description == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo __( 'Show descriptions', 'search-live' );
		echo '</label>';
		echo '</p>';

		echo '<h5>' . __( 'Search Form and UI Interaction', 'search-live' ) . '</h5>';

		// delay
		$delay = isset( $instance['delay'] ) ? intval( $instance['delay'] ) : Search_Live::DEFAULT_DELAY;
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'The delay until the search starts after the user stops typing (in milliseconds, minimum %d).', 'search-live' ), Search_Live::MIN_DELAY ) );
		echo __( 'Delay', 'search-live' );
		echo ' ';
		echo '<input id="' . $this->get_field_id( 'delay' ) . '" name="' . $this->get_field_name( 'delay' ) . '" type="text" value="' . esc_attr( $delay ) . '" />';
		echo ' ';
		echo __( 'ms', 'search-live' );
		echo '</label>';
		echo '</p>';

		// characters
		$characters = isset( $instance['characters'] ) ? intval( $instance['characters'] ) : Search_Live::DEFAULT_CHARACTERS;
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'The minimum number of characters required to start a search.', 'search-live' ) ) );
		echo __( 'Characters', 'search-live' );
		echo ' ';
		echo '<input id="' . $this->get_field_id( 'characters' ) . '" name="' . $this->get_field_name( 'characters' ) . '" type="text" value="' . esc_attr( $characters ) . '" />';
		echo '</label>';
		echo '</p>';

		// inhibit the enter key
		$inhibit_enter = isset( $instance['inhibit_enter'] ) ? $instance['inhibit_enter'] : 'no';
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'If the Enter key is not inhibited, a normal search is requested when the visitor presses the Enter key in the search field.', 'search-live' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'inhibit_enter' ),
			$this->get_field_name( 'inhibit_enter' ),
			$inhibit_enter == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo __( 'Inhibit form submission via the <em>Enter</em> key', 'search-live' );
		echo '</label>';
		echo '</p>';

		$navigable = isset( $instance['navigable'] ) ? $instance['navigable'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'If enabled, the visitor can use the cursor keys to navigate through the search results and visit a search result link by pressing the Enter key.', 'search-live' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'navigable' ),
			$this->get_field_name( 'navigable' ),
			$navigable == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo __( 'Navigable results', 'search-live' );
		echo '</label>';
		echo '</p>';

		// placeholder
		$placeholder = isset( $instance['placeholder'] ) ? $instance['placeholder'] : __( 'Search', 'search-live' );
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'The placeholder text for the search field.', 'search-live' ) ) );
		echo __( 'Placeholder', 'search-live' );
		echo ' ';
		echo '<input id="' . $this->get_field_id( 'placeholder' ) . '" name="' . $this->get_field_name( 'placeholder' ) . '" type="text" value="' . esc_attr( $placeholder ) . '" />';
		echo '</label>';
		echo '</p>';

		// field title
		$field_title = isset( $instance['field_title'] ) ? $instance['field_title'] : _x( 'Search for:', 'label', 'search-live' );
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'The tooltip of the search field.', 'search-live' ) ) );
		echo __( 'Tooltip', 'search-live' );
		echo ' ';
		echo '<input id="' . $this->get_field_id( 'field_title' ) . '" name="' . $this->get_field_name( 'field_title' ) . '" type="text" value="' . esc_attr( $field_title ) . '" />';
		echo '</label>';
		echo '</p>';

		// submit button
		$submit_button = isset( $instance['submit_button'] ) ? $instance['submit_button'] : 'no';
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'Show a submit button along with the search field.', 'search-live' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'submit_button' ),
			$this->get_field_name( 'submit_button' ),
			$submit_button == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo __( 'Submit button', 'search-live' );
		echo '</label>';
		echo '</p>';

		// submit button label
		$submit_button_label = isset( $instance['submit_button_label'] ) ? $instance['submit_button_label'] : __( 'Search', 'search-live' );
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'The text shown on the submit button.', 'search-live' ) ) );
		echo __( 'Submit button label', 'search-live' );
		echo ' ';
		echo '<input id="' . $this->get_field_id( 'submit_button_label' ) . '" name="' . $this->get_field_name( 'submit_button_label' ) . '" type="text" value="' . esc_attr( $submit_button_label ) . '" />';
		echo '</label>';
		echo '</p>';

		// dynamic focus
		$dynamic_focus = isset( $instance['dynamic_focus'] ) ? $instance['dynamic_focus'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'Show/hide search results when the search input field gains/loses focus.', 'search-live' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'dynamic_focus' ),
			$this->get_field_name( 'dynamic_focus' ),
			$dynamic_focus == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo __( 'Dynamic focus', 'search-live' );
		echo '</label>';
		echo '</p>';

		// floating results
		$floating = isset( $instance['floating'] ) ? $instance['floating'] : 'no';
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'Search results are shown floating below the search field.', 'search-live' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'floating' ),
			$this->get_field_name( 'floating' ),
			$floating == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo __( 'Floating results', 'search-live' );
		echo '</label>';
		echo '</p>';

		// no results
		$no_results = isset( $instance['no_results'] ) ? $instance['no_results'] : '';
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'The text shown when no search results are obtained.', 'search-live' ) ) );
		echo __( 'No results', 'search-live' );
		echo ' ';
		echo '<input id="' . $this->get_field_id( 'no_results' ) . '" name="' . $this->get_field_name( 'no_results' ) . '" type="text" value="' . esc_attr( $no_results ) . '" />';
		echo '</label>';
		echo '</p>';

		// auto adjust the results width
		$auto_adjust = isset( $instance['auto_adjust'] ) ? $instance['auto_adjust'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', sprintf( __( 'Automatically adjust the width of the results to match that of the search field.', 'search-live' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'auto_adjust' ),
			$this->get_field_name( 'auto_adjust' ),
			$auto_adjust == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo __( 'Auto-adjust results width', 'search-live' );
		echo '</label>';
		echo '</p>';

		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$wpml = isset( $instance['wpml'] ) ? $instance['wpml'] : 'no';
			echo '<p>';
			echo sprintf( '<label title="%s">', sprintf( __( 'Filter search results based on the current language.', 'search-live' ) ) );
			printf(
				'<input type="checkbox" id="%s" name="%s" %s />',
				$this->get_field_id( 'wpml' ),
				$this->get_field_name( 'wpml' ),
				$wpml == 'yes' ? ' checked="checked" ' : ''
			);
			echo ' ';
			echo __( 'WPML Language Filter', 'search-live' );
			echo '</label>';
			echo '</p>';
		}
	}

}

Search_Live_Widget::init();
