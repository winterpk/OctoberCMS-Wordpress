<?php namespace Winterpk\Wordpress\Components;

use Cms\Classes\ComponentBase;
// Wordpress user class
use Winterpk\Wordpress\Classes\wpUser;
use Validator;
use Flash;
use Redirect;

class Combo extends ComponentBase
{
	
	public $login_rules = array(
		'type' => 'required',
		'username' => 'required',
		'password' => 'required',
	);	
	
	public $register_rules = array(
		'type' => 'required',
		'username' => 'required|min:3',
		'password' => 'required|min:5|confirmed',
		'password_confirmation' => 'required',
		'email' => 'required|email',
	);
	
	public $messages = array(
		'required' => 'Required',
		'email' => 'Invalid Email',
	);
	
    public function componentDetails() {
        return [
            'name'        => 'Combo Form',
            'description' => 'Drops in a combination register / signup form'
        ];
    }
	
	/**
	 * Ajax handler
	 * 
	 */
	public function onSubmit() {
		$this->_user = wpUser::instance();
		$post = post();
		
		// Check type
		switch ($post['type']) {
			case 'register':
				
				// Validate input
				$validation = Validator::make($post, $this->register_rules, $this->messages);
				if ($validation->fails()) {
					return array('errors' => $validation->errors()->toArray());
				}
				
				// Register the user
				$check = $this->_user->register_user($post);
				if (isset($check['errors'])) {
					return $check;
				}
				
				//return Redirect::to($this->property('redirect_registration'));
				break;
			case 'login':
				
				// Validate input
				$validation = Validator::make($post, $this->login_rules, $this->messages);
				
				if ($validation->fails()) {
					return array('errors' => $validation->errors()->toArray());
				}
				
				$creds = array(
					'user_login' => $post['username'],
					'user_password' => $post['password'],
				);
				if (isset($post['remember_me'])) {
					$check = $this->_user->login($creds, true);	
				} else {
					$check = $this->_user->login($creds);
				}
				if (isset($check['errors'])) {
					return $check;
				} else {
					return Redirect::to($this->property('redirect_login'));	
				}
				break;
		}
		
		// How to redirect
		//return Redirect::to('http://google.com');
		
		return false;
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
			'redirect_login' => [
				 'title'             => 'Login Redirect',
				 'description'       => 'The user will be redirected to this page after a successful login',
				 'default'           => '/dashboard',
				 'type'              => 'string',
				 'required' 		 => 'true',
				 'validationMessage' => 'This field is required',
			],
			'redirect_registration' => [
				 'title'             => 'Login Registration',
				 'description'       => 'The user will be redirected to this page after a successful registration',
				 'default'           => '/dashboard',
				 'type'              => 'string',
				 'required' 		 => 'true',
				 'validationMessage' => 'This field is required',
			]
		];
	}
	
	/**
	 * Add styles and javascript not working properply
	 * This is in the default.html now
	 * 
	 */
	public function onRun() {
		//$this->addJs('/plugins/winterpk/wordpress/assets/global/js/global.js'); // This is included in the theme now
		$this->addCss('/plugins/winterpk/wordpress/assets/global/css/global.css');
		$this->addJs('/plugins/winterpk/wordpress/assets/combo/js/combo.js');
		$this->addCss('/plugins/winterpk/wordpress/assets/combo/css/combo.css');
	}
	
	public function onRender()
	{
		$this->page->password_recovery_link = $this->property('password_recovery_page');
	}
}
