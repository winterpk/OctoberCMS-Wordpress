# OctoberCMS-Wordpress
An OctoberCMS plugin that provides access to Wordpress with a set of components

## Description
Provides a set of components that tie into a Wordpress database to offer basic Wordpress functionality. 
It works by including the entire Wordpress CMS and connecting to a database based on a user config. So far only account functionality is available.
Uses an 'Auth' facade to provide access to the Wordpress layer, similar to how RainLab.User plugin provides access to core October Auth.
This means that this plugin CANNOT be used in combination with RainLab.User. 
 
## Features
- Combo Login and Registration Form
- Password Recovery form
- Session functionality

## Components
- Combo
- Passwordrecovery
- Session
- Verification

## Installation
Use Winterpk.Wordpress with the October plugin installer.  Otherwise upload to /plugins/winterpk/wordpress and log out and back into October backend.

- Combo component displays a registration and login form.
- Passwordrecovery displays a password recovery form. Add this component to the a page with the route "/password-recover". If you choose any other route, then make sure you set the appropriate value for Combo component.
- Session is used to retreive user data and provide auth.
- Verification is used to verify a users email if email verfication is turned on. 

