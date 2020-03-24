<?php
	if(!isset($user)){
		//header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
		require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/wp-authenticate.php';
		/*** REQUIRE USER AUTHENTICATION ***/
		login();
		/*** RETRIEVE LOGGED IN USER INFORMATION ***/
		$user = wp_get_current_user();
	};
	
	$showCreateTabs = true;
	$message = 'Let op! Dit formulier kan automatisch ingevuld worden vanuit de mail van Machforms.';

	// Include database class
	require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/utility.class.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/database.class.php';
	
	$database = new Database();
	
	if($_POST["Relatienr"]){
		$dataArray = $_POST;
		$dataArray["VolledigAdres"] = $dataArray["Straat"].' '.$dataArray["Huisnr"].$dataArray["HuisnrToev"];
		$dataArray["VolledigeNaam"] = str_replace('  ', ' ', $dataArray["Roepnaam"] .' '. $dataArray["Tussenvoegsels"] .' '. $dataArray["Achternaam"]);
		
		$Relatienr = htmlspecialchars($_POST["Relatienr"]);
		
		$insertSql = build_sql_insert('cr_leden', $dataArray);
		$countSql = "SELECT COUNT(*) as count FROM `cr_leden` WHERE `Relatienr` = '$Relatienr'";
		$database->query($countSql);
		if($database->single()["count"] == 0){
			$database->query($insertSql);
			$database->execute();
			$message = 'Lid is successvol aangemaakt! Klik <a href="/clubredders/modules/ledenbeheer/">hier</a> om terug te gaan naar de hoofdpagina voor het beheer van leden.';
		}else {
			$message='Sportlink nummer bestaat al. Klik opnieuw op de link uit de mail en probeer het nog een keer. Klik <a href="/clubredders/modules/ledenbeheer/">hier</a> om terug te gaan naar de hoofdpagina voor het beheer van leden.';
		}
		$showCreateTabs = false;
	}else{
		// Dropdown voor lidStatus maken
		$lidStatusSQL =  'SELECT DISTINCT `Lidstatus` FROM `cr_leden` ORDER BY `Lidstatus` ASC';
		$database->query($lidStatusSQL);	
		$lidStatusResult = $database->resultset();
		
		$newLid = array();
		
		//Fetch, sanitize input and pre-populate the view.
		foreach($_GET as $key=>$val) {
			if($key !== 'Relatienr'){
				$escKey = htmlspecialchars($key);
				$escVal = htmlspecialchars($val);
				$newLid[$escKey] = $escVal;
			}
		}
	}
	
	include '../views/nieuwlid.view.php';
?>