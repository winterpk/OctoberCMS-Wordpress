<?php
/**
 * Plugin Name: Simple Email Verification
 * Plugin URI: http://arr.ae
 * Description: Adds email verification to registration
 * Version: The plugin's version number. Example: 1.0.0
 * Author: winterpok
 * Author URI: http://arr.ae
 * License: GPL2
 */
 /*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/**
 * Activation function 
 * Creates the 'unverified' role type and sets it to the default
 * 
 */
register_activation_hook(__FILE__, 'sevActivate');  
function sevActivate() {
	add_role('unverified', __('Unverfied'));
	update_option('default_role', 'unverified');
	
	// Flush rewrite rules on activation
	flush_rewrite_rules();
}

/**
 * Deactivation function
 * Removes the unverified role
 * 
 */
register_deactivation_hook( __FILE__, 'sevDeactivate');
function sevDeactivate() {
	remove_role('unverified');
	update_option('default_role', 'subscriber');
}

// Redefine user notification function
if ( !function_exists('wp_new_user_notification') ) {

	function wp_new_user_notification( $user_id, $plaintext_pass = '', $verify = true ) {
		$host = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
		if ($verify) {
			$user = new WP_User( $user_id );
			$key = md5(rand(0,1000));
			add_user_meta($user->ID, '_validation_key', $key);
			$user_login = stripslashes( $user->user_login );
			$user_email = stripslashes( $user->user_email );
			$message  = sprintf( __('New user registration on %s:'), $_SERVER['HTTP_HOST'] ) . "\r\n\r\n";
			$message .= sprintf( __('Username: %s'), $user_login ) . "\r\n\r\n";
			$message .= sprintf( __('E-mail: %s'), $user_email ) . "\r\n";
			@wp_mail(
				get_option('admin_email'),
				sprintf(__('[%s] New User Registration'), get_option('blogname') ),
				$message
			);
			if ( empty( $plaintext_pass ) )
				return;
			$message  = __('Hi there,') . "\r\n\r\n";
			$message .= sprintf( __("Welcome to %s!"), $_SERVER['HTTP_HOST']) . "\r\n\r\n";
			$message .= 'Click on the following link to login:' . "\r\n\r\n";
			//$host = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
			//$message .= $host . '/verification?email=' . urlencode($user_email) . '&key=' . $key . "\r\n\r\n";
			$hash = urlencode(base64_encode($user_email . '|' . $key));
			$message .= $host . '?verify=' . $hash . "\r\n\r\n";
			$message .= sprintf( __('If you have any problems, please contact me at %s.'), get_option('admin_email') ) . "\r\n\r\n";
			wp_mail(
				$user_email,
				sprintf( __('[%s] account activation'), get_option('blogname') ),
				$message
			);
		} else {
			$user = get_userdata( $user_id );

			// The blogname option is escaped with esc_html on the way into the database in sanitize_option
			// we want to reverse this for the plain text arena of emails.
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
			$message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
			$message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";
			@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);
			if ( empty($plaintext_pass) )
				return;
			$message  = sprintf(__('Username: %s'), $user->user_login) . "\r\n";
			$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
			$message .= $host . "\r\n";
			
			// Only send the user verification email if they have an email set up
			if (isset($user->user_email) && ! empty($user->user_email)) {
				wp_mail($user->user_email, sprintf(__('[%s] Your username and password'), $blogname), $message);	
			}
		}
	}
}

/**
 * Add password to registration form
 * 
 */
add_action( 'register_form', 'sevShowPasswordField' );
function sevShowPasswordField() {
?>
	<p>
		<label for="password">Password<br/>
		<input id="password" class="input" type="password" size="25" value="" name="password" />
		</label>
	</p>
	<p>
		<label for="repeat_password">Repeat password<br/>
		<input id="repeat_password" class="input" type="password" size="25" value="" name="repeat_password" />
		</label>
	</p>
<?php
}

/**
 * Check registration form for errors
 * 
 */
add_action( 'register_post', 'sevCheckPasswordField' );
function sevCheckPasswordField() {
	if ( $_POST['password'] !== $_POST['repeat_password'] ) {
		$errors->add( 'passwords_not_matched', "<strong>ERROR</strong>: Passwords must match" );
	}
	if ( strlen( $_POST['password'] ) < 8 ) {
		$errors->add( 'password_too_short', "<strong>ERROR</strong>: Passwords must be at least eight characters long" );
	}
}

/**
 * Remove "A password will be e-mailed to you." message
 * 
 */
add_filter( 'gettext', 'sevEditPasswordEmaiText' );
function sevEditPasswordEmaiText($text) {
	if ($text == 'A password will be e-mailed to you.') {
		$text = 'If you leave password fields empty one will be generated for you. Password must be at least eight characters long.';
	}
	return $text;
}

/**
 * Store the new password
 * 
 */
add_action('user_register', 'sevRegisterPassword');
function sevRegisterPassword($user_id){
	$userdata = array();
	$userdata['ID'] = $user_id;
	if ($_POST['password'] !== '') {
		$userdata['user_pass'] = $_POST['password'];
	}
	$new_user_id = wp_update_user($userdata);
}

/**
 * Create link for activation by updating rewrite rules
 * 
 */
add_action('wp_loaded','sevFlushRules');
function sevFlushRules()
{
	$rules = get_option('rewrite_rules');
	if (!isset($rules['sev/(.*?)/(.+?)'])) {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}
}

add_filter('query_vars','sevInsertQueryVars');
function sevInsertQueryVars($vars)
{
	array_push($vars, 'email', 'key');
    return $vars;
}

function sevIncludeActivationPage($template)
{
	global $wp_query;
	if ($wp_query->query_vars['name'] == 'sev') {
		if (file_exists(get_stylesheet_directory() . '/sev-activate.php')) {
			return get_stylesheet_directory() . '/sev-activate.php';
		} else {
			$dir = plugin_dir_path( __FILE__ );
			return $dir . 'sev-activate.php';	
		}
	}

    return $template;
}
function sevInit()
{
	add_filter('template_include', 'sevIncludeActivationPage');
	add_filter('init', 'sevRewriteRules');
}
add_action('plugins_loaded', 'sevInit');

/**
 * Verification function looks for $_GET['verify'] and completes email verification
 */
function sev_verify()
{
	if (!empty($_GET['verify'])) {
		$data = explode('|', base64_decode(urldecode($_GET['verify'])));
		if (!$data) {
			return false;
		}
		if (count($data) != 2) {
			return false;
		}
		$email = $data[0];
		$key = $data[1];
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
			return false;
		}
		if (empty($key) || strlen($key) != 32) {
			return false;
		}
		
		$user = get_user_by('email', $email);
		if (!$user) {
			wp_redirect(home_url());
		}
		$db_key = get_user_meta($user->ID, '_validation_key');
		delete_user_meta($user->ID, '_validation_key');
		if (!empty($db_key[0]) && ($db_key[0] == $key)) {
			wp_update_user(array('ID' => $user->ID, 'role' => 'subscriber'));
			$creds = array();
			$creds['user_login'] = $user->data->user_login;
		    wp_set_current_user($user->ID, $user->user_login);
		    wp_set_auth_cookie($user->ID);
		    do_action('wp_login', $user->user_login);
		} else {
			wp_redirect(home_url());
		}
	}
}
add_action('wp_loaded', 'sev_verify');

