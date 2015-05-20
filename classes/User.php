<?php namespace Winterpk\Wordpress\Classes;
use Winterpk\Wordpress\Models\Settings;
use Winterpk\Wordpress\Vendor\Wordpress;

/**
 * This user class is a simple wrapper used to access
 * core wordpress functionality and some additional functionality.
 * 
 * @author	Winter King winterpk@gmail.com
 */
class User {
	
	function __construct() {
		$settings = Settings::instance();
		
		// Load up wordpress with shortinit
		define('WP_DATABASE_USER', $settings->database_user);
		define('WP_DATABASE_PASS', $settings->database_pass);
		define('WP_DATABASE_NAME', $settings->database_name);
		define('WP_DATABASE_HOST', $settings->database_host);
		//define('SHORTINIT', true);
		require_once(dirname(__FILE__).'/../vendor/wordpress-shortinit/wp-load.php');
	}
	
	/**
	 * Wrapper functions
	 */  
	function logged_in()
	{
		return is_user_logged_in();
	}
	
	function wp_get_current_user()
	{
		return wp_get_current_user();
	}
	
	function delete_metadata($meta_type, $object_id, $meta_key, $meta_value = '', $delete_all = false)
	{
		return delete_metadata($meta_type, $object_id, $meta_key, $meta_value, $delete_all);
	}
	
	function logout()
	{
		wp_logout();
	}
	
	function get_user_by($key, $value)
	{
		return get_user_by($key, $value);
	}
	
	function username_exists($username)
	{
		return username_exists($username);
	}
	
	function email_exists($email)
	{
		return email_exists($email);
	}
	
	function delete_user_meta($user_id, $meta_key, $meta_value = '')
	{
		return delete_user_meta($user_id, $meta_key, $meta_value);
	}
	
	function update_user_meta( $user_id, $meta_key, $meta_value, $prev_value = '')
	{
		return update_user_meta($user_id, $meta_key, $meta_value, $prev_value);
	}
	
	function update_user($userdata)
	{
		return wp_update_user($userdata);
	}
	
	function update_password($password, $user_id)
	{
		return wp_set_password($password, $user_id);
	}
	
	function get_users($args)
	{
		return get_users($args);
	}
	
	function hash_password($password)
	{
		return wp_hash_password($password);
	}
	
	function add_user_meta($user_id, $meta_key, $meta_value)
	{
		return add_user_meta($user_id, $meta_key, $meta_value);
	}
	
	function generate_password($length = 12, $special_chars = true, $extra_special_chars = false)
	{
		return wp_generate_password($length, $special_chars, $extra_special_chars);
	}
	
	function get_user_meta($user_id, $key = '', $single = false)
	{
		return get_user_meta($user_id, $key, $single);
	}
	
	/**
	 * Higher level functions
	 */
	function get_user_by_meta_data($meta_key, $meta_value)
	{

		// Query for users based on the meta data
		$user_query = new \WP_User_Query(
			array(
				'meta_key'	  =>	$meta_key,
				'meta_value'	=>	$meta_value
			)
		);

		// Get the results from the query, returning the first user
		$users = $user_query->get_results();
		return $users[0];
	}
	
	function toArray()
	{
		$user = wp_get_current_user();
		return (array)$user->data;	
	}
	
/**
	function facebook_connected()
	{
		$user = wp_get_current_user();
		if ( ! $user) {
			return false;
		}
		$facebook_connect = get_user_meta($user->ID, '_facebook_connect', true);
		if ($facebook_connect) {
			return true;
		} else {
			return false;
		}
	}

	function twitter_connected()
	{
		$user = wp_get_current_user();
		if ( ! $user) {
			return false;
		}
		$facebook_connect = get_user_meta($user->ID, '_twitter_connect', true);
		if ($facebook_connect) {
			return true;
		} else {
			return false;
		}
	}
**/
	function force_login($user_id, $remember = false)
	{
		$first_login = get_user_meta($user_id, '_first_login', true);
		
		if ($first_login == '') {
			update_user_meta($user_id, '_first_login', 'yes');
		}
		else {
			update_user_meta($user_id, '_first_login', 'no');
		}
		
		
		update_user_meta($user_id, '_last_login', time());
		wp_set_auth_cookie($user_id, $remember);
	}
	 
	function password_reset($login) {
		
		// Attempt to load user by email first
		$user = get_user_by('email', $login);
		if ( ! $user) {
			
			// Attempt to load user by username
			$user = get_user_by('login', $login);
			if ( ! $user) {
				return false;
			}
		}

		// We have the user so generate a key and store in the user meta
		$key = md5(uniqid(rand(), true));
		add_user_meta($user->ID, '_recovery_key', $key);
		return array('key' => $key, 'email' => $user->user_email, 'username' => $user->user_login);	
	}
	
	function authenticate($username, $password) {
		$signon = wp_signon($creds);
	}
	
	function check() {
		
	}
	
	function getUser() {
		$user = wp_get_current_user();
		return (array)$user->data;	
	}
	
	function register($userdata, $verify = true) {
		$errors = array();
		
		if ( ! isset($userdata['username']) || ! isset($userdata['password']) ) {
			$errors['username'] = array('Error');
			return array('errors' => $errors);
		}
		if (username_exists($userdata['username'])) {
			$errors['username'] = array('Username already exists');
		}	
		
		if (isset($userdata['email'])) {
			if (email_exists($userdata['email'])) {
				$errors['email'] = array('Email already exists');
			}	
		}
		
		if ($errors) {
			return array('errors' => $errors);
		}
		
		$user_id = wp_create_user($userdata['username'], $userdata['password'], $userdata['email']);
		wp_set_password($userdata['password'], $user_id);
		wp_update_user(array(
			'ID' => $user_id,
			'nickname' => $userdata['username'],
		));
		$user = new \WP_User($user_id);
		if ($verify) {
			$user->set_role('unverified');	
		} else {
			$user->set_role('subscriber');
		}
		wp_new_user_notification($user_id, $userdata['password'], $verify);
		return $user_id;
	}
	
	function first_login() {
		$user = $this->wp_get_current_user();
		if (!$user) {
			return false;
		}
		else {
			$first_login = $this->get_user_meta($user->ID, '_first_login', true);
			if ($first_login == 'yes') {
				return true;
			}
			else {
				return false;
			}
		}
	}
	
	function validate_user($email, $key) {
		$user = get_user_by('email', $email);
		if ( ! $user) {
			return false;
		}
		$db_key = get_user_meta($user->ID, '_validation_key');
		delete_user_meta($user->ID, '_validation_key');
		if ($db_key && ($db_key[0] == $key)) {
			wp_update_user(array('ID' => $user->ID, 'role' => 'subscriber'));
			$creds = array();
			$creds['user_login'] = $user->data->user_login;
			
			wp_clear_auth_cookie();
		    wp_set_current_user($user->ID);
		    wp_set_auth_cookie($user->ID);
			update_user_meta($user->ID, '_first_login', 'yes');
			update_user_meta($user->ID, '_last_login', time());
		    //do_action( 'wp_login', $user->user_login );
			return true;
		} else {
			return false;
		}
	}

	function login($creds, $remember = false) {
		$signon = wp_signon($creds, $remember);
		if (is_wp_error($signon)) {
			if ($signon->errors['incorrect_username']) {
				return array('errors' => array('username' => array('Invlid username')));
			} else if ($signon->errors['incorrect_password']) {
				return array('errors' => array('password' => array('Invalid password')));
			} else {
				return array('errors' => array('username' => array('Invalid username')));
			}
			return $signon->errors;
		}
		else {
			$userdata = get_userdata($signon->ID);
			if (in_array('unverified', $userdata->roles)) {
				return array('errors' => array('username' => array('User email is unverified')));
			}
			else {
				$first_login = get_user_meta($signon->ID, '_first_login', true);
				if ($first_login == '') {
					update_user_meta($signon->ID, '_first_login', 'yes');
				}
				else {
					update_user_meta($signon->ID, '_first_login', 'no');
				}
				update_user_meta($signon->ID, '_last_login', time());
				return true;	
			}
			
			
		}
		
	}
}
