<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'premiu35_gm');

/** MySQL database username */
define('DB_USER', 'premiu35_gm');

/** MySQL database password */
define('DB_PASSWORD', 'K442pa)RS.');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '(;Cjy6YHKIP~xrznyx:Bk5c.L&^DKhnq5J*)0p>^BogQfg*H<YhMb[Ycn~WGZ+yx');
define('SECURE_AUTH_KEY',  'NI*Bb 6*H.a#f2@p4?G#$bQ^?2?.Pk6XgIxCg1/jDahvqf>,:$-A>XdmrvlRJ(Jp');
define('LOGGED_IN_KEY',    'Fw^;N4Hr[[N{RPYOXc-_06{+%1E,xU;c|8An8k:L3B6F9w#Q~mg{oM_:3o}`xw/9');
define('NONCE_KEY',        'DrpKSVs<nkFrj5ru9n_!H(E$81<+&qItG9qY*Pn-5f,[F.&uLsLExNmW}lFjVHH ');
define('AUTH_SALT',        '5ic3:;+Lcd!@JVv~MTDho~#W#~Da7[(nckqpsSDFD1tEhtc@B675c9iM#$!|7A{O');
define('SECURE_AUTH_SALT', '_f5FE,2Feu^~a0 4*zWkglr5FB+#5J>ObbHB$uYNn_E|9@s{oBGxIK)XCv3s`H$!');
define('LOGGED_IN_SALT',   '!H}]LoMr_F/Y e4T2Mj-@7DW{ZEn,!^_af=XPIj*H~[Y71XK`eM.20u*fe<0$jUZ');
define('NONCE_SALT',       ':$hwwd_ .Mc VV&d28U#H14z=mP(5(SffR#^^}PMZLdbqtibMl(1eRR3M:oS|wqz');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* Multisite */
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', true);
define('DOMAIN_CURRENT_SITE', 'glowmenus.com');
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);



/* That's all, stop editing! Happy blogging. */


/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
