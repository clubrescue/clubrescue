<?php
	/*** Club.Redders - PAGE TEMPLATE TYPE - ExternalDataProvisioning - v0.8.5
	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();
	
	// Include database class
	include 'util/utility.class.php';	
	include 'util/database.class.php';
	
	/***
	Vereiste gegevens voor de SDEM-CSV Jaarrooster Tool en Competenties support Tool:
	
	- EDP-Leden
	  - Relatienr
	  - VolledigeNaam
	  - GeboorteDatum (alleen voor de Jaarrooster Tool)
	
	- EDP-Indeling
	  - Relatienr
	  - Activiteit
	  - Locatie
	
	- EDP-Diplomas
	  - Relatienr
	  - Diploma/pas
	  - Ingangsdatum pas
	  - Einddatum pas
	  
	***/
	
	if( current_user_can('contributor') ||  current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') ) {
		$tableQuery =  'SELECT IFNULL(NULL, \'EDP-Leden\') as `SourceTable`, `Relatienr`, `VolledigeNaam`, `GeboorteDatum`, NULL as `Activiteit`, NULL as `Locatie`, NULL as `Diploma`, NULL as `IngangsDatum`, NULL as `EindDatum` FROM `cr_leden`
					    UNION
						SELECT IFNULL(NULL, \'EDP-Indeling\') as `SourceTable`, `Relatienr`, NULL as `VolledigeNaam`, NULL as `GeboorteDatum`, `Activiteit`, `Locatie`, NULL as `Diploma`, NULL as `IngangsDatum`, NULL as `EindDatum` FROM `cr_activiteiten`
						UNION
						SELECT IFNULL(NULL, \'EDP-Diplomas\') as `SourceTable`, `Relatienr`, NULL as `VolledigeNaam`, NULL as `GeboorteDatum`, NULL as `Activiteit`, NULL as `Locatie`, `Diploma`, `IngangsDatum`, `EindDatum` FROM `cr_diplomas`
					   ';
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
		header('Content-Disposition: attachment; filename="SDEM-Jaarrooster-tool.csv"');
					
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