<?php namespace Winterpk\Wordpress\Classes;


use Illuminate\Support\ServiceProvider;


class AuthServiceProvider extends ServiceProvider {

	public function register()
	{
		// Check for rainlab user 
		$this->app->bind('auth', 'Winterpk\Wordpress\Classes\User');
	}
}
