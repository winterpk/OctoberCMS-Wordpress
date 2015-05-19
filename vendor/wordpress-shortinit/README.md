# wordpress-4.1.1-shortinit
Wordpress 4.1.1 streamlined for shortinit. Designed to be used outside of Wordpress as a connector.
Inspired from this post: http://www.stormyfrog.com/using-wpdb-outside-wordpress-revisited/

# Installation
Download into folder and include in your script

# Example
	define('WP_DATABASE_USER', 'dbuser');
	define('WP_DATABASE_PASS', 'dbpass');
	define('WP_DATABASE_NAME', 'dbname');
	define('WP_DATABASE_HOST', 'dbhost');
	define('SHORTINIT', true);
	require_once(dirname(__FILE__).'/../vendor/wordpress-shortinit/wp-load.php');

