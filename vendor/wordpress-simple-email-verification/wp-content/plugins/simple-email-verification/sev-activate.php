<?php

	if (isset($_GET['email']) &&  filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)){
		$email = $_GET['email'];
	}
	if (isset($_GET['key']) && (strlen($_GET['key']) == 32)) {
		$key = $_GET['key'];
	}
	if (isset($email) && isset($key)) {
		$user = get_user_by('email', $email);
		if (!$user) {
			wp_redirect(home_url());
		}
		$db_key = get_user_meta($user->ID, '_validation_key');
		delete_user_meta($user->ID, '_validation_key');
		if ($db_key && ($db_key[0] == $key)) {
			wp_update_user(array('ID' => $user->ID, 'role' => 'subscriber'));
			$creds = array();
			$creds['user_login'] = $user->data->user_login;
		    wp_set_current_user( $user_id, $user->user_login );
		    wp_set_auth_cookie( $user_id );
		    do_action( 'wp_login', $user->user_login );
		} else {
			wp_redirect(home_url());
		}
		
	} else {
		wp_redirect(home_url());
	}
	
?>
<h1>XFNGR</h1>
