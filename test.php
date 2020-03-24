<?php

	if(!isset($user)){
		header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
		require_once("wp-authenticate.php");
		login();
		$user = wp_get_current_user();
	}
	// Include database class
	//require_once 'util/utility.class.php';	
	//require_once 'util/database.class.php';	
	
?>