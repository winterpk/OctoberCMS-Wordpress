<?php namespace Winterpk\Wordpress\Components;

use Cms\Classes\ComponentBase;
use Winterpk\Wordpress\Facades\Auth;
use Redirect;
use Validator;
use ValidationException;
class Session extends ComponentBase
{
	
	/**
	 * Logged in true or false
	 * @var	bool
	 */
	public $logged_in;
	
	/**
	 * Stores an instance of the wordpress user class
	 * @var object
	 */
	private $_user;
	
	/**
	 * Stores the WP_User object
	 * @var object
	 */
	public $wp_user;
	
    public function componentDetails()
    {
        return [
            'name'        => 'Session Component',
            'description' => 'Gets user session data and secures pages / layouts'
        ];
    }
	
	public function logged_in()
	{
		return $this->logged_in;
	}
	
	public function user()
	{
		return $this->wp_user;
	}
	
	public function user_id()
	{
		return $this->wp_user->ID;
	}
	
    public function defineProperties()
    {
        return [
			'redirect_login' => [
				'title' => 'Redirect if logged in',
				'description' => 'If this field is filled out, user will be redirected to this page if they are logged in',
				'type' => 'string',
			],
			'redirect_logout' => [
				'title' => 'Redirect if not logged in',
				'description' => 'If this field is filled out, user will be redirected to this page if they are NOT logged in',
				'type' => 'string',
			],
        ];
    }
	
	public function onLogout() {
		Auth::logout();
	}
	
	public function onRun() {
		$this->logged_in = (int)Auth::logged_in();
		if ($this->property('redirect_logout') && $this->logged_in == 0) {
			return Redirect::to($this->property('redirect_logout'));
		}
		if ($this->property('redirect_login') && $this->logged_in == 1) {
			return Redirect::to($this->property('redirect_login'));
		}
		
		$this->wp_user = Auth::wp_get_current_user();
		
		if ($this->wp_user->data) {
			$this->page['user'] = $this->wp_user->to_array();
		} else {
			$this->page['user'] = false;
		}	
	}
}