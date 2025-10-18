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
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
// ** Pilih salah satu / uncomment untuk setting server lokal/remote ** //
// ** for local server = untuk server lokal, for remote server = untuk remote/akses luar ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'official_website' ); // sesuaikan dengan nama database pada server Anda

/** Database username (for Server) */
define( 'DB_USER', 'root' ); // for local server
// define( 'DB_USER', 'remote_db' ); // for remote server

/** Database password */
define( 'DB_PASSWORD', '@Rssswebserver2025' ); // for local server
// define( 'DB_PASSWORD', '@Rsssremotedbweb' ); // for remote server

/** Database hostname */
define( 'DB_HOST', 'localhost' ); // for local server
// define( 'DB_HOST', '36.88.17.170' ); // for remote server

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
define( 'AUTH_KEY',         'r&p?78}|kGz( {|bAH~Kds7;4%$<.}<a>00~]7KSHn>f2g!kV(m]g{fc{6/[i!{5' );
define( 'SECURE_AUTH_KEY',  'laI=<eKg#R^SH1~9.CDFVkkD,F}QJ@bn86a/o*yIpc~Rp}O@PpZjsnzb^sX5:)s.' );
define( 'LOGGED_IN_KEY',    '*d^AH:!XD-H1c[(bgqbf}b:q: sAiX(8N <yA]u?rae/ndF34x?lD!)S+cOGAUw{' );
define( 'NONCE_KEY',        'Du~umOwN6LPIZI+DEQd4N,NFYZe$.@()*H6]5[c9 LG0_J:I}s}W[.bnN:4tuMb=' );
define( 'AUTH_SALT',        'm*=R5X75<2y]{,:41|tz?Zg%ialmVBkmrPZU2nl(wx-[G2]JkYl=djEu3(r=$% W' );
define( 'SECURE_AUTH_SALT', 'a*-CVI(*[&Jc^k7zW.U+04`;w&l#>8,LPIl5bT}B.=Z|IQ28pWWa#8h!!PWLF50#' );
define( 'LOGGED_IN_SALT',   'qhLWsoHl[/q0nG` hF#y+7qhkW9@hIV*2!HO`&3!V-!WD9BfF]G/3/M^<dK/%zs8' );
define( 'NONCE_SALT',       'PO66+%3xb[J^de4seyE67]3GK8&Vb99 ~kY,=mB}bd!`4O7jD)$$w^vwFuJ3aA=|' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */

/* Khusus untuk remote project */
// define( 'WP_HOME', 'http://localhost/rssstumpang' );
// define( 'WP_SITEURL', 'http://localhost/rssstumpang' );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
