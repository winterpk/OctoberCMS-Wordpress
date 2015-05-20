<?php namespace Winterpk\Wordpress;
use System\Classes\PluginBase;
use App;

/**
 * Wordpress Plugin Information File
 */
class Plugin extends PluginBase
{
	
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Wordpress Components',
            'description' => 'Provides functionality for various Wordpress components such as authentication',
            'author'      => 'Winter King',
            'icon'        => 'icon-wordpress'
        ];
    }
	
	public function boot()
	{
		$get = get();
		if ( ! empty($_GET['verify'])) {
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
		// Register Auth service provider
		App::register('Winterpk\Wordpress\Classes\AuthServiceProvider');
	}
	
	public function registerSchedule($schedule)
	{
		$schedule->call(function() {
			mail('winterpk@gmail.com', 'test', 'test');
		})->everyFiveMinutes();
	}
	
	public function cleanup()
	{
		mail('winterpk@gmail.com', 'test', 'test');
	}
	
	public function registerComponents()
	{
		return [
			//'Winterpk\Wordpress\Components\Login' => 'login',
			//'Winterpk\Wordpress\Components\Register' => 'register',
			'Winterpk\Wordpress\Components\Session' => 'session',
			'Winterpk\Wordpress\Components\PasswordRecovery' => 'passwordrecovery',
			'Winterpk\Wordpress\Components\Combo' => 'combo',
			'Winterpk\Wordpress\Components\Verification' => 'verification',
		];
	}
	
	public function registerSettings()
	{
	    return [
	        'settings' => [
	            'label'       => 'Wordpress Components',
	            'description' => 'Wordpress database settings.',
	            'icon'        => 'icon-wordpress',
	            'class'       => 'Winterpk\Wordpress\Models\Settings',
	            'order'       => 1
	        ]
	    ];
	}

}
