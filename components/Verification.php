<?php namespace Winterpk\Wordpress\Components;

// Wordpress user class
use Winterpk\Wordpress\Classes\wpUser;
use Redirect;
class Verification extends \Cms\Classes\ComponentBase
{
	/**
	 * Contains an instance of the wordpress user class
	 * @var object
	 */
	private $_user;
	
	public function componentDetails() {
        return [
            'name'        => 'Verification Page',
            'description' => 'Put this component on the verification page'
        ];
    }
	
	public function defineProperties() {
		return [
			'redirect' => [
				 'title'             => 'Redirect Page',
				 'description'       => 'Url to redirect to after verification',
				 'default'           => '/dashboard',
				 'type'              => 'string',
				 'required' 		 => 'true',
				 'validationMessage' => 'This field is required',
			],
		];
	}
	
	/**
	 * Add styles and javascript not working properply
	 * This is in the default.html now
	 * 
	 */
	public function onRun() {
		$this->_user = wpUser::instance();
		$get = get();
		
		if (isset($get['email']) &&  filter_var($get['email'], FILTER_VALIDATE_EMAIL)){
			$email = $get['email'];
		}
		if (isset($get['key']) && (strlen($get['key']) == 32)) {
			$key = $get['key'];
		}
		if (isset($email) && isset($key)) {
			$check = $this->_user->validate_user($email, $key);
			return Redirect::to($this->property('redirect'));
		} else {
			return Redirect::to('/');
		}
	}
}
