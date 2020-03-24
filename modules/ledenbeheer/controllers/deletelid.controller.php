<?php
	if(!isset($user)){
		//header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
		require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/wp-authenticate.php';
		/*** REQUIRE USER AUTHENTICATION ***/
		login();
		/*** RETRIEVE LOGGED IN USER INFORMATION ***/
		$user = wp_get_current_user();
	};

	// Include database class
	require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/utility.class.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/database.class.php';
	
	$database = new Database();
	
	if($_POST["RelatieNr"]){
		
	}
	
	include '../views/deletelid.view.php';
?>