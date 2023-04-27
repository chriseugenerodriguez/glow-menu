<?php
/**
 * class-search-live-service.php
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
 * Search service.
 */
class Search_Live_Service {

	const SEARCH_TOKEN  = 'search-live';
	const SEARCH_QUERY  = 'search-live-query';

	const LIMIT         = 'limit';
	const DEFAULT_LIMIT = 10;

	const TITLE         = 'title';
	const EXCERPT       = 'excerpt';
	const CONTENT       = 'content';

	const DEFAULT_TITLE   = true;
	const DEFAULT_EXCERPT = true;
	const DEFAULT_CONTENT = true;

	const ORDER            = 'order';
	const DEFAULT_ORDER    = 'DESC';
	const ORDER_BY         = 'order_by';
	const DEFAULT_ORDER_BY = 'date';

	const THUMBNAILS          = 'thumbnails';
	const DEFAULT_THUMBNAILS  = true;

	const CACHE_LIFETIME     = 300; // in seconds, 5 minutes
	const POST_CACHE_GROUP   = 'ixslp';
	const RESULT_CACHE_GROUP = 'ixslr';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wp_enqueue_scripts' ) );
		add_filter( 'icl_set_current_language', array( __CLASS__, 'icl_set_current_language' ) );
		add_action( 'wp_ajax_search_live', array( __CLASS__, 'wp_ajax_search_live' ) );
		add_action( 'wp_ajax_nopriv_search_live', array( __CLASS__, 'wp_ajax_search_live' ) );
	}

	/**
	 * Handles wp_ajax_search_live and wp_ajax_nopriv_search_live actions.
	 * The request must carry action='search_live' for these actions
	 * to be invoked and this handler to be triggered. This is done in
	 * search-live.js where the params are passed to the jQuery.post() call.
	 */
	public static function wp_ajax_search_live() {
		ob_start();
		$results = Search_Live_Service::request_results();
		$ob = ob_get_clean();
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG && $ob ) {
			error_log( $ob );
		}
		echo json_encode( $results );
		exit;
	}

	public static function wp_enqueue_scripts() {
		wp_register_script( 'typewatch', SEARCH_LIVE_DEBUG ? SEARCH_LIVE_PLUGIN_URL . '/js/jquery.typewatch.js' : SEARCH_LIVE_PLUGIN_URL . '/js/jquery.typewatch.min.js', array( 'jquery' ), SEARCH_LIVE_PLUGIN_VERSION, true );
		wp_register_script( 'search-live', SEARCH_LIVE_DEBUG ? SEARCH_LIVE_PLUGIN_URL . '/js/search-live.js' : SEARCH_LIVE_PLUGIN_URL . '/js/search-live.min.js', array( 'jquery', 'typewatch' ), SEARCH_LIVE_PLUGIN_VERSION, true );
		wp_register_style( 'search-live', SEARCH_LIVE_PLUGIN_URL . '/css/search-live.css', array(), SEARCH_LIVE_PLUGIN_VERSION );

		// we don't need to do this because we're writing the URL to the handler $post_target_url
		//wp_localize_script( 'search-live', 'searchLiveAjax', array(
		//	'ajaxurl' => admin_url( 'admin-ajax.php' )
		//) );
	}

	/**
	 * Adds the pre_get_posts and posts_search filters to modify the search results.
	 */
	public static function wp_init() {
		$options = Search_Live::get_options();
		if ( self::process_request() ) {
			add_action( 'pre_get_posts', array( __CLASS__, 'pre_get_posts' ) );
			add_filter( 'posts_search', array( __CLASS__, 'posts_search' ), 10 , 2 );
		}
	}

	/**
	 * Modifies the search results, replacing the standard search results.
	 * This method is hooked on the pre_get_posts action
	 * when the request contains the ixsl parameter.
	 * 
	 * @see Search_Live_Service::wp_init()
	 * @since 1.5.0
	 * @param WP_Query $wp_query
	 * @return string
	 */
	public static function pre_get_posts( $wp_query ) {
		if ( self::process_search_query( $wp_query ) ) {
			// Add our search query parameter required by self::get_post_ids_for_request().
			if ( !isset( $_REQUEST[self::SEARCH_QUERY] ) ) {
				$_REQUEST[self::SEARCH_QUERY] = $_REQUEST['s'];
			}
			$post_ids = self::get_post_ids_for_request();
			if ( !empty( $post_ids ) ) {
				$wp_query->set( 'post__in', $post_ids );
			}
		}
	}

	/**
	 * Empty the search clause in the query when we use our own results.
	 * We're doing this instead of $wp_query->set( 's', '' ) to void the standard query; 
	 * since the 's' query variable is used in get_search_query() which in turn is
	 * used in general-template.php and by many themes to display the search
	 * terms along with results (these would appear empty if we voided 's').
	 * 
	 * @param string $search
	 * @param WP_Query $wp_query
	 * @return string
	 */
	public static function posts_search( $search, $wp_query ) {
		if ( self::process_search_query( $wp_query ) ) {
			$search = '';
		}
		return $search;
	}

	/**
	 * Returns true if the query is to be handled by us.
	 * 
	 * @param WP_Query $wp_query
	 * @return boolean true if the query should be handled by us
	 */
	private static function process_search_query( &$wp_query ) {
		$result = false;
		if ( $wp_query->is_search() ) {
			if ( self::process_request() ) {
				$result = true;
			}
		}
		return $result;
	}

	/**
	 * Returns true if the request should be handled by us.
	 * 
	 * @return boolean true if the request should be hanlded by us
	 */
	private static function process_request() {
		return isset( $_REQUEST['s'] ) && isset( $_REQUEST['ixsl'] ); // @todo remove true
	}

	/**
	 * Looks at the $_REQUEST for search parameters.
	 * 
	 * @return array
	 */
	private static function get_request_parameters() {

		$title       = isset( $_REQUEST[self::TITLE] ) ? intval( $_REQUEST[self::TITLE] ) > 0 : self::DEFAULT_TITLE;
		$excerpt     = isset( $_REQUEST[self::EXCERPT] ) ? intval( $_REQUEST[self::EXCERPT] ) > 0 : self::DEFAULT_EXCERPT;
		$content     = isset( $_REQUEST[self::CONTENT] ) ? intval( $_REQUEST[self::CONTENT] ) > 0 : self::DEFAULT_CONTENT;
		$limit       = isset( $_REQUEST[self::LIMIT] ) ? intval( $_REQUEST[self::LIMIT] ) : self::DEFAULT_LIMIT;
		$numberposts = intval( apply_filters( 'search_live_limit', $limit ) );
		$order       = isset( $_REQUEST[self::ORDER] ) ? strtoupper( trim( $_REQUEST[self::ORDER] ) ) : self::DEFAULT_ORDER;
		switch( $order ) {
			case 'DESC' :
			case 'ASC' :
				break;
			default :
				$order = 'DESC';
		}
		$order_by    = isset( $_REQUEST[self::ORDER_BY] ) ? strtolower( trim( $_REQUEST[self::ORDER_BY] ) ) : self::DEFAULT_ORDER_BY;
		switch( $order_by ) {
			case 'date' :
			case 'title' :
			case 'ID' :
			case 'rand' :
				break;
			default :
				$order_by = 'date';
		}

		// remove non-alphanumeric characters and compact whitespace
		$search_query = preg_replace( '/[^\p{L}\p{N}]++/u', ' ', $_REQUEST[self::SEARCH_QUERY] );
		$search_query = trim( preg_replace( '/\s+/', ' ', $search_query ) );

		return array(
			'title'        => $title,
			'excerpt'      => $excerpt,
			'content'      => $content,
			'limit'        => $limit,
			'numberposts'  => $numberposts,
			'order'        => $order,
			'order_by'     => $order_by,
			'search_query' => $search_query
		);
	}

	/**
	 * Returns results for the search request as an array of post IDs.
	 * @return array of post IDs
	 */
	public static function get_post_ids_for_request() {

		global $wpdb;

		$parameters = self::get_request_parameters();
		extract( $parameters );

		// make sure to search somewhere, at least in the title
		if ( !$title && !$excerpt && !$content ) {
			$title = true;
		}

		$search_terms = explode( ' ', $search_query );

		$cache_key = self::get_cache_key( array(
			'title'        => $title,
			'excerpt'      => $excerpt,
			'content'      => $content,
			'limit'        => $numberposts,
			'order'        => $order,
			'order_by'     => $order_by,
			'search_query' => $search_query
		) );

		$post_ids = wp_cache_get( $cache_key, self::POST_CACHE_GROUP, true );
		if ( $post_ids !== false ) {
			return $post_ids;
		}

		$options = Search_Live::get_options();
		$conj = array();

		foreach ( $search_terms as $search_term ) {

			$args   = array();
			$params = array();

			// Important: we are using prepare and can escape using simply
			// $wpdb::esc_like(); Without prepare we would also have to do esc_sql :
			// $like = '%' . esc_sql( $wpdb->esc_like( ... ) ) . '%';
			$like = '%' . $wpdb->esc_like( $search_term ) . '%';

			if ( $title ) {
				$args[] = ' post_title LIKE %s ';
				$params[] = $like;
			}
			if ( $excerpt ) {
				$args[] = ' post_excerpt LIKE %s ';
				$params[] = $like;
			}
			if ( $content ) {
				$args[] = ' post_content LIKE %s ';
				$params[] = $like;
			}

			if ( !empty( $args ) ) {
				// IMPORTANT : Do NOT skip the call to prepare() as we have $like in there!
				$conj[] = $wpdb->prepare( sprintf( ' ( %s ) ', implode( ' OR ', $args ) ), $params );
			}

		}

		$conditions = implode( ' AND ', $conj );
		$include = array();

		if ( $title || $excerpt || $content ) {
			$post_types = get_post_types( array( 'public' => true ) );
			if ( empty( $post_types ) || !is_array( $post_types ) ) {
				$post_types = array( 'post', 'page' );
			}
			$post_types = array_map( 'esc_sql', $post_types );
			$post_types = "('" . implode( "','", $post_types) . "')";
			$query =  sprintf( "SELECT ID FROM $wpdb->posts WHERE ( post_status = 'publish' AND post_type IN %s ) AND %s", $post_types, $conditions );
			// Preliminary results based on post title, excerpt, content
			$results = $wpdb->get_results( $query );
			if ( !empty( $results ) && is_array( $results ) ) {
				foreach ( $results as $result ) {
					$include[] = intval( $result->ID );
				}
			}
			unset( $results );
		}

		$cached = wp_cache_set( $cache_key, $include, self::POST_CACHE_GROUP, self::get_cache_lifetime() );

		return $include;
	}

	/**
	 * Helper to array_map boolean and.
	 * 
	 * @param boolean $a
	 * @param boolean $b
	 * @return boolean
	 */
	public static function mand( $a, $b ) {
		return $a && $b;
	}

	/**
	 * Obtain search results based on the request parameters.
	 * 
	 * @return array
	 */
	public static function request_results() {

		global $wpdb;

		$parameters = self::get_request_parameters();
		extract( $parameters );

		$thumbnails = isset( $_REQUEST[self::THUMBNAILS] ) ? intval( $_REQUEST[self::THUMBNAILS] ) > 0 : self::DEFAULT_THUMBNAILS; 

		$cache_key = self::get_cache_key( array(
			'title'        => $title,
			'excerpt'      => $excerpt,
			'content'      => $content,
			'limit'        => $numberposts,
			'order'        => $order,
			'order_by'     => $order_by,
			'search_query' => $search_query,
			'thumbnails'   => $thumbnails
		) );

		$search_terms = explode( ' ', $search_query );

		$results = wp_cache_get( $cache_key, self::RESULT_CACHE_GROUP, true );
		if ( $results !== false ) {
			return $results;
		}

		$include = self::get_post_ids_for_request();

		$options = Search_Live::get_options();
		$description_length = isset( $options[Search_Live::DESCRIPTION_LENGTH] ) ?
			$options[Search_Live::DESCRIPTION_LENGTH] :
			Search_Live::DESCRIPTION_LENGTH_DEFAULT;

		$results = array();
		$post_ids = array();
		if ( count( $include ) > 0 ) {
			$post_types = get_post_types( array( 'public' => true ) );
			if ( empty( $post_types ) || !is_array( $post_types ) ) {
				$post_types = array( 'post', 'page' );
			}
			// Run it through get_posts() so that the normal process for obtaining
			// posts and taking account filters etc can be applied.
			$query_args = array(
				'fields'      => 'ids',
				'post_type'   => $post_types,
				'post_status' => array( 'publish' ),
				'numberposts' => $numberposts, // * not effective with include, see below (WP 3.9.1)
				'include'     => $include,
				'order'       => $order,
				'orderby'     => $order_by,
				'suppress_filters' => 0
			);
			// Filter based on language? - suppress_filters is deactivated above,
			// leaving this here as a reminder that it is also needed if the language
			// code should be taken into account:
			/*if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
				$language = isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : null;
				if ( $language !== null ) {
					$query_args['suppress_filters'] = 0;
				}
			}*/
			self::before_get_posts();
			$posts = get_posts( $query_args );
			self::after_get_posts();

			$i = 0; // used as the element index for sorting
			foreach( $posts as $post ) {

				if ( $post = get_post( $post ) ) {

					$post_ids[] = $post->ID;

					$thumbnail_url = null;
					$thumbnail_alt = null;
					if ( $thumbnail_id = get_post_thumbnail_id( $post->ID ) ) {
						if ( $image = wp_get_attachment_image_src( $thumbnail_id, Search_Live_Thumbnail::thumbnail_size_name(), false ) ) {
							$thumbnail_url    = $image[0];
							$thumbnail_width  = $image[1];
							$thumbnail_height = $image[2];

							$thumbnail_alt = trim( strip_tags( get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ) );
							if ( empty( $thumbnail_alt ) ) {
								if ( $attachment = get_post( $thumbnail_id ) ) {
									$thumbnail_alt = trim( strip_tags( $attachment->post_excerpt ) );
									if ( empty( $thumbnail_alt ) ) {
										$thumbnail_alt = trim( strip_tags( $attachment->post_title ) );
									}
								}
							}
						}
					}
					// consider using the placeholder image
					if ( $thumbnail_url === null ) {
						$placeholder = Search_Live_Thumbnail::get_placeholder_thumbnail();
						if ( $placeholder !== null ) {
							list( $thumbnail_url, $thumbnail_width, $thumbnail_height ) = $placeholder;
							$thumbnail_alt = __( 'Placeholder Image', 'search-live' );
						}
					}
					// get the description from the excerpt or the content
					$description = '';
					if ( isset( $post->post_type ) && post_type_supports( $post->post_type, 'excerpt' ) ) {
						if ( !empty( $post->post_excerpt ) ) {
							$content = self::flatten( apply_filters( 'get_the_excerpt', $post->post_excerpt ) );
							$description = wp_trim_words( $content, $description_length, ' &hellip;' );
						}
					}
					if ( empty( $description ) ) {
						// Apply filters so that shortcodes are rendered instead
						// of being displayed as such.
						$content = $post->post_content;
						$content = apply_filters( 'the_content', $content );
						$content = str_replace( ']]>', ']]&gt;', $content );
						$content = self::flatten( $content );
						$description = wp_trim_words( $content, $description_length, ' &hellip;' );
					}
					// compose the result entry
					$results[$post->ID] = array(
						'id'          => $post->ID,
						'result_type' => 'post',
						'type'        => $post->post_type,
						'url'         => get_permalink( $post->ID ),
						'title'       => get_the_title( $post ),
						'description' => $description,
						'i'           => $i
					);
					if ( $thumbnails ) {
						if ( $thumbnail_url !== null ) {
							$results[$post->ID]['thumbnail']        = $thumbnail_url;
							$results[$post->ID]['thumbnail_width']  = $thumbnail_width;
							$results[$post->ID]['thumbnail_height'] = $thumbnail_height;
							if ( !empty( $thumbnail_alt ) ) {
								$results[$post->ID]['thumbnail_alt'] = $thumbnail_alt;
							}
						}
					}
					$i++;
					// Cap the results included as the numberposts parameter
					// is not taken into account if we also provide the include
					// parameter:
					if ( $i >= $numberposts ) {
						break;
					}

					unset( $post );
				}
			}
			unset( $posts );
			// reestablish the order of elements
			usort( $results, array( __CLASS__, 'usort' ) );
		}
		$cached = wp_cache_set( $cache_key, $results, self::RESULT_CACHE_GROUP, self::get_cache_lifetime() );
		return $results;
	}

	/**
	 * Computes a cache key based on the parameters provided.
	 * 
	 * @param array $parameters
	 * @return string
	 */
	public static function get_cache_key( $parameters ) {
		return md5( implode( '-', $parameters ) );
	}

	/**
	 * Returns the cache lifetime for stored results in seconds.
	 * @return int
	 */
	public static function get_cache_lifetime() {
		$l = intval( apply_filters( 'search_live_cache_lifetime', self::CACHE_LIFETIME ) );
		return $l;
	}

	/**
	 * Set the language if specified in the request.
	 * 
	 * @param string $lang
	 * @return string
	 */
	public static function icl_set_current_language( $lang ) {
		$language = isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : null;
		if ( $language !== null ) {
			$lang = $language;
		}
		return $lang;
	}

	/**
	 * Index sort.
	 * 
	 * @param array $e1
	 * @param array $e2
	 * @return int
	 */
	public static function usort( $e1, $e2 ) {
		return $e1['i'] - $e2['i'];
	}

	/**
	 * Reduces content to flat text only.
	 *
	 * @param string $content
	 * @return string
	 */
	private static function flatten( $content ) {
		// Add space between potential adjacent tags so the content
		// isn't glued together after applying wp_strip_all_tags().
		$content = str_replace( '><', '> <', $content );
		$content = wp_strip_all_tags( $content, true );
		$content = preg_replace('/\n+|\t+|\s+/', ' ', $content );
		$content = trim( $content );
		return $content;
	}

	/**
	 * Used to temporarily remove the WPML query filter on posts_where.
	 */
	private static function before_get_posts() {
		global $wpml_query_filter, $search_live_removed_wpml_query_filter;
		if ( isset( $wpml_query_filter ) ) {
			if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
				$language = isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : null;
				if ( $language === null ) {
					$search_live_removed_wpml_query_filter = remove_filter( 'posts_where', array( $wpml_query_filter, 'posts_where_filter' ), 10, 2 );
				}
			}
		}
	}

	/**
	 * Reinstates the WPML query filter on posts_where.
	 */
	private static function after_get_posts() {
		global $wpml_query_filter, $search_live_removed_wpml_query_filter;
		if ( isset( $wpml_query_filter ) ) {
			if ( $search_live_removed_wpml_query_filter ) {
				if ( has_filter('posts_where', array( $wpml_query_filter, 'posts_where_filter' ) ) === false ) {
					add_filter( 'posts_where', array( $wpml_query_filter, 'posts_where_filter' ), 10, 2 );
				}
			}
		}
	}
}
Search_Live_Service::init();
