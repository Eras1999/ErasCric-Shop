<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
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
define( 'DB_NAME', 'erasCric' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'rIp3K julaI-pS4GK{Ji=4; c8)}}J,K.2qn%uCAul~NnAQ*Zj*d(K6}34jdXg){' );
define( 'SECURE_AUTH_KEY',  'LGNW&o4OiR%!bUDno<FXNEV3J?%HB9L5rdjJ~,@txl-!.$uhVC~.TFSb^B4ZHvvB' );
define( 'LOGGED_IN_KEY',    '2diST/){ySz4G7cZJR$~Z2D8a!%r~Sx*C.]&rJ?rGcMWq{[ _4B{ddqc7y.1uz3O' );
define( 'NONCE_KEY',        'tr5F:Q_q;h=5d}0ie@0*iaG*l$BIy72_ACN}:`2}zvE?Zq4>]t:_H4#>)bN#|lE1' );
define( 'AUTH_SALT',        ']vCy`h&^jo;YOWi^#/OC#6s+b7OJ&2H<!rp|p<:U%),gFfg?KKk,qqZioF;D`Lk3' );
define( 'SECURE_AUTH_SALT', 'uwc.fg?SSO_!F9#JVXRfLYsFhpY8JA3A~9&nFvE6IX#?p#<.#0TBa$iWTx9)~Fca' );
define( 'LOGGED_IN_SALT',   'u9JH-&A<JnZ$/Xx3I,~,{o#m`8{E-.+ndTAS=)F}q&mY%r<2&!aI~n@@<{))wQw?' );
define( 'NONCE_SALT',       'kRK6wA9Xs-nm#7FJy2?-*BvP&~LTkuQN47Kjc?_(U>5,,ker^E?rEahS_8rX{@g,' );

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
