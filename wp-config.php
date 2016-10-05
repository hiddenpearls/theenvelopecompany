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

define( 'DB_NAME', 'arizona-envelope-prod' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         '_hqmi>w>|6RS{R!~SFieeLH]bJ[}9/-64WzL2<-Z<UJyWd}puU!evq)U?nE~0!6o');

define('SECURE_AUTH_KEY',  '6:N-P<O5G#vk0-oT+~ZzlnBSh`>UiDSD$}%+;-yaChu+&6F(fe_:<[/>a+QIW+sh');

define('LOGGED_IN_KEY',    'x<c* Y-ppE-FZv5s],CA!f[i5{=x`Z?6X+3G6GnL6`iM~aYi^{nGbZ-==~.({`;*');

define('NONCE_KEY',        'uQ<r2vm2eqZo^^-9ha8d;zztjz]%jaK9Y5hT>t`;xkw?ccFOPY;&8`:d{NZ(<M$D');

define('AUTH_SALT',        'OR1+hXe@ld=r&4/YIUFAliu[tQ#y<tGdDkSKyRYejxe 1$q%fT:}[A_yTfXKp$?J');

define('SECURE_AUTH_SALT', 'gGMav%s`]0-V(*M;:%N@J|<^MrNyme=qI-&-c}-O-*eMHQ8r997n^0:IJ+%ZT3}V');

define('LOGGED_IN_SALT',   ' U#4VrzlFPz=7|=DvyL[|{FjMKPK(+Qn.&D00o+ZfTG94Pl?(2[V.: 9s+6KU*/e');

define('NONCE_SALT',       '}e08@]_i/kO(j%s4:UdEus+$~Pbp+<%+l|2|Z0u[A*+ce42SDY+)_gKL/]n|]6oE');


/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


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
define('WP_DEBUG', true);

ini_set("memory_limit","128M");

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
