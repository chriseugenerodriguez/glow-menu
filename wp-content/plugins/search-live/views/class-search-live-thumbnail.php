<?php
/**
 * class-search-live-thumbnail.php
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
 * Thumbnail handling for search thumbnails.
 */
class Search_Live_Thumbnail {

	const THUMBNAIL                 = 'search-live-thumbnail';
	const THUMBNAIL_WIDTH           = 'search-live-thumbnail-width';
	const THUMBNAIL_HEIGHT          = 'search-live-thumbnail-height';
	const THUMBNAIL_DEFAULT_WIDTH   = 32;
	const THUMBNAIL_DEFAULT_HEIGHT  = 32;
	const THUMBNAIL_MAX_DIM         = 1024;
	const THUMBNAIL_CROP            = 'search-live-thumbnail-crop';
	const THUMBNAIL_DEFAULT_CROP    = true;
	const THUMBNAIL_USE_PLACEHOLDER = 'search-live-thumbnail-placeholder';
	const THUMBNAIL_USE_PLACEHOLDER_DEFAULT = true;

	/**
	 * Adds our filters and actions.
	 */
	public static function init() {
		add_action( 'after_setup_theme', array( __CLASS__, 'after_setup_theme' ) );
		add_filter( 'image_downsize', array( __CLASS__, 'image_downsize' ), 10, 3 );
	}

	/**
	 * Registers the thumbnail image size.
	 * Adds the image_downsize filter.
	 */
	public static function after_setup_theme() {
		// add the thumbnail image size
		$options = Search_Live::get_options();
		$thumbnail_width   = isset( $options[self::THUMBNAIL_WIDTH] ) ? $options[self::THUMBNAIL_WIDTH] : self::THUMBNAIL_DEFAULT_WIDTH;
		$thumbnail_height  = isset( $options[self::THUMBNAIL_HEIGHT] ) ? $options[self::THUMBNAIL_HEIGHT] : self::THUMBNAIL_DEFAULT_HEIGHT;
		$thumbnail_crop    = isset( $options[self::THUMBNAIL_CROP] ) ? $options[self::THUMBNAIL_CROP] : self::THUMBNAIL_DEFAULT_CROP;
		add_image_size( self::thumbnail_size_name(), intval( $thumbnail_width ), intval( $thumbnail_height ), $thumbnail_crop );
	}

	/**
	 * Returns the current size name for search result thumbnails.
	 * @return string
	 */
	public static function thumbnail_size_name() {
		$options = Search_Live::get_options();
		$thumbnail_width   = isset( $options[self::THUMBNAIL_WIDTH] ) ? $options[self::THUMBNAIL_WIDTH] : self::THUMBNAIL_DEFAULT_WIDTH;
		$thumbnail_height  = isset( $options[self::THUMBNAIL_HEIGHT] ) ? $options[self::THUMBNAIL_HEIGHT] : self::THUMBNAIL_DEFAULT_HEIGHT;
		return sprintf( self::THUMBNAIL . '-%dx%d', intval( $thumbnail_width ), intval( $thumbnail_height ) );
	}

	/**
	 * Obtains or generates the thumbnail image if the search-live-thumbnail
	 * size is requested.
	 * 
	 * @param boolean $foo false
	 * @param int $id
	 * @param string $size
	 * @return array|boolean image result as array or false if it couldn't be obtained/generated
	 */
	public static function image_downsize( $foo, $id, $size ) {

		$result = false;

		if ( $size == self::thumbnail_size_name() ) {

			// really make sure we have the size defined as we're going to need it
			self::after_setup_theme();

			require_once ABSPATH . '/wp-admin/includes/image.php';

			if ( !empty( $size ) && wp_attachment_is_image( $id ) ) {
				$regenerate = false;
				// Do we have the appropriate size? 
				if ( $intermediate = image_get_intermediate_size( $id, $size ) ) {
					$img_url = $intermediate['url'];
					if ( empty( $img_url ) && !empty( $intermediate['file'] ) ) {
						$original_file_url = wp_get_attachment_url( $id );
						if ( !empty( $original_file_url ) ) {
							$img_url = path_join( dirname( $original_file_url ), $intermediate['file'] );
						}
					}
					$width = $intermediate['width'];
					$height = $intermediate['height'];
					$is_intermediate = true;
				}

				if ( isset( $img_url ) ) {
					// adjust for editor or theme
					list( $width, $height ) = image_constrain_size_for_editor( $width, $height, $size );
					$result = array( $img_url, $width, $height, $is_intermediate );

					// check the dimensions and ratios, used below
					$options = Search_Live::get_options();
					$thumbnail_width   = isset( $options[self::THUMBNAIL_WIDTH] ) ? $options[self::THUMBNAIL_WIDTH] : self::THUMBNAIL_DEFAULT_WIDTH;
					$thumbnail_height  = isset( $options[self::THUMBNAIL_HEIGHT] ) ? $options[self::THUMBNAIL_HEIGHT] : self::THUMBNAIL_DEFAULT_HEIGHT;
					$thumbnail_crop    = isset( $options[self::THUMBNAIL_CROP] ) ? $options[self::THUMBNAIL_CROP] : self::THUMBNAIL_DEFAULT_CROP;

					switch( $thumbnail_crop ) {
						// crop => dimensions must match
						case true :
							if ( ( $width != $thumbnail_width ) || ( $height != $thumbnail_height ) ) {
								$regenerate = true;
							}
							break;
						// don't crop => ratios must match
						case false :
							$meta = wp_get_attachment_metadata( $id );
							$r1 = round( floatval( $width ) / floatval( $height > 0 ? $height : 1 ), 3 );
							$r2 = round( floatval( $meta['width'] ) / floatval( $meta['height'] > 0 ? $meta['height'] : 1 ), 3 );
							if ( $r2 == 0 ) {
								$r2 = 0.001;
							}
							// regenerate if the ratios differ over 25%
							if ( abs( $r1 / $r2 - 1 ) > 0.25 ) {
								$regenerate = true;
							}
							break;
					}
				}

				// Generate the thumbnail image if
				// a) there was no result which means no thumbnail has been generated yet
				// b) the dimensions are off and we're getting an image that is similar in size but not matching what we need 
				if ( !$result || $regenerate ) {
					$meta = wp_get_attachment_metadata( $id );
					$upload_dir = wp_upload_dir();
					$img_file = get_attached_file( $id );
					$new_meta = wp_generate_attachment_metadata( $id, $img_file );
					wp_update_attachment_metadata( $id, $new_meta );
					// now try again
					if ( $intermediate = image_get_intermediate_size( $id, $size ) ) {
						$img_url = $intermediate['url'];
						if ( empty( $img_url ) && !empty( $intermediate['file'] ) ) {
							$original_file_url = wp_get_attachment_url( $id );
							if ( !empty( $original_file_url ) ) {
								$img_url = path_join( dirname( $original_file_url ), $intermediate['file'] );
							}
						}
						$width = $intermediate['width'];
						$height = $intermediate['height'];
						$is_intermediate = true;
					}
					if ( isset( $img_url ) ) {
						// adjust ...
						list( $width, $height ) = image_constrain_size_for_editor( $width, $height, $size );
						$result = array( $img_url, $width, $height, $is_intermediate );
					}
				}
			}
		}
		return $result;
	}

	/**
	 * Retrieve the placeholder thumbnail image and its dimensions, or null
	 * if none should be used.
	 * 
	 * @return array holding the placeholder URL, width and height in that order or null
	 */
	public static function get_placeholder_thumbnail() {
		$result = null;
		$options = Search_Live::get_options();
		$thumbnail_use_placeholder = isset( $options[self::THUMBNAIL_USE_PLACEHOLDER] ) ? $options[self::THUMBNAIL_USE_PLACEHOLDER] : self::THUMBNAIL_USE_PLACEHOLDER_DEFAULT;
		if ( $thumbnail_use_placeholder ) {
			$thumbnail_url = SEARCH_LIVE_PLUGIN_URL . '/images/placeholder.png'; // @todo use uploaded image if available instead
			$thumbnail_width   = isset( $options[self::THUMBNAIL_WIDTH] ) ? $options[self::THUMBNAIL_WIDTH] : self::THUMBNAIL_DEFAULT_WIDTH;
			$thumbnail_height  = isset( $options[self::THUMBNAIL_HEIGHT] ) ? $options[self::THUMBNAIL_HEIGHT] : self::THUMBNAIL_DEFAULT_HEIGHT;
			$result = array( $thumbnail_url, $thumbnail_width, $thumbnail_height );
		}
		return $result;
	}
}
Search_Live_Thumbnail::init();
