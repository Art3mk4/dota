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
define('DB_NAME', 'dota');

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

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'EVA>a(A22^Np>PPR~%.Czk3t0)+KFJQ%WsUoiI;&a}2r(mUAr|H0:-cAew<_=YJn');
define('SECURE_AUTH_KEY',  '[&y3n>5PHR]BDmVoZ !Y%)Rq%~ik.N1L}F^z.Z>^wc|r,#]svHggH*aZJX ptz=E');
define('LOGGED_IN_KEY',    '%mFmm?3eQ8-8W#in{/LCC[|lie?%KH-!>Iz;Pocs)p7%4yf}QXs|^RmP@BK|WZ6~');
define('NONCE_KEY',        'W;2zb*#ff4(.WYc|x+.dV~?2aEHBg3G)9PyN<z8)sW2!iWp{I0$|ATAWarMgZ,_y');
define('AUTH_SALT',        'lZ5M*qP5ostpmU-}h1q|1b56W7LIEnj/1xbx=D7CRy1&1RfLdI$W%hmdOjp{+ pQ');
define('SECURE_AUTH_SALT', 'y[cZ)5bu_m|x;3P an^?Zv EPaSVBh=Z0%7q%)@<&]jw6th]Q`:jp1PNG=%:!yOv');
define('LOGGED_IN_SALT',   'RnNyTHjx9c%zKt2^~GEU/2j7L?+/<sEdc8}88lKLlcLpQgX $%IyV8Aj7` xAz&m');
define('NONCE_SALT',       '@.1<h_PUd}qW/qiFi#C+)]}nW_*z}evano*^f+x}~fH3q492rZ:bL KKLa~N~7bD');

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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
