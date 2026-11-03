<?php
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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

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
define( 'AUTH_KEY',          'b.JuJj,crp3]n*<uo[ wnG)?LV4i:f_xXU~{cwj!(j/88~O>+}syrP*y{R3~)%)5' );
define( 'SECURE_AUTH_KEY',   '|u|az]BXNW%>$^2^h*HWb%%QL`S:{NzTOPKoZFRr&j+P3dMzgeV%tp[cdW|DYsra' );
define( 'LOGGED_IN_KEY',     '20(;s]oh<PL@gwhi+HM4u?ZfG#epUT`sR*ugXe~]+r,$U*=ZCvc~d3<oTN-B?^V>' );
define( 'NONCE_KEY',         '.Ur85*W+P.xK{~~V`T=s6e!uR[u>QFV?v(+hw=_w~wsSa:HE$#:<3dKgbyw-JZ ^' );
define( 'AUTH_SALT',         'U.}{7TyV;W$sKwS@Ck(oh6wt?2`r!gNV[e9;^ 8.nZ2Xv+p0] f@b.j,r;dsp16>' );
define( 'SECURE_AUTH_SALT',  'AJwZ^`W7(%&uZMR9<7^P./}1PA8Em)l3i^_H3`&8Fq4P!xMj*>y[_Txxx>|@*zjk' );
define( 'LOGGED_IN_SALT',    '>:Q]x5UFM)A-gxw<-9zzOh3~ tvJj9_5r:rH`,:Z<~i4{w]OtrW>K{w^GI{Er|pm' );
define( 'NONCE_SALT',        '?0UWVmc*#(dy4bN/0NR!P7{{Cd&1a7@D(v.MD2 Twbql7*0bUsymO9@9=xwec2F#' );
define( 'WP_CACHE_KEY_SALT', 'JVM0/mL`E3KV3n;dpoUwS~kwV$9YpX<;ba{~Ic>4CnD<RNgW/!e:5{j7ySuLjDH*' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
