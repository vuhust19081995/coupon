<?php

// BEGIN iThemes Security - Do not modify or remove this line
// iThemes Security Config Details: 2
define( 'DISALLOW_FILE_EDIT', true ); // Disable File Editor - Security > Settings > WordPress Tweaks > File Editor
// END iThemes Security - Do not modify or remove this line

define( 'ITSEC_ENCRYPTION_KEY', 'W2h7emZxIHxHdnxMJVZMVWRSeCt9XjBGfld+WE8zRTIlfjF3Tnc8eVZKclA8dDN4SW1rLS5ETD5eWE00O1U3ZQ==' );

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'coupon-db' );

/** Database username */
define( 'DB_USER', 'root' );
// define( 'DB_USER', 'vunv_coupon' );

/** Database password */
define( 'DB_PASSWORD', 'root' );
// define( 'DB_PASSWORD', 'vunvcouponpass' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '+TM7yz0FHNq}C:x{-|5-N2C~1BBG3xejv8:]LG!Y7fu0dOL3E5s}S4eAXy+>/S)F');
define('SECURE_AUTH_KEY',  '7=/2Nd?]U3C`(@LwDc+5k70`zm=8(numcZMnrRr7w*^3GvmPe1byfw*bH @HmQ>/');
define('LOGGED_IN_KEY',    '{Qr>!KI>>cwu+7<-c1;t/7-2sd|fm>3+P/+DXqQ2%J(~-Gz22bspze1?j6 hM5Y9');
define('NONCE_KEY',        '>|hv[wCK|=|Xh&0)a*B+]-L|dVr|*UCGOypX`luT}g+GTi]6rBcvxu1cV]l*?rI*');
define('AUTH_SALT',        'A>YNC!6-78R++d2Z/+yK~G~~0us&vrs1k~`zK8n[Vx8:S@X}nx!F:RN6m2m3.8Aa');
define('SECURE_AUTH_SALT', 'J{!1!u;{u%-F$n|%WI@&n7Ur6BC5@Ck|h14_vrABTgVjT:n%+2^x@f$.|r[g,{s.');
define('LOGGED_IN_SALT',   'B|SyOkq-CGa{R|uUs2NGk|E#KX[]kd:b=|+H|d}7Q1+6OcjmF&q}_/nq#H@?FW^&');
define('NONCE_SALT',       'J2{;<VAY8;FIww%Qy+dyt8(}&xD-+d|H7ci9FeR5br04X,o0jpv|_!Z$eyP|Os@^');
/**#@-*/

/**
 * WordPress database table prefix.
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
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
//http://localhost/wp-admin/admin.php?page%20=app-setup&firstrun%20=1