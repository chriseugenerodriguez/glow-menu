<?php
/**
 * search-live.php
 *
 * Copyright (c) 2015 - 2017 "kento" Karim Rahimpur www.itthinx.com
 * 
 * This code is released under the GNU General Public License Version 3.
 * The following additional terms apply to all files as per section
 * "7. Additional Terms." See COPYRIGHT.txt and LICENSE.txt.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * All legal, copyright and license notices and all author attributions
 * must be preserved in all files and user interfaces.
 * 
 * Where modified versions of this material are allowed under the applicable
 * license, modified version must be marked as such and the origin of the
 * modified material must be clearly indicated, including the copyright
 * holder, the author and the date of modification and the origin of the
 * modified material.
 * 
 * This material may not be used for publicity purposes and the use of
 * names of licensors and authors of this material for publicity purposes
 * is prohibited.
 * 
 * The use of trade names, trademarks or service marks, licensor or author
 * names is prohibited unless granted in writing by their respective owners.
 * 
 * Where modified versions of this material are allowed under the applicable
 * license, anyone who conveys this material (or modified versions of it) with
 * contractual assumptions of liability to the recipient, for any liability
 * that these contractual assumptions directly impose on those licensors and
 * authors, is required to fully indemnify the licensors and authors of this
 * material.
 * 
 * This header and all notices must be kept intact.
 * 
 * @author itthinx
 * @package search-live
 * @since 1.0.0
 *
 * Plugin Name: Search Live
 * Plugin URI: http://www.itthinx.com/plugins/search-live/
 * Description: Search Live supplies integrated live search facilities and advanced search features.
 * Version: 1.8.0
 * Author: itthinx
 * Author URI: http://www.itthinx.com
 * Donate-Link: http://www.itthinx.com/shop/
 * Text Domain: search-live
 * Domain Path: /languages
 * License: GPLv3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SEARCH_LIVE_PLUGIN_VERSION', '1.8.0' );
define( 'SEARCH_LIVE_PLUGIN', 'search-live' );
define( 'SEARCH_LIVE_PLUGIN_DOMAIN', 'search-live' );
define( 'SEARCH_LIVE_FILE', __FILE__ );
if ( !defined( 'SEARCH_LIVE_LOG' ) ) {
	define( 'SEARCH_LIVE_LOG', false );
}
if ( !defined( 'SEARCH_LIVE_DEBUG' ) ) {
	define( 'SEARCH_LIVE_DEBUG', false );
}
define( 'SEARCH_LIVE_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'SEARCH_LIVE_CORE_LIB', SEARCH_LIVE_CORE_DIR . 'core' );
define( 'SEARCH_LIVE_ADMIN_LIB', SEARCH_LIVE_CORE_DIR . 'admin' );
define( 'SEARCH_LIVE_VIEWS_LIB', SEARCH_LIVE_CORE_DIR . 'views' );
define( 'SEARCH_LIVE_PLUGIN_URL', plugins_url( 'search-live' ) );
require_once SEARCH_LIVE_CORE_LIB . '/class-search-live.php';
