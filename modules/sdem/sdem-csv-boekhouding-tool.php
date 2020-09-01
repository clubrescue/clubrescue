<?php
	/*** Club.Redders - PAGE TEMPLATE TYPE - ExternalDataProvisioning - v0.8.5
	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("../../wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();
	
	// Include database class
	include '../../util/utility.class.php';
	include '../../util/database.class.php';
	
	if(current_user_can('editor') || current_user_can('administrator') ) {
		$tableQuery =  'SELECT * FROM `cr_bankexports` ';	
		$tableQuery .= 'WHERE `Rentedatum` >= \'20191101\' AND `Rentedatum` <= \'20201031\'';
	} else {
		$tableQuery = '';			
	}
	
	$database = new Database();			
	$database->query($tableQuery);	
	$tableResult = $database->resultset();

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	try {
					
		header("Content-type: text/csv");
		header("Cache-Control: no-store, no-cache");
		header('Content-Disposition: attachment; filename="SDEM-Boekhouding-tool.csv"');
					
		//	$target = 'aad/Jaarrooster.csv';
		$database = new Database();
		$database->query($tableQuery);
		$database->execute();
		//if(file_exists($target)){
		//	$target_backup = 'backup/outputfilename('.time().').csv';
		//	rename($target,$target_backup);
		//}
		$output = fopen('php://output', 'w');
		$header = true;
			while ($row = $database->fetch()) {
				if ($header) {
					fputcsv($output, array_keys($row));
					$header = false;
				}
				fputcsv($output, $row);
			}
			fclose($output);
		}
	catch (PDOException $e) {
		// error handler
		var_dump($e);
	}
?>