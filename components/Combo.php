<?php namespace Winterpk\Wordpress\Components;

use Cms\Classes\ComponentBase;
use Winterpk\Wordpress\Facades\Auth;
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
	
    public function componentDetails()
    {
        return [
            'name'        => 'Combo Form',
            'description' => 'Drops in a combination register / signup form'
        ];
    }
	
	/**
	 * Add styles and javascript not working properply
	 * This is in the default.html now
	 * 
	 */
	public function onRun()
	{
		$this->addCss('/plugins/winterpk/wordpress/assets/global/css/global.css');
		$this->addJs('/plugins/winterpk/wordpress/assets/combo/js/combo.js');
		$this->addCss('/plugins/winterpk/wordpress/assets/combo/css/combo.css');
	}
	
	public function onRender()
	{
		$this->page->password_recovery_link = $this->property('password_recovery_page');
	}
		
	/**
	 * Ajax handler
	 * 
	 */
	public function onSubmit()
	{
		$post = post();
		if (empty($post['type'])) {
			return false;
		}
		
		// Check type
		switch ($post['type']) {
			case 'register':
				
				// Validate input
				$validation = Validator::make($post, $this->register_rules, $this->messages);
				if ($validation->fails()) {
					return array('errors' => $validation->errors()->toArray());
				}

				// Register the user
				$check = Auth::register($post, (bool)$this->property('email_verfication'));
				if (isset($check['errors'])) {
					return $check;
				}
				return true;
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
					$check = Auth::login($creds, true);	
				} else {
					$check = Auth::login($creds);
				}
				if (isset($check['errors'])) {
					return $check;
				} else {
					return Redirect::to($this->property('redirect_login'));
				}
				break;
		}
		return false;
	}
	
	public function defineProperties()
	{
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
			'email_verfication' => [
				 'title'             => 'Login Registration',
				 'description'       => 'If set to true, the user must validate their email before they can log in.',
				 'default'           => 'false',
				 'type'              => 'dropdown',
				 'options'			 => ['true'=>'Yes','false'=>'No'],
			]
		];
	}
}
