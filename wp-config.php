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
define('DB_NAME', 'az-envelope');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/** Set max number of revisions stored in database to 5 for lower space usage */
define( 'WP_POST_REVISIONS', 5 );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '$3_A*=w@0/&3cWc#aeK7@I)Y;aGK-0ycJl{@oXTj}>kfOp|$FDav}ISx-3czYAR+');
define('SECURE_AUTH_KEY',  'T.Lu;`}is4#ozcsQYZT41=i48QnDc7DR)TGi$t092nF3P6}93=_:,Uxxe`M-GeiK');
define('LOGGED_IN_KEY',    'shU hS@D3gQ,TL2oHo%6JgiN!APx|l@}L4doj&gg<0BP{Z1$wZv$fr<+{:@.6SE>');
define('NONCE_KEY',        'OACDV0Z^+b S(vN}Ev#U,[0Ohd;H-?CdX9l:.gI`G$@+y78r[S4xBku~yKf>G,Hu');
define('AUTH_SALT',        '-@gS>WFYrwKEh|M~$wpm>zsqD*83x^e)0Y2X*Rq?xSrAAktJ#sl Z|a;G:_[8ud%');
define('SECURE_AUTH_SALT', ' ,s9fSN7e7Yeir&G6n6DO&&J5.0]2J0*b<le-w{z?VuXumka:xpT%iCO] )e~Gcp');
define('LOGGED_IN_SALT',   '>J1{_Jv,~ggFcb(|;{.P7-BVT?4]HIzK<rCn5<dm&+eH{(gcVkIw)3bNEV-eRf)y');
define('NONCE_SALT',       'd6&CzbB5@LCB>G@ o-8<4s:~[9au9CkSo|E>*!2x `*^*E6x~ R,G>4e3X>j3%L=');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'aze_';

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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
