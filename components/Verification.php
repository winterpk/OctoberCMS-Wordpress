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
		
	}
}
