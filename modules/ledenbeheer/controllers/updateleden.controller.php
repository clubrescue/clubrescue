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
	$selectedBewaker = isset($_POST["bewaker"]) ? htmlspecialchars($_POST["bewaker"]): htmlspecialchars($_POST["Relatienr"]);
	
	$dropdownLedenSQL = ' SELECT `Relatienr`, `VolledigeNaam`, `Achternaam` FROM `cr_leden` ORDER BY `Achternaam` ASC';
	$database->query($dropdownLedenSQL);	
	$dropdownLedenResult = $database->resultset();
	
	if($selectedBewaker){
		$ledenGegevensSQL = 'SELECT `cr_leden`.* , SUM(`TransactieBedrag`) as `TransactieBedrag` FROM `cr_leden` left outer join `cr_transacties` on `cr_leden`.`Relatienr`= `cr_transacties`.`Relatienr` where `cr_leden`.`Relatienr` = \''.$selectedBewaker.'\'';
		$database->query($ledenGegevensSQL);
		$ledenGegevensResult = $database->single();

		$lidStatusSQL =  'SELECT DISTINCT `Lidstatus` FROM `cr_leden` ORDER BY `Lidstatus` ASC';
		$database->query($lidStatusSQL);	
		$lidStatusResult = $database->resultset();	
	}
	
	if($_POST["Relatienr"]){
		
		$dataArray = $_POST;
		
		$dataArray["VolledigAdres"] = $dataArray["Straat"].' '.$dataArray["Huisnr"].$dataArray["HuisnrToev"];
		$dataArray["VolledigeNaam"] = str_replace('  ', ' ', $dataArray["Roepnaam"] .' '. $dataArray["Tussenvoegsels"] .' '. $dataArray["Achternaam"]);
		
		$updateSQL = build_sql_update('cr_leden', $dataArray, '`Relatienr` = \''.$selectedBewaker.'\'');
		$database->query($updateSQL);
		$updateResult = $database->execute();
		
		if($updateResult){
			
			$logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \''.addslashes($updateSQL).'\', \''.$user->user_login.'\')';
			$database->query($logQuery);
			$database->execute();

			$ledenGegevensSQL = 'SELECT `cr_leden`.* , SUM(`TransactieBedrag`) as `TransactieBedrag` FROM `cr_leden` left outer join `cr_transacties` on `cr_leden`.`Relatienr`= `cr_transacties`.`Relatienr` where `cr_leden`.`Relatienr` = \''.$selectedBewaker.'\'';
			$database->query($ledenGegevensSQL);
			$ledenGegevensResult = $database->single();

			$lidStatusSQL =  'SELECT DISTINCT `Lidstatus` FROM `cr_leden` ORDER BY `Lidstatus` ASC';
			$database->query($lidStatusSQL);	
			$lidStatusResult = $database->resultset();
			
			$dropdownLedenSQL = ' SELECT `Relatienr`, `VolledigeNaam`, `Achternaam` FROM `cr_leden` ORDER BY `Achternaam` ASC';
			$database->query($dropdownLedenSQL);	
			$dropdownLedenResult = $database->resultset();
		}
	}
	
	include '../views/updateleden.view.php';
?>