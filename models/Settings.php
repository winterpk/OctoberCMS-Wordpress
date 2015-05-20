<?php namespace Winterpk\Wordpress\Models;

use Model; 

class Settings extends Model{
	
    public $implement = ['System.Behaviors.SettingsModel'];
     
    public $settingsCode = 'wordpress_components';
 
    public $settingsFields = 'fields.yaml';
}