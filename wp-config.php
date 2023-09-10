<?php

// BEGIN iThemes Security - Do not modify or remove this line
// iThemes Security Config Details: 2
define( 'DISALLOW_FILE_EDIT', true ); // Disable File Editor - Security > Settings > WordPress Tweaks > File Editor
// END iThemes Security - Do not modify or remove this line

define( 'ITSEC_ENCRYPTION_KEY', 'Kix7R0B9eTUmc21+KTlTblRaN2ApQiU8UUQ3fSF7e3VYaW5zSHteVmRMNXtDZ3daU241O0JfYTJuME5JY0lNIw==' );

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
define( 'DB_USER', 'vunv_coupon' );

/** Database password */
define( 'DB_PASSWORD', 'vunvcouponpass' );

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
define('AUTH_KEY',         '$X0pG_uU]csZYpscEHat--$W<PE~NCvu?P(LU(9 [RC+kgPRN0F:Jm^6:h.j<$D_');
define('SECURE_AUTH_KEY',  '#2-A@%3Ht@+hMo&+b)/0xp,VWEH4gs}Ky<kP//i.8o*3bSg@9!lIz$<ACK}Ft][i');
define('LOGGED_IN_KEY',    'X^^1Z:W?x:;!dK_$-EGQZjP&Bi8;&q;}LgMMWbqLM33$aD5j-nsq2v%uAgule0Zx');
define('NONCE_KEY',        '/4f)W[_51I^P&1)|g?|6~/qb 0[o]I,%tD!Kf*^ID4K7^CA@*[NUHK;_ii&N>5Ft');
define('AUTH_SALT',        'Kj5Z|vhgE)J4Ul9_#ywwi3599`-ApA-Cy865@C6R_@tr%)vX^^gU`t.?<W+Z-}OI');
define('SECURE_AUTH_SALT', '-CmEH?3a|tzS;sz3g2QIktam+L[8H%aBl^ <S7k;+{~-)-83Xx,[aftUiN].~PDY');
define('LOGGED_IN_SALT',   'rFTj)ZwRI>+o2U1!MkxEh[r}DTJ>5#Pn(IR(STc@RcI|#4M~3H>RT6q@pyMT4/bQ');
define('NONCE_SALT',       '8<A;6%XxVf)ljI<_/`VletE|QctuZ0{N()LWWIoxlEWRpc~ARSYbdY}qg+;aB.!s');

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
