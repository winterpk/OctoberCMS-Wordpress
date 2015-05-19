<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', WP_DATABASE_NAME);

/** MySQL database username */
define('DB_USER', WP_DATABASE_USER);

/** MySQL database password */
define('DB_PASSWORD', WP_DATABASE_PASS);

/** MySQL hostname */
define('DB_HOST', WP_DATABASE_HOST);

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
define('AUTH_KEY',         'P(p+5 eqcao5}|U~RQ{5$(JksI/j1CW7Y<l>!9I*6i^dG4NHU p/Mxczs^[]zm.a');
define('SECURE_AUTH_KEY',  '(T]]Cjln6:0(-XyT*5*>nK2dTZ !V0*1`]}O|Xfh`Xj)m|-]-*8^b6Z)o:U>oP}1');
define('LOGGED_IN_KEY',    'I7H(_.+I`<mShN1;bXT^1-1<F5Yjgt2(iA.+H[C]r!Ne_^0~LP^J5zFoehMS= XK');
define('NONCE_KEY',        'n]LAQU|-E=Xsp.2;fnIVat;VIWaFutcE0O>QVG|[rK75hQ Lgn_`;22`dWg5|i;,');
define('AUTH_SALT',        '!g2-e8Dq2Poaz,aobHDX8O1IxtU=i^)!>8}O4-BSw@`Grs,-azaXFEzorf%<IWML');
define('SECURE_AUTH_SALT', 'rT|H.HhEd8_D$XdO_59=xyI^{uF#k*(En:A:C=l QU3bPx+>I$t*> R<RiVp[y}d');
define('LOGGED_IN_SALT',   '|V^u+H.2GI}%p]]4DH9=Wm}5[p:`h8zfZ~_ Xawp|93of*K;X,bB.bVIv1%29PI&');
define('NONCE_SALT',       '^_K,S$c!_/ERf#ahs+k^57[dQ?Ili2s7,n%Zb$j*R5nNQ8`77 wx^jw+.@u=mE, ');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
