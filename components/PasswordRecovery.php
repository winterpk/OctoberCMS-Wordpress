<?php namespace Winterpk\Wordpress\Components;

use Cms\Classes\ComponentBase;
use Winterpk\Wordpress\Classes\wpUser;
use Validator;
use Redirect;

class PasswordRecovery extends ComponentBase
{
	
	public $recovery_rules = array(
		'login' => 'required',
	);
	
	public $update_rules = array(
		'password' => 'required|confirmed|min:5',
		'password_confirmation' => 'required',
		'key' => 'required'
	);
	
	public $messages = array(
		//'required' => 'Required',
	);
	
	private $_user;
	
    public function componentDetails()
    {
        return [
            'name'        => 'Password Recovery',
            'description' => 'Drop this onto a template to include a password recovery form'
        ];
    }
	
	// Ajax handler
	public function onRecovery() {
		$this->_user = wpUser::instance();
		
		// Attempt to load up a user by email or username
		$post = post();
		$validation = Validator::make($post, $this->recovery_rules, $this->messages);
		if ($validation->fails()) {
			return array('errors' => $validation->errors()->toArray());
		}
		$login = $post['login'];
		
		// Attempt to load user by email first
		$user = $this->_user->get_user_by('email', $login);
		if ( ! $user) {
			
			// Attempt to load user by username
			$user = $this->_user->get_user_by('login', $login);
			if ( ! $user) {
				return array('errors' => array('login' => array('Invalid username or email')));
			}
		}
		
		// We have the user so generate a key and store in the user meta
		$key = md5(uniqid(rand(), true));
		
		// Remove any old keys
		$this->_user->delete_metadata('user', $user->ID, '_recovery_key', '', true);
		
		// Create new key
		$this->_user->add_user_meta($user->ID, '_recovery_key', $key);
		
		// Create the validation link and send email
		$host = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
		$link = $host . $this->property('password_recovery_page') . "?key=".$reset['key'];
		$user_login = stripslashes($user->data->user_login);
		$user_email = stripslashes($user->data->user_email);
		$message  = sprintf( __('User password request on %s:'), $host ) . "\r\n\r\n";
		$message .= sprintf( __('Username: %s'), $user_login ) . "\r\n\r\n";
		$message .= sprintf( __('E-mail: %s'), $user_email ) . "\r\n\r\n";
		@wp_mail(
			get_option('admin_email'),
			sprintf(__('[%s] Password Reset Request'), $_SERVER['HTTP_HOST'] ),
			$message
		);
		$message  = __('Hi there,') . "\r\n\r\n";
		$message .= sprintf( __("You requested a password reset for: %s"), $_SERVER['HTTP_HOST']) . "\r\n\r\n";
		$message .= __("Click the link below to reset your password.") . "\r\n\r\n";
		$message .= $host . $this->property('password_recovery_page') . '?key=' . $key . "\r\n\r\n";
		$message .= sprintf( __('If you have any problems, please contact me at %s.'), get_option('admin_email') ) . "\r\n\r\n";
		wp_mail(
			$user_email,
			sprintf( __('[%s] password reset'), $_SERVER['HTTP_HOST'] ),
			$message
		);
		return array('#password-recovery-forms' => $this->renderPartial('passwordrecovery::checkemail'));
	}

	public function onUpdate() {
		$this->_user = wpUser::instance();
		// Load user by meta key and update the password if they validate
		$post = post();
		$validation = Validator::make($post, $this->update_rules, $this->messages);
		if ($validation->fails()) {
			return array('errors' => $validation->errors()->toArray());
		}
		$user = $this->_user->get_users(array('meta_key' => '_recovery_key', 'meta_value' => $post['key']));
		if ( ! $user) {
			return array('#password-recovery-forms' => $this->renderPartial('passwordrecovery::error'));
		}
		
		// Update password and delete the reset key
		$this->_user->update_password($post['password'], $user[0]->ID);
		$this->_user->delete_metadata('user', $user[0]->ID, '_recovery_key', '', true);
		
		return array('#password-recovery-forms' => $this->renderPartial('passwordrecovery::complete'));
	}
	
	public function onTest() {
		return Redirect::to('/');
	}
	
    public function defineProperties() {
		return [
			'password_recovery_page' => [
				 'title'             => 'Password Recovery Page',
				 'description'       => 'This is needed to redirect to the forget password page',
				 'default'           => '/password-recovery',
				 'type'              => 'string',
				 'required' 		 => 'true',
				 'validationMessage' => 'This field is required',
			], 
			'404_page' => [
				 'title'             => '404 Page',
				 'description'       => 'This is needed to redirect to the 404 page',
				 'default'           => '/404',
				 'type'              => 'string',
				 'required' 		 => 'true',
				 'validationMessage' => 'This field is required',
			]
		];
	}
	
	public function onRun() {
		$this->_user = wpUser::instance();
		$get = get();
		if (isset($get['key'])) {
			
			// Attempt to load the user by the key
			$user = $this->_user->get_users(array('meta_key' => '_recovery_key', 'meta_value' => $get['key']));
			if ( ! $user) {
				return Redirect::to($this->property('404_page'));
			}
		}
		$this->addJs('/plugins/winterpk/wordpress/assets/global/js/global.js');
		$this->addCss('/plugins/winterpk/wordpress/assets/global/css/global.css');
		$this->addJs('/plugins/winterpk/wordpress/assets/passwordrecovery/js/password-recovery.js');
		$this->addCss('/plugins/winterpk/wordpress/assets/passwordrecovery/css/password-recovery.css');
	}
	
	public function onRender()
	{
		$this->_user = wpUser::instance();
		$get = get();
		if (isset($get['key'])) {
			$this->page->key = $get['key'];
		}
	}
}