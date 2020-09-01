<?php

	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("../../../wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/
	login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();
	
	
	// Include database class
	include '../../../util/utility.class.php';
	include '../../../util/database.class.php';

	$where4kader = 'BWK%';
	$where4ASCOPL = 'BWK'.Date("Y").'%';
	$whereIsNot = '%NI';
	
	$locations = ['','19','20','21','28','PC19','PC20','PC21','PC28','ASC','OPL','CVD','CVDS'];

	if( current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') || current_user_can('contributor')) {	
		$query = "SELECT distinct `Activiteit` FROM  `cr_activiteiten` WHERE `Activiteit` LIKE '$where4kader' AND `Activiteit` NOT LIKE '$whereIsNot' ORDER BY `ACTIVITEIT` DESC";
	}else{
		$query = "SELECT distinct `Activiteit` FROM  `cr_activiteiten` WHERE  `Relatienr` =  '$user->user_login' AND  `Locatie` IN ('OPL',  'ASC') AND `Activiteit` LIKE '$where4ASCOPL' AND `Activiteit` NOT LIKE '$whereIsNot' ORDER BY `ACTIVITEIT` ASC";
	}
	$database = new Database();
	$database->query($query);	
	$results = $database->resultset();
	
	// Materialize design
	//include __DIR__ . '/../../../header.php';
	//echo '<main>';
	//echo '<div class="container">';
	
	if($database->RowCount() > 0){

		echo '<form action="" method="POST"><select class="form-control" name="week">';
		foreach ($results as $key => $value) {
			if ($_POST["week"] === $value["Activiteit"]){
				echo '<option value="'.$value["Activiteit"].'" selected="selected">'.$value["Activiteit"].'</option>';
			}else{
				echo '<option value="'.$value["Activiteit"].'">'.$value["Activiteit"].'</option>';
			}
		}
		echo '</select>
				<button class="btn waves-effect waves-light" type="submit" name="action">Open de geselecteerde week
					<i class="material-icons right">send</i>
				</button>
			  </form>';
		
		if(isset($_POST["week"])) {
			//SORTERING IN ROOSTER IS OP BASIS VAN:
			// - Meeste ervaring (Berekend vanuit cr_activiteiten - aantal * Relatienr en Activiteit BWK* zonder BWK*NI t/m de BWK* voorafgaand aan de te roosteren week, dus ervaring bij aanvang van de week)
			// - Leeftijd (Bij gelijke ervaring oudste bewaker eerst, berekend vanuit cr_leden GeboorteDatum - in dagen)
			// - Volgorde van database resultaten (Bij gelijke ervaring en leeftijd in dagen komt eerst de bewaker die als eerste door de query op de database is gevonden gevolgd door de eerst volgende en zo verder)																																																															\''.$_POST["week"].'\'																																	\''.$_POST["week"].'\'
	//		$tableQuery = ' SELECT leden.`RelatieNr`, leden.`VolledigeNaam`, act.`Locatie`, ervaring.`WekenErvaring`, leden.`GeboorteDatum`, FLOOR(DATEDIFF(NOW(),leden.`GeboorteDatum`)/\'365.242199\') as Leeftijd,
	//						ROW_NUMBER() OVER(PARTITION BY act.`Locatie` ORDER BY ervaring.`WekenErvaring` DESC, leden.`GeboorteDatum` ASC) `PostPositie` FROM `cr_leden` leden join `cr_activiteiten` act on leden.`RelatieNr` = act.`RelatieNr` and act.`Activiteit` = \''.$_POST["week"].'\' join (SELECT Relatienr, count(*) as `WekenErvaring` FROM `cr_activiteiten` WHERE `Activiteit` not like \'%NI\' AND `Activiteit` < \''.$_POST["week"].'\' GROUP BY Relatienr) ervaring on leden.`RelatieNr` = ervaring.`RelatieNr` ';

			$tableQuery = 'SELECT *,
								-- INDEX (=ROW_NUMBER) PER LOCATIE GESORTEERD OP WEKENERVARING DESC en GEBOORTEDATUM ASC INDIEN GELIJK
								ROW_NUMBER() OVER(PARTITION BY `Locatie` ORDER BY `WekenErvaring` DESC, `GeboorteDatum` ASC) `PostPositie`,
								-- AFRONDEN NAAR HELE WEKEN NAAR BOVEN PER POST OBV CASE STATEMENT HIERONDER
								CEILING(AVG(`WekenErvaring`) OVER(PARTITION BY `Post`)) `PostErvaring`
							FROM (
								SELECT 
									leden.`RelatieNr`,
									leden.`VolledigeNaam`,
									act.`Locatie`,
									ervaring.`WekenErvaring`,
									leden.`GeboorteDatum`,
									CASE 
										WHEN act.`Locatie` IN (\'19\', \'PC19\') THEN \'19\' 
										WHEN act.`Locatie` IN (\'20\', \'PC20\') THEN \'20\' 
										WHEN act.`Locatie` IN (\'21\', \'PC21\', \'CVDS\') THEN \'21\' 
										WHEN act.`Locatie` IN (\'28\', \'PC28\', \'CVD\') THEN \'28\' 
										WHEN act.`Locatie` IN (\'ASC\', \'OPL\') THEN \'ASCOPL\' 
									END `Post`,
									regel1.`CompetentiesRegel1`,
									regel2.`CompetentiesRegel2`,
									FLOOR(DATEDIFF(NOW(),leden.`GeboorteDatum`)/\'365.242199\') as Leeftijd
								FROM `cr_leden` leden
								join `cr_activiteiten` act 
									-- UPDATE HIER WELKE WEEK JE WILT HEBBEN
									on leden.`RelatieNr` = act.`RelatieNr` and act.`Activiteit` = \''.$_POST["week"].'\'
									join (
									-- UPDATE HIER WELKE WEEK JE WILT HEBBEN
									SELECT Relatienr, count(*) as `WekenErvaring` FROM `cr_activiteiten` WHERE `Activiteit` not like \'%NI\' AND `Activiteit` < \''.$_POST["week"].'\' GROUP BY Relatienr
								) ervaring on leden.`RelatieNr` = ervaring.`RelatieNr`
								left join (
									SELECT diplomas.`Relatienr`, GROUP_CONCAT(DISTINCT beheer.`Afkorting` ORDER BY beheer.`Volgordenr` SEPARATOR \' \') as `CompetentiesRegel1`
									FROM `cr_diplomas` diplomas
									JOIN `cr_diplomabeheer` beheer
										ON beheer.`Naam` = diplomas.`Diploma` 
									WHERE 
										-- UPDATE HIER WELKE SOORTEN DIPLOMAS ER OP REGEL 1 MOETEN, DIPLOMA MOET GELDIG ZIJN OF GEEN EINDDATUM HEBBEN
										diplomas.`Soort` IN (\'PvB\', \'Niveau\') and (`EindDatum` = \'0000-00-00\' OR `EindDatum` >= NOW()) and beheer.`Afkorting` <> \'\'
									GROUP BY diplomas.`Relatienr`
								) regel1 ON regel1.`Relatienr` = leden.`RelatieNr`
								left join (
									SELECT diplomas.`Relatienr`, GROUP_CONCAT(DISTINCT beheer.`Afkorting` ORDER BY beheer.`Volgordenr` SEPARATOR \' \') as `CompetentiesRegel2`
									FROM `cr_diplomas` diplomas
									JOIN `cr_diplomabeheer` beheer
										ON beheer.`Naam` = diplomas.`Diploma` 
									WHERE 
										-- UPDATE HIER WELKE SOORTEN DIPLOMAS ER OP REGEL 2 MOETEN, DIPLOMA MOET GELDIG ZIJN OF GEEN EINDDATUM HEBBEN
										diplomas.`Soort` IN (\'Bondsdiploma\', \'EvC\') and (`EindDatum` = \'0000-00-00\' OR `EindDatum` >= NOW()) and beheer.`Afkorting` <> \'\'
									GROUP BY diplomas.`Relatienr`
								) regel2 ON regel2.`Relatienr` = leden.`RelatieNr`
							) AS BASE ';
	
			$database->query($tableQuery);	
			$tableResult = $database->resultset();

			echo '<form action="rooster-technisch-ontw-v2.php" method="post" name="location">';
			//echo '<table class="striped">';
			//echo '<tr><th>Relatienummer</th><th>Volledige Naam</th><th>Locatie</th><th>Ervaring</th><th>Leeftijd</th><th>LocatieVolgnr</th></tr>';
			//foreach ($tableResult as $key => $value) {
			//	echo '<tr>';
			//	echo '<td>'.$value["RelatieNr"] . '</td>';
			//	echo '<td>'.$value["VolledigeNaam"] . '</td>';
			//	echo '<td>'.$value["Locatie"] . '</td>';
			//	echo '<td>'.$value["WekenErvaring"] . '</td>';
			//	echo '<td>'.$value["Leeftijd"] . '</td>';
			//	echo '<td>'.$value["PostPositie"] . '</td>';
			//	echo '</tr>';
			//}
		}
	}else{
		// Geen roosters om in te laden.
		echo 'Je hebt helaas geen roosters om in te laden!';
	}

// We gaan nu onze array uitpakken door een variabele te maken voor elke waarde in de array.
// Ieder lid dat aanwezig is in de array krijgt een eigen prefix. Deze gaan we later matchen met de locatie en positie waardes.

	//POS0
		if ($tableResult[0][Locatie] == 'ASC' AND $tableResult[0][PostPositie] == '1') {
			extract($tableResult[0], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[0][Locatie] == 'OPL' AND $tableResult[0][PostPositie] == '1') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[0][Locatie] == 'PC19' AND $tableResult[0][PostPositie] == '1') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[0][Locatie] == 'PC20' AND $tableResult[0][PostPositie] == '1') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[0][Locatie] == 'PC21' AND $tableResult[0][PostPositie] == '1') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[0][Locatie] == 'PC28' AND $tableResult[0][PostPositie] == '1') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[0][Locatie] == '19' AND $tableResult[0][PostPositie] == '1') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[0][Locatie] == '20' AND $tableResult[0][PostPositie] == '1') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[0][Locatie] == 'CVDS' AND $tableResult[0][PostPositie] == '1') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[0][Locatie] == 'CVD' AND $tableResult[0][PostPositie] == '1') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[0][Locatie] == '19' AND $tableResult[0][PostPositie] == '2') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[0][Locatie] == '20' AND $tableResult[0][PostPositie] == '2') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[0][Locatie] == '21' AND $tableResult[0][PostPositie] == '1') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[0][Locatie] == '28' AND $tableResult[0][PostPositie] == '1') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[0][Locatie] == '19' AND $tableResult[0][PostPositie] == '3') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[0][Locatie] == '20' AND $tableResult[0][PostPositie] == '3') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[0][Locatie] == '21' AND $tableResult[0][PostPositie] == '2') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[0][Locatie] == '28' AND $tableResult[0][PostPositie] == '2') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[0][Locatie] == '19' AND $tableResult[0][PostPositie] == '4') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[0][Locatie] == '20' AND $tableResult[0][PostPositie] == '4') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[0][Locatie] == '21' AND $tableResult[0][PostPositie] == '3') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[0][Locatie] == '28' AND $tableResult[0][PostPositie] == '3') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[0][Locatie] == '19' AND $tableResult[0][PostPositie] == '5') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[0][Locatie] == '20' AND $tableResult[0][PostPositie] == '5') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[0][Locatie] == '21' AND $tableResult[0][PostPositie] == '4') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[0][Locatie] == '28' AND $tableResult[0][PostPositie] == '4') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[0][Locatie] == '19' AND $tableResult[0][PostPositie] == '6') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[0][Locatie] == '20' AND $tableResult[0][PostPositie] == '6') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[0][Locatie] == '21' AND $tableResult[0][PostPositie] == '5') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[0][Locatie] == '28' AND $tableResult[0][PostPositie] == '5') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[0][Locatie] == '21' AND $tableResult[0][PostPositie] == '6') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[0][Locatie] == '28' AND $tableResult[0][PostPositie] == '6') {
				extract($tableResult[0], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}
	//POS1
		if ($tableResult[1][Locatie] == 'ASC' AND $tableResult[1][PostPositie] == '1') {
			extract($tableResult[1], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[1][Locatie] == 'OPL' AND $tableResult[1][PostPositie] == '1') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[1][Locatie] == 'PC19' AND $tableResult[1][PostPositie] == '1') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[1][Locatie] == 'PC20' AND $tableResult[1][PostPositie] == '1') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[1][Locatie] == 'PC21' AND $tableResult[1][PostPositie] == '1') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[1][Locatie] == 'PC28' AND $tableResult[1][PostPositie] == '1') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[1][Locatie] == '19' AND $tableResult[1][PostPositie] == '1') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[1][Locatie] == '20' AND $tableResult[1][PostPositie] == '1') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[1][Locatie] == 'CVDS' AND $tableResult[1][PostPositie] == '1') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[1][Locatie] == 'CVD' AND $tableResult[1][PostPositie] == '1') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[1][Locatie] == '19' AND $tableResult[1][PostPositie] == '2') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[1][Locatie] == '20' AND $tableResult[1][PostPositie] == '2') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[1][Locatie] == '21' AND $tableResult[1][PostPositie] == '1') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[1][Locatie] == '28' AND $tableResult[1][PostPositie] == '1') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[1][Locatie] == '19' AND $tableResult[1][PostPositie] == '3') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[1][Locatie] == '20' AND $tableResult[1][PostPositie] == '3') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[1][Locatie] == '21' AND $tableResult[1][PostPositie] == '2') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[1][Locatie] == '28' AND $tableResult[1][PostPositie] == '2') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[1][Locatie] == '19' AND $tableResult[1][PostPositie] == '4') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[1][Locatie] == '20' AND $tableResult[1][PostPositie] == '4') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[1][Locatie] == '21' AND $tableResult[1][PostPositie] == '3') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[1][Locatie] == '28' AND $tableResult[1][PostPositie] == '3') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[1][Locatie] == '19' AND $tableResult[1][PostPositie] == '5') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[1][Locatie] == '20' AND $tableResult[1][PostPositie] == '5') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[1][Locatie] == '21' AND $tableResult[1][PostPositie] == '4') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[1][Locatie] == '28' AND $tableResult[1][PostPositie] == '4') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[1][Locatie] == '19' AND $tableResult[1][PostPositie] == '6') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[1][Locatie] == '20' AND $tableResult[1][PostPositie] == '6') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[1][Locatie] == '21' AND $tableResult[1][PostPositie] == '5') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[1][Locatie] == '28' AND $tableResult[1][PostPositie] == '5') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[1][Locatie] == '21' AND $tableResult[1][PostPositie] == '6') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[1][Locatie] == '28' AND $tableResult[1][PostPositie] == '6') {
				extract($tableResult[1], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}
	//POS2
		if ($tableResult[2][Locatie] == 'ASC' AND $tableResult[2][PostPositie] == '1') {
			extract($tableResult[2], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[2][Locatie] == 'OPL' AND $tableResult[2][PostPositie] == '1') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[2][Locatie] == 'PC19' AND $tableResult[2][PostPositie] == '1') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[2][Locatie] == 'PC20' AND $tableResult[2][PostPositie] == '1') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[2][Locatie] == 'PC21' AND $tableResult[2][PostPositie] == '1') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[2][Locatie] == 'PC28' AND $tableResult[2][PostPositie] == '1') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[2][Locatie] == '19' AND $tableResult[2][PostPositie] == '1') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[2][Locatie] == '20' AND $tableResult[2][PostPositie] == '1') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[2][Locatie] == 'CVDS' AND $tableResult[2][PostPositie] == '1') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[2][Locatie] == 'CVD' AND $tableResult[2][PostPositie] == '1') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[2][Locatie] == '19' AND $tableResult[2][PostPositie] == '2') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[2][Locatie] == '20' AND $tableResult[2][PostPositie] == '2') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[2][Locatie] == '21' AND $tableResult[2][PostPositie] == '1') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[2][Locatie] == '28' AND $tableResult[2][PostPositie] == '1') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[2][Locatie] == '19' AND $tableResult[2][PostPositie] == '3') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[2][Locatie] == '20' AND $tableResult[2][PostPositie] == '3') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[2][Locatie] == '21' AND $tableResult[2][PostPositie] == '2') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[2][Locatie] == '28' AND $tableResult[2][PostPositie] == '2') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[2][Locatie] == '19' AND $tableResult[2][PostPositie] == '4') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[2][Locatie] == '20' AND $tableResult[2][PostPositie] == '4') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[2][Locatie] == '21' AND $tableResult[2][PostPositie] == '3') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[2][Locatie] == '28' AND $tableResult[2][PostPositie] == '3') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[2][Locatie] == '19' AND $tableResult[2][PostPositie] == '5') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[2][Locatie] == '20' AND $tableResult[2][PostPositie] == '5') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[2][Locatie] == '21' AND $tableResult[2][PostPositie] == '4') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[2][Locatie] == '28' AND $tableResult[2][PostPositie] == '4') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[2][Locatie] == '19' AND $tableResult[2][PostPositie] == '6') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[2][Locatie] == '20' AND $tableResult[2][PostPositie] == '6') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[2][Locatie] == '21' AND $tableResult[2][PostPositie] == '5') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[2][Locatie] == '28' AND $tableResult[2][PostPositie] == '5') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[2][Locatie] == '21' AND $tableResult[2][PostPositie] == '6') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[2][Locatie] == '28' AND $tableResult[2][PostPositie] == '6') {
				extract($tableResult[2], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}
	//POS3
		if ($tableResult[3][Locatie] == 'ASC' AND $tableResult[3][PostPositie] == '1') {
			extract($tableResult[3], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[3][Locatie] == 'OPL' AND $tableResult[3][PostPositie] == '1') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[3][Locatie] == 'PC19' AND $tableResult[3][PostPositie] == '1') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[3][Locatie] == 'PC20' AND $tableResult[3][PostPositie] == '1') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[3][Locatie] == 'PC21' AND $tableResult[3][PostPositie] == '1') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[3][Locatie] == 'PC28' AND $tableResult[3][PostPositie] == '1') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[3][Locatie] == '19' AND $tableResult[3][PostPositie] == '1') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[3][Locatie] == '20' AND $tableResult[3][PostPositie] == '1') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[3][Locatie] == 'CVDS' AND $tableResult[3][PostPositie] == '1') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[3][Locatie] == 'CVD' AND $tableResult[3][PostPositie] == '1') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[3][Locatie] == '19' AND $tableResult[3][PostPositie] == '2') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[3][Locatie] == '20' AND $tableResult[3][PostPositie] == '2') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[3][Locatie] == '21' AND $tableResult[3][PostPositie] == '1') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[3][Locatie] == '28' AND $tableResult[3][PostPositie] == '1') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[3][Locatie] == '19' AND $tableResult[3][PostPositie] == '3') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[3][Locatie] == '20' AND $tableResult[3][PostPositie] == '3') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[3][Locatie] == '21' AND $tableResult[3][PostPositie] == '2') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[3][Locatie] == '28' AND $tableResult[3][PostPositie] == '2') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[3][Locatie] == '19' AND $tableResult[3][PostPositie] == '4') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[3][Locatie] == '20' AND $tableResult[3][PostPositie] == '4') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[3][Locatie] == '21' AND $tableResult[3][PostPositie] == '3') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[3][Locatie] == '28' AND $tableResult[3][PostPositie] == '3') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[3][Locatie] == '19' AND $tableResult[3][PostPositie] == '5') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[3][Locatie] == '20' AND $tableResult[3][PostPositie] == '5') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[3][Locatie] == '21' AND $tableResult[3][PostPositie] == '4') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[3][Locatie] == '28' AND $tableResult[3][PostPositie] == '4') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[3][Locatie] == '19' AND $tableResult[3][PostPositie] == '6') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[3][Locatie] == '20' AND $tableResult[3][PostPositie] == '6') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[3][Locatie] == '21' AND $tableResult[3][PostPositie] == '5') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[3][Locatie] == '28' AND $tableResult[3][PostPositie] == '5') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[3][Locatie] == '21' AND $tableResult[3][PostPositie] == '6') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[3][Locatie] == '28' AND $tableResult[3][PostPositie] == '6') {
				extract($tableResult[3], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}
	//POS4
		if ($tableResult[4][Locatie] == 'ASC' AND $tableResult[4][PostPositie] == '1') {
			extract($tableResult[4], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[4][Locatie] == 'OPL' AND $tableResult[4][PostPositie] == '1') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[4][Locatie] == 'PC19' AND $tableResult[4][PostPositie] == '1') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[4][Locatie] == 'PC20' AND $tableResult[4][PostPositie] == '1') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[4][Locatie] == 'PC21' AND $tableResult[4][PostPositie] == '1') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[4][Locatie] == 'PC28' AND $tableResult[4][PostPositie] == '1') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[4][Locatie] == '19' AND $tableResult[4][PostPositie] == '1') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[4][Locatie] == '20' AND $tableResult[4][PostPositie] == '1') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[4][Locatie] == 'CVDS' AND $tableResult[4][PostPositie] == '1') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[4][Locatie] == 'CVD' AND $tableResult[4][PostPositie] == '1') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[4][Locatie] == '19' AND $tableResult[4][PostPositie] == '2') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[4][Locatie] == '20' AND $tableResult[4][PostPositie] == '2') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[4][Locatie] == '21' AND $tableResult[4][PostPositie] == '1') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[4][Locatie] == '28' AND $tableResult[4][PostPositie] == '1') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[4][Locatie] == '19' AND $tableResult[4][PostPositie] == '3') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[4][Locatie] == '20' AND $tableResult[4][PostPositie] == '3') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[4][Locatie] == '21' AND $tableResult[4][PostPositie] == '2') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[4][Locatie] == '28' AND $tableResult[4][PostPositie] == '2') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[4][Locatie] == '19' AND $tableResult[4][PostPositie] == '4') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[4][Locatie] == '20' AND $tableResult[4][PostPositie] == '4') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[4][Locatie] == '21' AND $tableResult[4][PostPositie] == '3') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[4][Locatie] == '28' AND $tableResult[4][PostPositie] == '3') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[4][Locatie] == '19' AND $tableResult[4][PostPositie] == '5') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[4][Locatie] == '20' AND $tableResult[4][PostPositie] == '5') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[4][Locatie] == '21' AND $tableResult[4][PostPositie] == '4') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[4][Locatie] == '28' AND $tableResult[4][PostPositie] == '4') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[4][Locatie] == '19' AND $tableResult[4][PostPositie] == '6') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[4][Locatie] == '20' AND $tableResult[4][PostPositie] == '6') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[4][Locatie] == '21' AND $tableResult[4][PostPositie] == '5') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[4][Locatie] == '28' AND $tableResult[4][PostPositie] == '5') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[4][Locatie] == '21' AND $tableResult[4][PostPositie] == '6') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[4][Locatie] == '28' AND $tableResult[4][PostPositie] == '6') {
				extract($tableResult[4], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}
	//POS5
		if ($tableResult[5][Locatie] == 'ASC' AND $tableResult[5][PostPositie] == '1') {
			extract($tableResult[5], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[5][Locatie] == 'OPL' AND $tableResult[5][PostPositie] == '1') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[5][Locatie] == 'PC19' AND $tableResult[5][PostPositie] == '1') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[5][Locatie] == 'PC20' AND $tableResult[5][PostPositie] == '1') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[5][Locatie] == 'PC21' AND $tableResult[5][PostPositie] == '1') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[5][Locatie] == 'PC28' AND $tableResult[5][PostPositie] == '1') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[5][Locatie] == '19' AND $tableResult[5][PostPositie] == '1') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[5][Locatie] == '20' AND $tableResult[5][PostPositie] == '1') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[5][Locatie] == 'CVDS' AND $tableResult[5][PostPositie] == '1') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[5][Locatie] == 'CVD' AND $tableResult[5][PostPositie] == '1') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[5][Locatie] == '19' AND $tableResult[5][PostPositie] == '2') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[5][Locatie] == '20' AND $tableResult[5][PostPositie] == '2') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[5][Locatie] == '21' AND $tableResult[5][PostPositie] == '1') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[5][Locatie] == '28' AND $tableResult[5][PostPositie] == '1') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[5][Locatie] == '19' AND $tableResult[5][PostPositie] == '3') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[5][Locatie] == '20' AND $tableResult[5][PostPositie] == '3') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[5][Locatie] == '21' AND $tableResult[5][PostPositie] == '2') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[5][Locatie] == '28' AND $tableResult[5][PostPositie] == '2') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[5][Locatie] == '19' AND $tableResult[5][PostPositie] == '4') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[5][Locatie] == '20' AND $tableResult[5][PostPositie] == '4') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[5][Locatie] == '21' AND $tableResult[5][PostPositie] == '3') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[5][Locatie] == '28' AND $tableResult[5][PostPositie] == '3') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[5][Locatie] == '19' AND $tableResult[5][PostPositie] == '5') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[5][Locatie] == '20' AND $tableResult[5][PostPositie] == '5') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[5][Locatie] == '21' AND $tableResult[5][PostPositie] == '4') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[5][Locatie] == '28' AND $tableResult[5][PostPositie] == '4') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[5][Locatie] == '19' AND $tableResult[5][PostPositie] == '6') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[5][Locatie] == '20' AND $tableResult[5][PostPositie] == '6') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[5][Locatie] == '21' AND $tableResult[5][PostPositie] == '5') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[5][Locatie] == '28' AND $tableResult[5][PostPositie] == '5') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[5][Locatie] == '21' AND $tableResult[5][PostPositie] == '6') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[5][Locatie] == '28' AND $tableResult[5][PostPositie] == '6') {
				extract($tableResult[5], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}
	//POS6
		if ($tableResult[6][Locatie] == 'ASC' AND $tableResult[6][PostPositie] == '1') {
			extract($tableResult[6], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[6][Locatie] == 'OPL' AND $tableResult[6][PostPositie] == '1') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[6][Locatie] == 'PC19' AND $tableResult[6][PostPositie] == '1') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[6][Locatie] == 'PC20' AND $tableResult[6][PostPositie] == '1') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[6][Locatie] == 'PC21' AND $tableResult[6][PostPositie] == '1') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[6][Locatie] == 'PC28' AND $tableResult[6][PostPositie] == '1') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[6][Locatie] == '19' AND $tableResult[6][PostPositie] == '1') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[6][Locatie] == '20' AND $tableResult[6][PostPositie] == '1') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[6][Locatie] == 'CVDS' AND $tableResult[6][PostPositie] == '1') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[6][Locatie] == 'CVD' AND $tableResult[6][PostPositie] == '1') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[6][Locatie] == '19' AND $tableResult[6][PostPositie] == '2') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[6][Locatie] == '20' AND $tableResult[6][PostPositie] == '2') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[6][Locatie] == '21' AND $tableResult[6][PostPositie] == '1') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[6][Locatie] == '28' AND $tableResult[6][PostPositie] == '1') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[6][Locatie] == '19' AND $tableResult[6][PostPositie] == '3') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[6][Locatie] == '20' AND $tableResult[6][PostPositie] == '3') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[6][Locatie] == '21' AND $tableResult[6][PostPositie] == '2') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[6][Locatie] == '28' AND $tableResult[6][PostPositie] == '2') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[6][Locatie] == '19' AND $tableResult[6][PostPositie] == '4') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[6][Locatie] == '20' AND $tableResult[6][PostPositie] == '4') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[6][Locatie] == '21' AND $tableResult[6][PostPositie] == '3') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[6][Locatie] == '28' AND $tableResult[6][PostPositie] == '3') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[6][Locatie] == '19' AND $tableResult[6][PostPositie] == '5') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[6][Locatie] == '20' AND $tableResult[6][PostPositie] == '5') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[6][Locatie] == '21' AND $tableResult[6][PostPositie] == '4') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[6][Locatie] == '28' AND $tableResult[6][PostPositie] == '4') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[6][Locatie] == '19' AND $tableResult[6][PostPositie] == '6') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[6][Locatie] == '20' AND $tableResult[6][PostPositie] == '6') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[6][Locatie] == '21' AND $tableResult[6][PostPositie] == '5') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[6][Locatie] == '28' AND $tableResult[6][PostPositie] == '5') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[6][Locatie] == '21' AND $tableResult[6][PostPositie] == '6') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[6][Locatie] == '28' AND $tableResult[6][PostPositie] == '6') {
				extract($tableResult[6], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}
	//POS7
		if ($tableResult[7][Locatie] == 'ASC' AND $tableResult[7][PostPositie] == '1') {
			extract($tableResult[7], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[7][Locatie] == 'OPL' AND $tableResult[7][PostPositie] == '1') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[7][Locatie] == 'PC19' AND $tableResult[7][PostPositie] == '1') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[7][Locatie] == 'PC20' AND $tableResult[7][PostPositie] == '1') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[7][Locatie] == 'PC21' AND $tableResult[7][PostPositie] == '1') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[7][Locatie] == 'PC28' AND $tableResult[7][PostPositie] == '1') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[7][Locatie] == '19' AND $tableResult[7][PostPositie] == '1') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[7][Locatie] == '20' AND $tableResult[7][PostPositie] == '1') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[7][Locatie] == 'CVDS' AND $tableResult[7][PostPositie] == '1') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[7][Locatie] == 'CVD' AND $tableResult[7][PostPositie] == '1') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[7][Locatie] == '19' AND $tableResult[7][PostPositie] == '2') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[7][Locatie] == '20' AND $tableResult[7][PostPositie] == '2') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[7][Locatie] == '21' AND $tableResult[7][PostPositie] == '1') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[7][Locatie] == '28' AND $tableResult[7][PostPositie] == '1') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[7][Locatie] == '19' AND $tableResult[7][PostPositie] == '3') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[7][Locatie] == '20' AND $tableResult[7][PostPositie] == '3') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[7][Locatie] == '21' AND $tableResult[7][PostPositie] == '2') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[7][Locatie] == '28' AND $tableResult[7][PostPositie] == '2') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[7][Locatie] == '19' AND $tableResult[7][PostPositie] == '4') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[7][Locatie] == '20' AND $tableResult[7][PostPositie] == '4') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[7][Locatie] == '21' AND $tableResult[7][PostPositie] == '3') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[7][Locatie] == '28' AND $tableResult[7][PostPositie] == '3') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[7][Locatie] == '19' AND $tableResult[7][PostPositie] == '5') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[7][Locatie] == '20' AND $tableResult[7][PostPositie] == '5') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[7][Locatie] == '21' AND $tableResult[7][PostPositie] == '4') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[7][Locatie] == '28' AND $tableResult[7][PostPositie] == '4') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[7][Locatie] == '19' AND $tableResult[7][PostPositie] == '6') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[7][Locatie] == '20' AND $tableResult[7][PostPositie] == '6') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[7][Locatie] == '21' AND $tableResult[7][PostPositie] == '5') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[7][Locatie] == '28' AND $tableResult[7][PostPositie] == '5') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[7][Locatie] == '21' AND $tableResult[7][PostPositie] == '6') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[7][Locatie] == '28' AND $tableResult[7][PostPositie] == '6') {
				extract($tableResult[7], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}
	//POS8
		if ($tableResult[8][Locatie] == 'ASC' AND $tableResult[8][PostPositie] == '1') {
			extract($tableResult[8], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[8][Locatie] == 'OPL' AND $tableResult[8][PostPositie] == '1') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[8][Locatie] == 'PC19' AND $tableResult[8][PostPositie] == '1') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[8][Locatie] == 'PC20' AND $tableResult[8][PostPositie] == '1') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[8][Locatie] == 'PC21' AND $tableResult[8][PostPositie] == '1') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[8][Locatie] == 'PC28' AND $tableResult[8][PostPositie] == '1') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[8][Locatie] == '19' AND $tableResult[8][PostPositie] == '1') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[8][Locatie] == '20' AND $tableResult[8][PostPositie] == '1') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[8][Locatie] == 'CVDS' AND $tableResult[8][PostPositie] == '1') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[8][Locatie] == 'CVD' AND $tableResult[8][PostPositie] == '1') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[8][Locatie] == '19' AND $tableResult[8][PostPositie] == '2') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[8][Locatie] == '20' AND $tableResult[8][PostPositie] == '2') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[8][Locatie] == '21' AND $tableResult[8][PostPositie] == '1') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[8][Locatie] == '28' AND $tableResult[8][PostPositie] == '1') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[8][Locatie] == '19' AND $tableResult[8][PostPositie] == '3') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[8][Locatie] == '20' AND $tableResult[8][PostPositie] == '3') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[8][Locatie] == '21' AND $tableResult[8][PostPositie] == '2') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[8][Locatie] == '28' AND $tableResult[8][PostPositie] == '2') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[8][Locatie] == '19' AND $tableResult[8][PostPositie] == '4') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[8][Locatie] == '20' AND $tableResult[8][PostPositie] == '4') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[8][Locatie] == '21' AND $tableResult[8][PostPositie] == '3') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[8][Locatie] == '28' AND $tableResult[8][PostPositie] == '3') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[8][Locatie] == '19' AND $tableResult[8][PostPositie] == '5') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[8][Locatie] == '20' AND $tableResult[8][PostPositie] == '5') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[8][Locatie] == '21' AND $tableResult[8][PostPositie] == '4') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[8][Locatie] == '28' AND $tableResult[8][PostPositie] == '4') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[8][Locatie] == '19' AND $tableResult[8][PostPositie] == '6') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[8][Locatie] == '20' AND $tableResult[8][PostPositie] == '6') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[8][Locatie] == '21' AND $tableResult[8][PostPositie] == '5') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[8][Locatie] == '28' AND $tableResult[8][PostPositie] == '5') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[8][Locatie] == '21' AND $tableResult[8][PostPositie] == '6') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[8][Locatie] == '28' AND $tableResult[8][PostPositie] == '6') {
				extract($tableResult[8], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}
	//POS9
		if ($tableResult[9][Locatie] == 'ASC' AND $tableResult[9][PostPositie] == '1') {
			extract($tableResult[9], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[9][Locatie] == 'OPL' AND $tableResult[9][PostPositie] == '1') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[9][Locatie] == 'PC19' AND $tableResult[9][PostPositie] == '1') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[9][Locatie] == 'PC20' AND $tableResult[9][PostPositie] == '1') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[9][Locatie] == 'PC21' AND $tableResult[9][PostPositie] == '1') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[9][Locatie] == 'PC28' AND $tableResult[9][PostPositie] == '1') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[9][Locatie] == '19' AND $tableResult[9][PostPositie] == '1') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[9][Locatie] == '20' AND $tableResult[9][PostPositie] == '1') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[9][Locatie] == 'CVDS' AND $tableResult[9][PostPositie] == '1') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[9][Locatie] == 'CVD' AND $tableResult[9][PostPositie] == '1') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[9][Locatie] == '19' AND $tableResult[9][PostPositie] == '2') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[9][Locatie] == '20' AND $tableResult[9][PostPositie] == '2') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[9][Locatie] == '21' AND $tableResult[9][PostPositie] == '1') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[9][Locatie] == '28' AND $tableResult[9][PostPositie] == '1') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[9][Locatie] == '19' AND $tableResult[9][PostPositie] == '3') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[9][Locatie] == '20' AND $tableResult[9][PostPositie] == '3') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[9][Locatie] == '21' AND $tableResult[9][PostPositie] == '2') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[9][Locatie] == '28' AND $tableResult[9][PostPositie] == '2') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[9][Locatie] == '19' AND $tableResult[9][PostPositie] == '4') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[9][Locatie] == '20' AND $tableResult[9][PostPositie] == '4') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[9][Locatie] == '21' AND $tableResult[9][PostPositie] == '3') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[9][Locatie] == '28' AND $tableResult[9][PostPositie] == '3') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[9][Locatie] == '19' AND $tableResult[9][PostPositie] == '5') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[9][Locatie] == '20' AND $tableResult[9][PostPositie] == '5') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[9][Locatie] == '21' AND $tableResult[9][PostPositie] == '4') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[9][Locatie] == '28' AND $tableResult[9][PostPositie] == '4') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[9][Locatie] == '19' AND $tableResult[9][PostPositie] == '6') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[9][Locatie] == '20' AND $tableResult[9][PostPositie] == '6') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[9][Locatie] == '21' AND $tableResult[9][PostPositie] == '5') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[9][Locatie] == '28' AND $tableResult[9][PostPositie] == '5') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[9][Locatie] == '21' AND $tableResult[9][PostPositie] == '6') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[9][Locatie] == '28' AND $tableResult[9][PostPositie] == '6') {
				extract($tableResult[9], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}	
	//POS10
		if ($tableResult[10][Locatie] == 'ASC' AND $tableResult[10][PostPositie] == '1') {
			extract($tableResult[10], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[10][Locatie] == 'OPL' AND $tableResult[10][PostPositie] == '1') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[10][Locatie] == 'PC19' AND $tableResult[10][PostPositie] == '1') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[10][Locatie] == 'PC20' AND $tableResult[10][PostPositie] == '1') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[10][Locatie] == 'PC21' AND $tableResult[10][PostPositie] == '1') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[10][Locatie] == 'PC28' AND $tableResult[10][PostPositie] == '1') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[10][Locatie] == '19' AND $tableResult[10][PostPositie] == '1') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[10][Locatie] == '20' AND $tableResult[10][PostPositie] == '1') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[10][Locatie] == 'CVDS' AND $tableResult[10][PostPositie] == '1') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[10][Locatie] == 'CVD' AND $tableResult[10][PostPositie] == '1') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[10][Locatie] == '19' AND $tableResult[10][PostPositie] == '2') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[10][Locatie] == '20' AND $tableResult[10][PostPositie] == '2') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[10][Locatie] == '21' AND $tableResult[10][PostPositie] == '1') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[10][Locatie] == '28' AND $tableResult[10][PostPositie] == '1') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[10][Locatie] == '19' AND $tableResult[10][PostPositie] == '3') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[10][Locatie] == '20' AND $tableResult[10][PostPositie] == '3') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[10][Locatie] == '21' AND $tableResult[10][PostPositie] == '2') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[10][Locatie] == '28' AND $tableResult[10][PostPositie] == '2') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[10][Locatie] == '19' AND $tableResult[10][PostPositie] == '4') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[10][Locatie] == '20' AND $tableResult[10][PostPositie] == '4') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[10][Locatie] == '21' AND $tableResult[10][PostPositie] == '3') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[10][Locatie] == '28' AND $tableResult[10][PostPositie] == '3') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[10][Locatie] == '19' AND $tableResult[10][PostPositie] == '5') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[10][Locatie] == '20' AND $tableResult[10][PostPositie] == '5') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[10][Locatie] == '21' AND $tableResult[10][PostPositie] == '4') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[10][Locatie] == '28' AND $tableResult[10][PostPositie] == '4') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[10][Locatie] == '19' AND $tableResult[10][PostPositie] == '6') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[10][Locatie] == '20' AND $tableResult[10][PostPositie] == '6') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[10][Locatie] == '21' AND $tableResult[10][PostPositie] == '5') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[10][Locatie] == '28' AND $tableResult[10][PostPositie] == '5') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[10][Locatie] == '21' AND $tableResult[10][PostPositie] == '6') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[10][Locatie] == '28' AND $tableResult[10][PostPositie] == '6') {
				extract($tableResult[10], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}	
	//POS11
		if ($tableResult[11][Locatie] == 'ASC' AND $tableResult[11][PostPositie] == '1') {
			extract($tableResult[11], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[11][Locatie] == 'OPL' AND $tableResult[11][PostPositie] == '1') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[11][Locatie] == 'PC19' AND $tableResult[11][PostPositie] == '1') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[11][Locatie] == 'PC20' AND $tableResult[11][PostPositie] == '1') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[11][Locatie] == 'PC21' AND $tableResult[11][PostPositie] == '1') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[11][Locatie] == 'PC28' AND $tableResult[11][PostPositie] == '1') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[11][Locatie] == '19' AND $tableResult[11][PostPositie] == '1') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[11][Locatie] == '20' AND $tableResult[11][PostPositie] == '1') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[11][Locatie] == 'CVDS' AND $tableResult[11][PostPositie] == '1') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[11][Locatie] == 'CVD' AND $tableResult[11][PostPositie] == '1') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[11][Locatie] == '19' AND $tableResult[11][PostPositie] == '2') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[11][Locatie] == '20' AND $tableResult[11][PostPositie] == '2') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[11][Locatie] == '21' AND $tableResult[11][PostPositie] == '1') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[11][Locatie] == '28' AND $tableResult[11][PostPositie] == '1') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[11][Locatie] == '19' AND $tableResult[11][PostPositie] == '3') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[11][Locatie] == '20' AND $tableResult[11][PostPositie] == '3') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[11][Locatie] == '21' AND $tableResult[11][PostPositie] == '2') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[11][Locatie] == '28' AND $tableResult[11][PostPositie] == '2') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[11][Locatie] == '19' AND $tableResult[11][PostPositie] == '4') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[11][Locatie] == '20' AND $tableResult[11][PostPositie] == '4') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[11][Locatie] == '21' AND $tableResult[11][PostPositie] == '3') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[11][Locatie] == '28' AND $tableResult[11][PostPositie] == '3') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[11][Locatie] == '19' AND $tableResult[11][PostPositie] == '5') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[11][Locatie] == '20' AND $tableResult[11][PostPositie] == '5') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[11][Locatie] == '21' AND $tableResult[11][PostPositie] == '4') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[11][Locatie] == '28' AND $tableResult[11][PostPositie] == '4') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[11][Locatie] == '19' AND $tableResult[11][PostPositie] == '6') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[11][Locatie] == '20' AND $tableResult[11][PostPositie] == '6') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[11][Locatie] == '21' AND $tableResult[11][PostPositie] == '5') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[11][Locatie] == '28' AND $tableResult[11][PostPositie] == '5') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[11][Locatie] == '21' AND $tableResult[11][PostPositie] == '6') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[11][Locatie] == '28' AND $tableResult[11][PostPositie] == '6') {
				extract($tableResult[11], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}	
	//POS12
		if ($tableResult[12][Locatie] == 'ASC' AND $tableResult[12][PostPositie] == '1') {
			extract($tableResult[12], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[12][Locatie] == 'OPL' AND $tableResult[12][PostPositie] == '1') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[12][Locatie] == 'PC19' AND $tableResult[12][PostPositie] == '1') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[12][Locatie] == 'PC20' AND $tableResult[12][PostPositie] == '1') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[12][Locatie] == 'PC21' AND $tableResult[12][PostPositie] == '1') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[12][Locatie] == 'PC28' AND $tableResult[12][PostPositie] == '1') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[12][Locatie] == '19' AND $tableResult[12][PostPositie] == '1') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[12][Locatie] == '20' AND $tableResult[12][PostPositie] == '1') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[12][Locatie] == 'CVDS' AND $tableResult[12][PostPositie] == '1') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[12][Locatie] == 'CVD' AND $tableResult[12][PostPositie] == '1') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[12][Locatie] == '19' AND $tableResult[12][PostPositie] == '2') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[12][Locatie] == '20' AND $tableResult[12][PostPositie] == '2') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[12][Locatie] == '21' AND $tableResult[12][PostPositie] == '1') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[12][Locatie] == '28' AND $tableResult[12][PostPositie] == '1') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[12][Locatie] == '19' AND $tableResult[12][PostPositie] == '3') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[12][Locatie] == '20' AND $tableResult[12][PostPositie] == '3') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[12][Locatie] == '21' AND $tableResult[12][PostPositie] == '2') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[12][Locatie] == '28' AND $tableResult[12][PostPositie] == '2') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[12][Locatie] == '19' AND $tableResult[12][PostPositie] == '4') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[12][Locatie] == '20' AND $tableResult[12][PostPositie] == '4') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[12][Locatie] == '21' AND $tableResult[12][PostPositie] == '3') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[12][Locatie] == '28' AND $tableResult[12][PostPositie] == '3') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[12][Locatie] == '19' AND $tableResult[12][PostPositie] == '5') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[12][Locatie] == '20' AND $tableResult[12][PostPositie] == '5') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[12][Locatie] == '21' AND $tableResult[12][PostPositie] == '4') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[12][Locatie] == '28' AND $tableResult[12][PostPositie] == '4') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[12][Locatie] == '19' AND $tableResult[12][PostPositie] == '6') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[12][Locatie] == '20' AND $tableResult[12][PostPositie] == '6') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[12][Locatie] == '21' AND $tableResult[12][PostPositie] == '5') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[12][Locatie] == '28' AND $tableResult[12][PostPositie] == '5') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[12][Locatie] == '21' AND $tableResult[12][PostPositie] == '6') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[12][Locatie] == '28' AND $tableResult[12][PostPositie] == '6') {
				extract($tableResult[12], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}	
	//POS13
		if ($tableResult[13][Locatie] == 'ASC' AND $tableResult[13][PostPositie] == '1') {
			extract($tableResult[13], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[13][Locatie] == 'OPL' AND $tableResult[13][PostPositie] == '1') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[13][Locatie] == 'PC19' AND $tableResult[13][PostPositie] == '1') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[13][Locatie] == 'PC20' AND $tableResult[13][PostPositie] == '1') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[13][Locatie] == 'PC21' AND $tableResult[13][PostPositie] == '1') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[13][Locatie] == 'PC28' AND $tableResult[13][PostPositie] == '1') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[13][Locatie] == '19' AND $tableResult[13][PostPositie] == '1') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[13][Locatie] == '20' AND $tableResult[13][PostPositie] == '1') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[13][Locatie] == 'CVDS' AND $tableResult[13][PostPositie] == '1') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[13][Locatie] == 'CVD' AND $tableResult[13][PostPositie] == '1') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[13][Locatie] == '19' AND $tableResult[13][PostPositie] == '2') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[13][Locatie] == '20' AND $tableResult[13][PostPositie] == '2') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[13][Locatie] == '21' AND $tableResult[13][PostPositie] == '1') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[13][Locatie] == '28' AND $tableResult[13][PostPositie] == '1') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[13][Locatie] == '19' AND $tableResult[13][PostPositie] == '3') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[13][Locatie] == '20' AND $tableResult[13][PostPositie] == '3') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[13][Locatie] == '21' AND $tableResult[13][PostPositie] == '2') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[13][Locatie] == '28' AND $tableResult[13][PostPositie] == '2') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[13][Locatie] == '19' AND $tableResult[13][PostPositie] == '4') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[13][Locatie] == '20' AND $tableResult[13][PostPositie] == '4') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[13][Locatie] == '21' AND $tableResult[13][PostPositie] == '3') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[13][Locatie] == '28' AND $tableResult[13][PostPositie] == '3') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[13][Locatie] == '19' AND $tableResult[13][PostPositie] == '5') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[13][Locatie] == '20' AND $tableResult[13][PostPositie] == '5') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[13][Locatie] == '21' AND $tableResult[13][PostPositie] == '4') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[13][Locatie] == '28' AND $tableResult[13][PostPositie] == '4') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[13][Locatie] == '19' AND $tableResult[13][PostPositie] == '6') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[13][Locatie] == '20' AND $tableResult[13][PostPositie] == '6') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[13][Locatie] == '21' AND $tableResult[13][PostPositie] == '5') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[13][Locatie] == '28' AND $tableResult[13][PostPositie] == '5') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[13][Locatie] == '21' AND $tableResult[13][PostPositie] == '6') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[13][Locatie] == '28' AND $tableResult[13][PostPositie] == '6') {
				extract($tableResult[13], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}	
	//POS14
		if ($tableResult[14][Locatie] == 'ASC' AND $tableResult[14][PostPositie] == '1') {
			extract($tableResult[14], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[14][Locatie] == 'OPL' AND $tableResult[14][PostPositie] == '1') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[14][Locatie] == 'PC19' AND $tableResult[14][PostPositie] == '1') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[14][Locatie] == 'PC20' AND $tableResult[14][PostPositie] == '1') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[14][Locatie] == 'PC21' AND $tableResult[14][PostPositie] == '1') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[14][Locatie] == 'PC28' AND $tableResult[14][PostPositie] == '1') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[14][Locatie] == '19' AND $tableResult[14][PostPositie] == '1') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[14][Locatie] == '20' AND $tableResult[14][PostPositie] == '1') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[14][Locatie] == 'CVDS' AND $tableResult[14][PostPositie] == '1') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[14][Locatie] == 'CVD' AND $tableResult[14][PostPositie] == '1') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[14][Locatie] == '19' AND $tableResult[14][PostPositie] == '2') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[14][Locatie] == '20' AND $tableResult[14][PostPositie] == '2') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[14][Locatie] == '21' AND $tableResult[14][PostPositie] == '1') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[14][Locatie] == '28' AND $tableResult[14][PostPositie] == '1') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[14][Locatie] == '19' AND $tableResult[14][PostPositie] == '3') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[14][Locatie] == '20' AND $tableResult[14][PostPositie] == '3') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[14][Locatie] == '21' AND $tableResult[14][PostPositie] == '2') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[14][Locatie] == '28' AND $tableResult[14][PostPositie] == '2') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[14][Locatie] == '19' AND $tableResult[14][PostPositie] == '4') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[14][Locatie] == '20' AND $tableResult[14][PostPositie] == '4') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[14][Locatie] == '21' AND $tableResult[14][PostPositie] == '3') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[14][Locatie] == '28' AND $tableResult[14][PostPositie] == '3') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[14][Locatie] == '19' AND $tableResult[14][PostPositie] == '5') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[14][Locatie] == '20' AND $tableResult[14][PostPositie] == '5') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[14][Locatie] == '21' AND $tableResult[14][PostPositie] == '4') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[14][Locatie] == '28' AND $tableResult[14][PostPositie] == '4') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[14][Locatie] == '19' AND $tableResult[14][PostPositie] == '6') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[14][Locatie] == '20' AND $tableResult[14][PostPositie] == '6') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[14][Locatie] == '21' AND $tableResult[14][PostPositie] == '5') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[14][Locatie] == '28' AND $tableResult[14][PostPositie] == '5') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[14][Locatie] == '21' AND $tableResult[14][PostPositie] == '6') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[14][Locatie] == '28' AND $tableResult[14][PostPositie] == '6') {
				extract($tableResult[14], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}	
	//POS15
		if ($tableResult[15][Locatie] == 'ASC' AND $tableResult[15][PostPositie] == '1') {
			extract($tableResult[15], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[15][Locatie] == 'OPL' AND $tableResult[15][PostPositie] == '1') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[15][Locatie] == 'PC19' AND $tableResult[15][PostPositie] == '1') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[15][Locatie] == 'PC20' AND $tableResult[15][PostPositie] == '1') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[15][Locatie] == 'PC21' AND $tableResult[15][PostPositie] == '1') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[15][Locatie] == 'PC28' AND $tableResult[15][PostPositie] == '1') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[15][Locatie] == '19' AND $tableResult[15][PostPositie] == '1') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[15][Locatie] == '20' AND $tableResult[15][PostPositie] == '1') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[15][Locatie] == 'CVDS' AND $tableResult[15][PostPositie] == '1') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[15][Locatie] == 'CVD' AND $tableResult[15][PostPositie] == '1') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[15][Locatie] == '19' AND $tableResult[15][PostPositie] == '2') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[15][Locatie] == '20' AND $tableResult[15][PostPositie] == '2') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[15][Locatie] == '21' AND $tableResult[15][PostPositie] == '1') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[15][Locatie] == '28' AND $tableResult[15][PostPositie] == '1') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[15][Locatie] == '19' AND $tableResult[15][PostPositie] == '3') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[15][Locatie] == '20' AND $tableResult[15][PostPositie] == '3') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[15][Locatie] == '21' AND $tableResult[15][PostPositie] == '2') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[15][Locatie] == '28' AND $tableResult[15][PostPositie] == '2') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[15][Locatie] == '19' AND $tableResult[15][PostPositie] == '4') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[15][Locatie] == '20' AND $tableResult[15][PostPositie] == '4') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[15][Locatie] == '21' AND $tableResult[15][PostPositie] == '3') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[15][Locatie] == '28' AND $tableResult[15][PostPositie] == '3') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[15][Locatie] == '19' AND $tableResult[15][PostPositie] == '5') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[15][Locatie] == '20' AND $tableResult[15][PostPositie] == '5') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[15][Locatie] == '21' AND $tableResult[15][PostPositie] == '4') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[15][Locatie] == '28' AND $tableResult[15][PostPositie] == '4') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[15][Locatie] == '19' AND $tableResult[15][PostPositie] == '6') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[15][Locatie] == '20' AND $tableResult[15][PostPositie] == '6') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[15][Locatie] == '21' AND $tableResult[15][PostPositie] == '5') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[15][Locatie] == '28' AND $tableResult[15][PostPositie] == '5') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[15][Locatie] == '21' AND $tableResult[15][PostPositie] == '6') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[15][Locatie] == '28' AND $tableResult[15][PostPositie] == '6') {
				extract($tableResult[15], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}	
	//POS16
		if ($tableResult[16][Locatie] == 'ASC' AND $tableResult[16][PostPositie] == '1') {
			extract($tableResult[16], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[16][Locatie] == 'OPL' AND $tableResult[16][PostPositie] == '1') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[16][Locatie] == 'PC19' AND $tableResult[16][PostPositie] == '1') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[16][Locatie] == 'PC20' AND $tableResult[16][PostPositie] == '1') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[16][Locatie] == 'PC21' AND $tableResult[16][PostPositie] == '1') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[16][Locatie] == 'PC28' AND $tableResult[16][PostPositie] == '1') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[16][Locatie] == '19' AND $tableResult[16][PostPositie] == '1') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[16][Locatie] == '20' AND $tableResult[16][PostPositie] == '1') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[16][Locatie] == 'CVDS' AND $tableResult[16][PostPositie] == '1') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[16][Locatie] == 'CVD' AND $tableResult[16][PostPositie] == '1') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[16][Locatie] == '19' AND $tableResult[16][PostPositie] == '2') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[16][Locatie] == '20' AND $tableResult[16][PostPositie] == '2') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[16][Locatie] == '21' AND $tableResult[16][PostPositie] == '1') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[16][Locatie] == '28' AND $tableResult[16][PostPositie] == '1') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[16][Locatie] == '19' AND $tableResult[16][PostPositie] == '3') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[16][Locatie] == '20' AND $tableResult[16][PostPositie] == '3') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[16][Locatie] == '21' AND $tableResult[16][PostPositie] == '2') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[16][Locatie] == '28' AND $tableResult[16][PostPositie] == '2') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[16][Locatie] == '19' AND $tableResult[16][PostPositie] == '4') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[16][Locatie] == '20' AND $tableResult[16][PostPositie] == '4') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[16][Locatie] == '21' AND $tableResult[16][PostPositie] == '3') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[16][Locatie] == '28' AND $tableResult[16][PostPositie] == '3') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[16][Locatie] == '19' AND $tableResult[16][PostPositie] == '5') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[16][Locatie] == '20' AND $tableResult[16][PostPositie] == '5') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[16][Locatie] == '21' AND $tableResult[16][PostPositie] == '4') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[16][Locatie] == '28' AND $tableResult[16][PostPositie] == '4') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[16][Locatie] == '19' AND $tableResult[16][PostPositie] == '6') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[16][Locatie] == '20' AND $tableResult[16][PostPositie] == '6') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[16][Locatie] == '21' AND $tableResult[16][PostPositie] == '5') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[16][Locatie] == '28' AND $tableResult[16][PostPositie] == '5') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[16][Locatie] == '21' AND $tableResult[16][PostPositie] == '6') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[16][Locatie] == '28' AND $tableResult[16][PostPositie] == '6') {
				extract($tableResult[16], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}	
	//POS17
		if ($tableResult[17][Locatie] == 'ASC' AND $tableResult[17][PostPositie] == '1') {
			extract($tableResult[17], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[17][Locatie] == 'OPL' AND $tableResult[17][PostPositie] == '1') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[17][Locatie] == 'PC19' AND $tableResult[17][PostPositie] == '1') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[17][Locatie] == 'PC20' AND $tableResult[17][PostPositie] == '1') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[17][Locatie] == 'PC21' AND $tableResult[17][PostPositie] == '1') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[17][Locatie] == 'PC28' AND $tableResult[17][PostPositie] == '1') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[17][Locatie] == '19' AND $tableResult[17][PostPositie] == '1') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[17][Locatie] == '20' AND $tableResult[17][PostPositie] == '1') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[17][Locatie] == 'CVDS' AND $tableResult[17][PostPositie] == '1') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[17][Locatie] == 'CVD' AND $tableResult[17][PostPositie] == '1') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[17][Locatie] == '19' AND $tableResult[17][PostPositie] == '2') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[17][Locatie] == '20' AND $tableResult[17][PostPositie] == '2') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[17][Locatie] == '21' AND $tableResult[17][PostPositie] == '1') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[17][Locatie] == '28' AND $tableResult[17][PostPositie] == '1') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[17][Locatie] == '19' AND $tableResult[17][PostPositie] == '3') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[17][Locatie] == '20' AND $tableResult[17][PostPositie] == '3') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[17][Locatie] == '21' AND $tableResult[17][PostPositie] == '2') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[17][Locatie] == '28' AND $tableResult[17][PostPositie] == '2') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[17][Locatie] == '19' AND $tableResult[17][PostPositie] == '4') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[17][Locatie] == '20' AND $tableResult[17][PostPositie] == '4') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[17][Locatie] == '21' AND $tableResult[17][PostPositie] == '3') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[17][Locatie] == '28' AND $tableResult[17][PostPositie] == '3') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[17][Locatie] == '19' AND $tableResult[17][PostPositie] == '5') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[17][Locatie] == '20' AND $tableResult[17][PostPositie] == '5') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[17][Locatie] == '21' AND $tableResult[17][PostPositie] == '4') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[17][Locatie] == '28' AND $tableResult[17][PostPositie] == '4') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[17][Locatie] == '19' AND $tableResult[17][PostPositie] == '6') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[17][Locatie] == '20' AND $tableResult[17][PostPositie] == '6') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[17][Locatie] == '21' AND $tableResult[17][PostPositie] == '5') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[17][Locatie] == '28' AND $tableResult[17][PostPositie] == '5') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[17][Locatie] == '21' AND $tableResult[17][PostPositie] == '6') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[17][Locatie] == '28' AND $tableResult[17][PostPositie] == '6') {
				extract($tableResult[17], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}
	//POS18
		if ($tableResult[18][Locatie] == 'ASC' AND $tableResult[18][PostPositie] == '1') {
			extract($tableResult[18], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[18][Locatie] == 'OPL' AND $tableResult[18][PostPositie] == '1') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[18][Locatie] == 'PC19' AND $tableResult[18][PostPositie] == '1') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[18][Locatie] == 'PC20' AND $tableResult[18][PostPositie] == '1') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[18][Locatie] == 'PC21' AND $tableResult[18][PostPositie] == '1') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[18][Locatie] == 'PC28' AND $tableResult[18][PostPositie] == '1') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[18][Locatie] == '19' AND $tableResult[18][PostPositie] == '1') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[18][Locatie] == '20' AND $tableResult[18][PostPositie] == '1') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[18][Locatie] == 'CVDS' AND $tableResult[18][PostPositie] == '1') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[18][Locatie] == 'CVD' AND $tableResult[18][PostPositie] == '1') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[18][Locatie] == '19' AND $tableResult[18][PostPositie] == '2') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[18][Locatie] == '20' AND $tableResult[18][PostPositie] == '2') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[18][Locatie] == '21' AND $tableResult[18][PostPositie] == '1') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[18][Locatie] == '28' AND $tableResult[18][PostPositie] == '1') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[18][Locatie] == '19' AND $tableResult[18][PostPositie] == '3') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[18][Locatie] == '20' AND $tableResult[18][PostPositie] == '3') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[18][Locatie] == '21' AND $tableResult[18][PostPositie] == '2') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[18][Locatie] == '28' AND $tableResult[18][PostPositie] == '2') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[18][Locatie] == '19' AND $tableResult[18][PostPositie] == '4') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[18][Locatie] == '20' AND $tableResult[18][PostPositie] == '4') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[18][Locatie] == '21' AND $tableResult[18][PostPositie] == '3') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[18][Locatie] == '28' AND $tableResult[18][PostPositie] == '3') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[18][Locatie] == '19' AND $tableResult[18][PostPositie] == '5') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[18][Locatie] == '20' AND $tableResult[18][PostPositie] == '5') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[18][Locatie] == '21' AND $tableResult[18][PostPositie] == '4') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[18][Locatie] == '28' AND $tableResult[18][PostPositie] == '4') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[18][Locatie] == '19' AND $tableResult[18][PostPositie] == '6') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[18][Locatie] == '20' AND $tableResult[18][PostPositie] == '6') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[18][Locatie] == '21' AND $tableResult[18][PostPositie] == '5') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[18][Locatie] == '28' AND $tableResult[18][PostPositie] == '5') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[18][Locatie] == '21' AND $tableResult[18][PostPositie] == '6') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[18][Locatie] == '28' AND $tableResult[18][PostPositie] == '6') {
				extract($tableResult[18], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}	
	//POS19
		if ($tableResult[19][Locatie] == 'ASC' AND $tableResult[19][PostPositie] == '1') {
			extract($tableResult[19], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[19][Locatie] == 'OPL' AND $tableResult[19][PostPositie] == '1') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[19][Locatie] == 'PC19' AND $tableResult[19][PostPositie] == '1') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[19][Locatie] == 'PC20' AND $tableResult[19][PostPositie] == '1') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[19][Locatie] == 'PC21' AND $tableResult[19][PostPositie] == '1') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[19][Locatie] == 'PC28' AND $tableResult[19][PostPositie] == '1') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[19][Locatie] == '19' AND $tableResult[19][PostPositie] == '1') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[19][Locatie] == '20' AND $tableResult[19][PostPositie] == '1') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[19][Locatie] == 'CVDS' AND $tableResult[19][PostPositie] == '1') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[19][Locatie] == 'CVD' AND $tableResult[19][PostPositie] == '1') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[19][Locatie] == '19' AND $tableResult[19][PostPositie] == '2') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[19][Locatie] == '20' AND $tableResult[19][PostPositie] == '2') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[19][Locatie] == '21' AND $tableResult[19][PostPositie] == '1') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[19][Locatie] == '28' AND $tableResult[19][PostPositie] == '1') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[19][Locatie] == '19' AND $tableResult[19][PostPositie] == '3') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[19][Locatie] == '20' AND $tableResult[19][PostPositie] == '3') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[19][Locatie] == '21' AND $tableResult[19][PostPositie] == '2') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[19][Locatie] == '28' AND $tableResult[19][PostPositie] == '2') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[19][Locatie] == '19' AND $tableResult[19][PostPositie] == '4') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[19][Locatie] == '20' AND $tableResult[19][PostPositie] == '4') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[19][Locatie] == '21' AND $tableResult[19][PostPositie] == '3') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[19][Locatie] == '28' AND $tableResult[19][PostPositie] == '3') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[19][Locatie] == '19' AND $tableResult[19][PostPositie] == '5') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[19][Locatie] == '20' AND $tableResult[19][PostPositie] == '5') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[19][Locatie] == '21' AND $tableResult[19][PostPositie] == '4') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[19][Locatie] == '28' AND $tableResult[19][PostPositie] == '4') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[19][Locatie] == '19' AND $tableResult[19][PostPositie] == '6') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[19][Locatie] == '20' AND $tableResult[19][PostPositie] == '6') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[19][Locatie] == '21' AND $tableResult[19][PostPositie] == '5') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[19][Locatie] == '28' AND $tableResult[19][PostPositie] == '5') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[19][Locatie] == '21' AND $tableResult[19][PostPositie] == '6') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[19][Locatie] == '28' AND $tableResult[19][PostPositie] == '6') {
				extract($tableResult[19], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}	
	//POS20
		if ($tableResult[20][Locatie] == 'ASC' AND $tableResult[20][PostPositie] == '1') {
			extract($tableResult[20], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[20][Locatie] == 'OPL' AND $tableResult[20][PostPositie] == '1') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[20][Locatie] == 'PC19' AND $tableResult[20][PostPositie] == '1') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[20][Locatie] == 'PC20' AND $tableResult[20][PostPositie] == '1') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[20][Locatie] == 'PC21' AND $tableResult[20][PostPositie] == '1') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[20][Locatie] == 'PC28' AND $tableResult[20][PostPositie] == '1') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[20][Locatie] == '19' AND $tableResult[20][PostPositie] == '1') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[20][Locatie] == '20' AND $tableResult[20][PostPositie] == '1') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[20][Locatie] == 'CVDS' AND $tableResult[20][PostPositie] == '1') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[20][Locatie] == 'CVD' AND $tableResult[20][PostPositie] == '1') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[20][Locatie] == '19' AND $tableResult[20][PostPositie] == '2') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[20][Locatie] == '20' AND $tableResult[20][PostPositie] == '2') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[20][Locatie] == '21' AND $tableResult[20][PostPositie] == '1') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[20][Locatie] == '28' AND $tableResult[20][PostPositie] == '1') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[20][Locatie] == '19' AND $tableResult[20][PostPositie] == '3') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[20][Locatie] == '20' AND $tableResult[20][PostPositie] == '3') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[20][Locatie] == '21' AND $tableResult[20][PostPositie] == '2') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[20][Locatie] == '28' AND $tableResult[20][PostPositie] == '2') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[20][Locatie] == '19' AND $tableResult[20][PostPositie] == '4') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[20][Locatie] == '20' AND $tableResult[20][PostPositie] == '4') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[20][Locatie] == '21' AND $tableResult[20][PostPositie] == '3') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[20][Locatie] == '28' AND $tableResult[20][PostPositie] == '3') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[20][Locatie] == '19' AND $tableResult[20][PostPositie] == '5') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[20][Locatie] == '20' AND $tableResult[20][PostPositie] == '5') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[20][Locatie] == '21' AND $tableResult[20][PostPositie] == '4') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[20][Locatie] == '28' AND $tableResult[20][PostPositie] == '4') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[20][Locatie] == '19' AND $tableResult[20][PostPositie] == '6') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[20][Locatie] == '20' AND $tableResult[20][PostPositie] == '6') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[20][Locatie] == '21' AND $tableResult[20][PostPositie] == '5') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[20][Locatie] == '28' AND $tableResult[20][PostPositie] == '5') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[20][Locatie] == '21' AND $tableResult[20][PostPositie] == '6') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[20][Locatie] == '28' AND $tableResult[20][PostPositie] == '6') {
				extract($tableResult[20], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}	
	//POS21
		if ($tableResult[21][Locatie] == 'ASC' AND $tableResult[21][PostPositie] == '1') {
			extract($tableResult[21], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[21][Locatie] == 'OPL' AND $tableResult[21][PostPositie] == '1') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[21][Locatie] == 'PC19' AND $tableResult[21][PostPositie] == '1') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[21][Locatie] == 'PC20' AND $tableResult[21][PostPositie] == '1') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[21][Locatie] == 'PC21' AND $tableResult[21][PostPositie] == '1') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[21][Locatie] == 'PC28' AND $tableResult[21][PostPositie] == '1') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[21][Locatie] == '19' AND $tableResult[21][PostPositie] == '1') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[21][Locatie] == '20' AND $tableResult[21][PostPositie] == '1') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[21][Locatie] == 'CVDS' AND $tableResult[21][PostPositie] == '1') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[21][Locatie] == 'CVD' AND $tableResult[21][PostPositie] == '1') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[21][Locatie] == '19' AND $tableResult[21][PostPositie] == '2') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[21][Locatie] == '20' AND $tableResult[21][PostPositie] == '2') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[21][Locatie] == '21' AND $tableResult[21][PostPositie] == '1') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[21][Locatie] == '28' AND $tableResult[21][PostPositie] == '1') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[21][Locatie] == '19' AND $tableResult[21][PostPositie] == '3') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[21][Locatie] == '20' AND $tableResult[21][PostPositie] == '3') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[21][Locatie] == '21' AND $tableResult[21][PostPositie] == '2') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[21][Locatie] == '28' AND $tableResult[21][PostPositie] == '2') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[21][Locatie] == '19' AND $tableResult[21][PostPositie] == '4') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[21][Locatie] == '20' AND $tableResult[21][PostPositie] == '4') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[21][Locatie] == '21' AND $tableResult[21][PostPositie] == '3') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[21][Locatie] == '28' AND $tableResult[21][PostPositie] == '3') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[21][Locatie] == '19' AND $tableResult[21][PostPositie] == '5') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[21][Locatie] == '20' AND $tableResult[21][PostPositie] == '5') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[21][Locatie] == '21' AND $tableResult[21][PostPositie] == '4') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[21][Locatie] == '28' AND $tableResult[21][PostPositie] == '4') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[21][Locatie] == '19' AND $tableResult[21][PostPositie] == '6') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[21][Locatie] == '20' AND $tableResult[21][PostPositie] == '6') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[21][Locatie] == '21' AND $tableResult[21][PostPositie] == '5') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[21][Locatie] == '28' AND $tableResult[21][PostPositie] == '5') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[21][Locatie] == '21' AND $tableResult[21][PostPositie] == '6') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[21][Locatie] == '28' AND $tableResult[21][PostPositie] == '6') {
				extract($tableResult[21], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}	
	//POS22
		if ($tableResult[22][Locatie] == 'ASC' AND $tableResult[22][PostPositie] == '1') {
			extract($tableResult[22], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[22][Locatie] == 'OPL' AND $tableResult[22][PostPositie] == '1') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[22][Locatie] == 'PC19' AND $tableResult[22][PostPositie] == '1') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[22][Locatie] == 'PC20' AND $tableResult[22][PostPositie] == '1') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[22][Locatie] == 'PC21' AND $tableResult[22][PostPositie] == '1') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[22][Locatie] == 'PC28' AND $tableResult[22][PostPositie] == '1') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[22][Locatie] == '19' AND $tableResult[22][PostPositie] == '1') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[22][Locatie] == '20' AND $tableResult[22][PostPositie] == '1') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[22][Locatie] == 'CVDS' AND $tableResult[22][PostPositie] == '1') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[22][Locatie] == 'CVD' AND $tableResult[22][PostPositie] == '1') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[22][Locatie] == '19' AND $tableResult[22][PostPositie] == '2') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[22][Locatie] == '20' AND $tableResult[22][PostPositie] == '2') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[22][Locatie] == '21' AND $tableResult[22][PostPositie] == '1') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[22][Locatie] == '28' AND $tableResult[22][PostPositie] == '1') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[22][Locatie] == '19' AND $tableResult[22][PostPositie] == '3') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[22][Locatie] == '20' AND $tableResult[22][PostPositie] == '3') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[22][Locatie] == '21' AND $tableResult[22][PostPositie] == '2') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[22][Locatie] == '28' AND $tableResult[22][PostPositie] == '2') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[22][Locatie] == '19' AND $tableResult[22][PostPositie] == '4') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[22][Locatie] == '20' AND $tableResult[22][PostPositie] == '4') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[22][Locatie] == '21' AND $tableResult[22][PostPositie] == '3') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[22][Locatie] == '28' AND $tableResult[22][PostPositie] == '3') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[22][Locatie] == '19' AND $tableResult[22][PostPositie] == '5') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[22][Locatie] == '20' AND $tableResult[22][PostPositie] == '5') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[22][Locatie] == '21' AND $tableResult[22][PostPositie] == '4') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[22][Locatie] == '28' AND $tableResult[22][PostPositie] == '4') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[22][Locatie] == '19' AND $tableResult[22][PostPositie] == '6') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[22][Locatie] == '20' AND $tableResult[22][PostPositie] == '6') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[22][Locatie] == '21' AND $tableResult[22][PostPositie] == '5') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[22][Locatie] == '28' AND $tableResult[22][PostPositie] == '5') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[22][Locatie] == '21' AND $tableResult[22][PostPositie] == '6') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[22][Locatie] == '28' AND $tableResult[22][PostPositie] == '6') {
				extract($tableResult[22], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}	
	//POS23
		if ($tableResult[23][Locatie] == 'ASC' AND $tableResult[23][PostPositie] == '1') {
			extract($tableResult[23], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[23][Locatie] == 'OPL' AND $tableResult[23][PostPositie] == '1') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[23][Locatie] == 'PC19' AND $tableResult[23][PostPositie] == '1') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[23][Locatie] == 'PC20' AND $tableResult[23][PostPositie] == '1') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[23][Locatie] == 'PC21' AND $tableResult[23][PostPositie] == '1') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[23][Locatie] == 'PC28' AND $tableResult[23][PostPositie] == '1') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[23][Locatie] == '19' AND $tableResult[23][PostPositie] == '1') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[23][Locatie] == '20' AND $tableResult[23][PostPositie] == '1') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[23][Locatie] == 'CVDS' AND $tableResult[23][PostPositie] == '1') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[23][Locatie] == 'CVD' AND $tableResult[23][PostPositie] == '1') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[23][Locatie] == '19' AND $tableResult[23][PostPositie] == '2') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[23][Locatie] == '20' AND $tableResult[23][PostPositie] == '2') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[23][Locatie] == '21' AND $tableResult[23][PostPositie] == '1') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[23][Locatie] == '28' AND $tableResult[23][PostPositie] == '1') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[23][Locatie] == '19' AND $tableResult[23][PostPositie] == '3') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[23][Locatie] == '20' AND $tableResult[23][PostPositie] == '3') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[23][Locatie] == '21' AND $tableResult[23][PostPositie] == '2') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[23][Locatie] == '28' AND $tableResult[23][PostPositie] == '2') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[23][Locatie] == '19' AND $tableResult[23][PostPositie] == '4') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[23][Locatie] == '20' AND $tableResult[23][PostPositie] == '4') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[23][Locatie] == '21' AND $tableResult[23][PostPositie] == '3') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[23][Locatie] == '28' AND $tableResult[23][PostPositie] == '3') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[23][Locatie] == '19' AND $tableResult[23][PostPositie] == '5') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[23][Locatie] == '20' AND $tableResult[23][PostPositie] == '5') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[23][Locatie] == '21' AND $tableResult[23][PostPositie] == '4') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[23][Locatie] == '28' AND $tableResult[23][PostPositie] == '4') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[23][Locatie] == '19' AND $tableResult[23][PostPositie] == '6') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[23][Locatie] == '20' AND $tableResult[23][PostPositie] == '6') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[23][Locatie] == '21' AND $tableResult[23][PostPositie] == '5') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[23][Locatie] == '28' AND $tableResult[23][PostPositie] == '5') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[23][Locatie] == '21' AND $tableResult[23][PostPositie] == '6') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[23][Locatie] == '28' AND $tableResult[23][PostPositie] == '6') {
				extract($tableResult[23], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}
	//POS24
		if ($tableResult[24][Locatie] == 'ASC' AND $tableResult[24][PostPositie] == '1') {
			extract($tableResult[24], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[24][Locatie] == 'OPL' AND $tableResult[24][PostPositie] == '1') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[24][Locatie] == 'PC19' AND $tableResult[24][PostPositie] == '1') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[24][Locatie] == 'PC20' AND $tableResult[24][PostPositie] == '1') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[24][Locatie] == 'PC21' AND $tableResult[24][PostPositie] == '1') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[24][Locatie] == 'PC28' AND $tableResult[24][PostPositie] == '1') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[24][Locatie] == '19' AND $tableResult[24][PostPositie] == '1') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[24][Locatie] == '20' AND $tableResult[24][PostPositie] == '1') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[24][Locatie] == 'CVDS' AND $tableResult[24][PostPositie] == '1') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[24][Locatie] == 'CVD' AND $tableResult[24][PostPositie] == '1') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[24][Locatie] == '19' AND $tableResult[24][PostPositie] == '2') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[24][Locatie] == '20' AND $tableResult[24][PostPositie] == '2') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[24][Locatie] == '21' AND $tableResult[24][PostPositie] == '1') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[24][Locatie] == '28' AND $tableResult[24][PostPositie] == '1') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[24][Locatie] == '19' AND $tableResult[24][PostPositie] == '3') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[24][Locatie] == '20' AND $tableResult[24][PostPositie] == '3') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[24][Locatie] == '21' AND $tableResult[24][PostPositie] == '2') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[24][Locatie] == '28' AND $tableResult[24][PostPositie] == '2') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[24][Locatie] == '19' AND $tableResult[24][PostPositie] == '4') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[24][Locatie] == '20' AND $tableResult[24][PostPositie] == '4') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[24][Locatie] == '21' AND $tableResult[24][PostPositie] == '3') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[24][Locatie] == '28' AND $tableResult[24][PostPositie] == '3') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[24][Locatie] == '19' AND $tableResult[24][PostPositie] == '5') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[24][Locatie] == '20' AND $tableResult[24][PostPositie] == '5') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[24][Locatie] == '21' AND $tableResult[24][PostPositie] == '4') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[24][Locatie] == '28' AND $tableResult[24][PostPositie] == '4') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[24][Locatie] == '19' AND $tableResult[24][PostPositie] == '6') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[24][Locatie] == '20' AND $tableResult[24][PostPositie] == '6') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[24][Locatie] == '21' AND $tableResult[24][PostPositie] == '5') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[24][Locatie] == '28' AND $tableResult[24][PostPositie] == '5') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[24][Locatie] == '21' AND $tableResult[24][PostPositie] == '6') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[24][Locatie] == '28' AND $tableResult[24][PostPositie] == '6') {
				extract($tableResult[24], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}
	//POS25
		if ($tableResult[25][Locatie] == 'ASC' AND $tableResult[25][PostPositie] == '1') {
			extract($tableResult[25], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[25][Locatie] == 'OPL' AND $tableResult[25][PostPositie] == '1') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[25][Locatie] == 'PC19' AND $tableResult[25][PostPositie] == '1') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[25][Locatie] == 'PC20' AND $tableResult[25][PostPositie] == '1') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[25][Locatie] == 'PC21' AND $tableResult[25][PostPositie] == '1') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[25][Locatie] == 'PC28' AND $tableResult[25][PostPositie] == '1') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[25][Locatie] == '19' AND $tableResult[25][PostPositie] == '1') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[25][Locatie] == '20' AND $tableResult[25][PostPositie] == '1') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[25][Locatie] == 'CVDS' AND $tableResult[25][PostPositie] == '1') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[25][Locatie] == 'CVD' AND $tableResult[25][PostPositie] == '1') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[25][Locatie] == '19' AND $tableResult[25][PostPositie] == '2') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[25][Locatie] == '20' AND $tableResult[25][PostPositie] == '2') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[25][Locatie] == '21' AND $tableResult[25][PostPositie] == '1') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[25][Locatie] == '28' AND $tableResult[25][PostPositie] == '1') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[25][Locatie] == '19' AND $tableResult[25][PostPositie] == '3') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[25][Locatie] == '20' AND $tableResult[25][PostPositie] == '3') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[25][Locatie] == '21' AND $tableResult[25][PostPositie] == '2') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[25][Locatie] == '28' AND $tableResult[25][PostPositie] == '2') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[25][Locatie] == '19' AND $tableResult[25][PostPositie] == '4') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[25][Locatie] == '20' AND $tableResult[25][PostPositie] == '4') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[25][Locatie] == '21' AND $tableResult[25][PostPositie] == '3') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[25][Locatie] == '28' AND $tableResult[25][PostPositie] == '3') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[25][Locatie] == '19' AND $tableResult[25][PostPositie] == '5') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[25][Locatie] == '20' AND $tableResult[25][PostPositie] == '5') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[25][Locatie] == '21' AND $tableResult[25][PostPositie] == '4') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[25][Locatie] == '28' AND $tableResult[25][PostPositie] == '4') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[25][Locatie] == '19' AND $tableResult[25][PostPositie] == '6') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[25][Locatie] == '20' AND $tableResult[25][PostPositie] == '6') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[25][Locatie] == '21' AND $tableResult[25][PostPositie] == '5') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[25][Locatie] == '28' AND $tableResult[25][PostPositie] == '5') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[25][Locatie] == '21' AND $tableResult[25][PostPositie] == '6') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[25][Locatie] == '28' AND $tableResult[25][PostPositie] == '6') {
				extract($tableResult[25], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}
	//POS26
		if ($tableResult[26][Locatie] == 'ASC' AND $tableResult[26][PostPositie] == '1') {
			extract($tableResult[26], EXTR_PREFIX_ALL, "LocPosASC");
		}
		elseif ($tableResult[26][Locatie] == 'OPL' AND $tableResult[26][PostPositie] == '1') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPosOPL");
		}
		elseif ($tableResult[26][Locatie] == 'PC19' AND $tableResult[26][PostPositie] == '1') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPosPC19");
		}
		elseif ($tableResult[26][Locatie] == 'PC20' AND $tableResult[26][PostPositie] == '1') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPosPC20");
		}
		elseif ($tableResult[26][Locatie] == 'PC21' AND $tableResult[26][PostPositie] == '1') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPosPC21");
		}
		elseif ($tableResult[26][Locatie] == 'PC28' AND $tableResult[26][PostPositie] == '1') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPosPC28");
		}
		elseif ($tableResult[26][Locatie] == '19' AND $tableResult[26][PostPositie] == '1') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos191");
		}
		elseif ($tableResult[26][Locatie] == '20' AND $tableResult[26][PostPositie] == '1') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos201");
		}
		elseif ($tableResult[26][Locatie] == 'CVDS' AND $tableResult[26][PostPositie] == '1') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPosCVDS");
		}
		elseif ($tableResult[26][Locatie] == 'CVD' AND $tableResult[26][PostPositie] == '1') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPosCVD");
		}
		elseif ($tableResult[26][Locatie] == '19' AND $tableResult[26][PostPositie] == '2') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos192");
		}
		elseif ($tableResult[26][Locatie] == '20' AND $tableResult[26][PostPositie] == '2') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos202");
		}
		elseif ($tableResult[26][Locatie] == '21' AND $tableResult[26][PostPositie] == '1') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos211");
		}
		elseif ($tableResult[26][Locatie] == '28' AND $tableResult[26][PostPositie] == '1') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos281");
		}
		elseif ($tableResult[26][Locatie] == '19' AND $tableResult[26][PostPositie] == '3') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos193");
		}
		elseif ($tableResult[26][Locatie] == '20' AND $tableResult[26][PostPositie] == '3') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos203");
		}
		elseif ($tableResult[26][Locatie] == '21' AND $tableResult[26][PostPositie] == '2') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos212");
		}
		elseif ($tableResult[26][Locatie] == '28' AND $tableResult[26][PostPositie] == '2') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos282");
		}
		elseif ($tableResult[26][Locatie] == '19' AND $tableResult[26][PostPositie] == '4') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos194");
		}
		elseif ($tableResult[26][Locatie] == '20' AND $tableResult[26][PostPositie] == '4') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos204");
		}
		elseif ($tableResult[26][Locatie] == '21' AND $tableResult[26][PostPositie] == '3') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos213");
		}
		elseif ($tableResult[26][Locatie] == '28' AND $tableResult[26][PostPositie] == '3') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos283");
		}
		elseif ($tableResult[26][Locatie] == '19' AND $tableResult[26][PostPositie] == '5') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos195");
		}
		elseif ($tableResult[26][Locatie] == '20' AND $tableResult[26][PostPositie] == '5') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos205");
		}
		elseif ($tableResult[26][Locatie] == '21' AND $tableResult[26][PostPositie] == '4') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos214");
		}
		elseif ($tableResult[26][Locatie] == '28' AND $tableResult[26][PostPositie] == '4') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos284");
		}
		elseif ($tableResult[26][Locatie] == '19' AND $tableResult[26][PostPositie] == '6') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos196");
		}
		elseif ($tableResult[26][Locatie] == '20' AND $tableResult[26][PostPositie] == '6') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos206");
		}
		elseif ($tableResult[26][Locatie] == '21' AND $tableResult[26][PostPositie] == '5') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos215");
		}
		elseif ($tableResult[26][Locatie] == '28' AND $tableResult[26][PostPositie] == '5') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos285");
		}
		elseif ($tableResult[26][Locatie] == '21' AND $tableResult[26][PostPositie] == '6') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos216");
		}
		elseif ($tableResult[26][Locatie] == '28' AND $tableResult[26][PostPositie] == '6') {
				extract($tableResult[26], EXTR_PREFIX_ALL, "LocPos286");
		}
		else {
		}

// De competenteis worden nu nog niet ingelezen dus we maken een tijdelijke variabele aan tot deze toegevoegd zijn.
	if ($LocPosASC_RelatieNr != '') {$LocPosASC_Competenties = $LocPosASC_CompetentiesRegel1 . $LocPosASC_CompetentiesRegel2;}
	if ($LocPosOPL_RelatieNr != '') {$LocPosOPL_Competenties = $LocPosOPL_CompetentiesRegel1 . $LocPosOPL_CompetentiesRegel2;}
	if ($LocPosPC19_RelatieNr != '') {$LocPosPC19_Competenties = $LocPosPC19_CompetentiesRegel1 . $LocPosPC19_CompetentiesRegel2;}
	if ($LocPosPC20_RelatieNr != '') {$LocPosPC20_Competenties = $LocPosPC20_CompetentiesRegel1 . $LocPosPC20_CompetentiesRegel2;}
	if ($LocPosPC21_RelatieNr != '') {$LocPosPC21_Competenties = $LocPosPC21_CompetentiesRegel1 . $LocPosPC21_CompetentiesRegel2;}
	if ($LocPosPC28_RelatieNr != '') {$LocPosPC28_Competenties = $LocPosPC28_CompetentiesRegel1 . $LocPosPC28_CompetentiesRegel2;}
	if ($LocPos191_RelatieNr != '') {$LocPos191_Competenties = $LocPos191_CompetentiesRegel1 . $LocPos191_CompetentiesRegel2;}
	if ($LocPos201_RelatieNr != '') {$LocPos201_Competenties = $LocPos201_CompetentiesRegel1 . $LocPos201_CompetentiesRegel2;}
	if ($LocPosCVDS_RelatieNr != '') {$LocPosCVDS_Competenties = $LocPosCVDS_CompetentiesRegel1 . $LocPosCVDS_CompetentiesRegel2;}
	if ($LocPosCVD_RelatieNr != '') {$LocPosCVD_Competenties = $LocPosCVD_CompetentiesRegel1 . $LocPosCVD_CompetentiesRegel2;}
	if ($LocPos192_RelatieNr != '') {$LocPos192_Competenties = $LocPos192_CompetentiesRegel1 . $LocPos192_CompetentiesRegel2;}
	if ($LocPos202_RelatieNr != '') {$LocPos202_Competenties = $LocPos202_CompetentiesRegel1 . $LocPos202_CompetentiesRegel2;}
	if ($LocPos211_RelatieNr != '') {$LocPos211_Competenties = $LocPos211_CompetentiesRegel1 . $LocPos211_CompetentiesRegel2;}
	if ($LocPos281_RelatieNr != '') {$LocPos281_Competenties = $LocPos281_CompetentiesRegel1 . $LocPos281_CompetentiesRegel2;}
	if ($LocPos193_RelatieNr != '') {$LocPos193_Competenties = $LocPos193_CompetentiesRegel1 . $LocPos193_CompetentiesRegel2;}
	if ($LocPos203_RelatieNr != '') {$LocPos203_Competenties = $LocPos203_CompetentiesRegel1 . $LocPos203_CompetentiesRegel2;}
	if ($LocPos212_RelatieNr != '') {$LocPos212_Competenties = $LocPos212_CompetentiesRegel1 . $LocPos212_CompetentiesRegel2;}
	if ($LocPos282_RelatieNr != '') {$LocPos282_Competenties = $LocPos282_CompetentiesRegel1 . $LocPos282_CompetentiesRegel2;}
	if ($LocPos194_RelatieNr != '') {$LocPos194_Competenties = $LocPos194_CompetentiesRegel1 . $LocPos194_CompetentiesRegel2;}
	if ($LocPos204_RelatieNr != '') {$LocPos204_Competenties = $LocPos204_CompetentiesRegel1 . $LocPos204_CompetentiesRegel2;}
	if ($LocPos213_RelatieNr != '') {$LocPos213_Competenties = $LocPos213_CompetentiesRegel1 . $LocPos213_CompetentiesRegel2;}
	if ($LocPos283_RelatieNr != '') {$LocPos283_Competenties = $LocPos283_CompetentiesRegel1 . $LocPos283_CompetentiesRegel2;}
	if ($LocPos195_RelatieNr != '') {$LocPos195_Competenties = $LocPos195_CompetentiesRegel1 . $LocPos195_CompetentiesRegel2;}
	if ($LocPos205_RelatieNr != '') {$LocPos205_Competenties = $LocPos205_CompetentiesRegel1 . $LocPos205_CompetentiesRegel2;}
	if ($LocPos214_RelatieNr != '') {$LocPos214_Competenties = $LocPos214_CompetentiesRegel1 . $LocPos214_CompetentiesRegel2;}
	if ($LocPos284_RelatieNr != '') {$LocPos284_Competenties = $LocPos284_CompetentiesRegel1 . $LocPos284_CompetentiesRegel2;}
	if ($LocPos196_RelatieNr != '') {$LocPos196_Competenties = $LocPos196_CompetentiesRegel1 . $LocPos196_CompetentiesRegel2;}
	if ($LocPos206_RelatieNr != '') {$LocPos206_Competenties = $LocPos206_CompetentiesRegel1 . $LocPos206_CompetentiesRegel2;}
	if ($LocPos215_RelatieNr != '') {$LocPos215_Competenties = $LocPos215_CompetentiesRegel1 . $LocPos215_CompetentiesRegel2;}
	if ($LocPos285_RelatieNr != '') {$LocPos285_Competenties = $LocPos285_CompetentiesRegel1 . $LocPos285_CompetentiesRegel2;}
	if ($LocPos216_RelatieNr != '') {$LocPos216_Competenties = $LocPos216_CompetentiesRegel1 . $LocPos216_CompetentiesRegel2;}
	if ($LocPos286_RelatieNr != '') {$LocPos286_Competenties = $LocPos286_CompetentiesRegel1 . $LocPos286_CompetentiesRegel2;}

	//Formaat digitale pasfoto: 148 pixels (breedte) x 184 pixels (hoogte)
	$FOTO_CODERING = '<img src="data:image/jpeg;base64,';
	$FOTO_STYLE = '" style="display:block;width:100%;height:100%;"/>';
	$FOTO_FOLDER = './pasfotos/';
	//$FOTO_FOLDER = 'https://trb.nu/clubredders/pasfotos/';
	//$FOTO_FOLDER = 'https://SU_WP:Wijwillen1pasfoto@trb.nu/clubredders/pasfotos/';

	//Zet de variabelen voor het rooster.
	
	//ASC
	if ($LocPosASC_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPosASC_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPosASC_RelatieNr.'.jpg')) {
		$FOTO_ASC = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPosASC_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_ASC = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_ASC = $LocPosASC_VolledigeNaam;
		$AGE_ASC = $LocPosASC_Leeftijd . ' jaar';
		if ($LocPosASC_WekenErvaring < '1') {
		$EXP_ASC = 'Geen';
		} elseif ($LocPosASC_WekenErvaring == '1') {
		$EXP_ASC = $LocPosASC_WekenErvaring . ' week';
		} else {
		$EXP_ASC = $LocPosASC_WekenErvaring . ' weken';
		}
		$PVB_ASC = $LocPosASC_Competenties;
	}
	//OPL
	if ($LocPosOPL_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPosOPL_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPosOPL_RelatieNr.'.jpg')) {
		$FOTO_OPL = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPosOPL_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_OPL = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_OPL = $LocPosOPL_VolledigeNaam;
		$AGE_OPL = $LocPosOPL_Leeftijd . ' jaar';
		if ($LocPosOPL_WekenErvaring < '1') {
		$EXP_OPL = 'Geen';
		} elseif ($LocPosOPL_WekenErvaring == '1') {
		$EXP_OPL = $LocPosOPL_WekenErvaring . ' week';
		} else {
		$EXP_OPL = $LocPosOPL_WekenErvaring . ' weken';
		}
		$PVB_OPL = $LocPosOPL_Competenties;
	}
	//PC19
	if ($LocPosPC19_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPosPC19_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPosPC19_RelatieNr.'.jpg')) {
		$FOTO_PC19 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPosPC19_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_PC19 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_PC19 = $LocPosPC19_VolledigeNaam;
		$AGE_PC19 = $LocPosPC19_Leeftijd . ' jaar';
		if ($LocPosPC19_WekenErvaring < '1') {
		$EXP_PC19 = 'Geen';
		} elseif ($LocPosPC19_WekenErvaring == '1') {
		$EXP_PC19 = $LocPosPC19_WekenErvaring . ' week';
		} else {
		$EXP_PC19 = $LocPosPC19_WekenErvaring . ' weken';
		}
		$PVB_PC19 = $LocPosPC19_Competenties;
	}
	//PC20
	if ($LocPosPC20_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPosPC20_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPosPC20_RelatieNr.'.jpg')) {
		$FOTO_PC20 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPosPC20_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_PC20 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_PC20 = $LocPosPC20_VolledigeNaam;
		$AGE_PC20 = $LocPosPC20_Leeftijd . ' jaar';
		if ($LocPosPC20_WekenErvaring < '1') {
		$EXP_PC20 = 'Geen';
		} elseif ($LocPosPC20_WekenErvaring == '1') {
		$EXP_PC20 = $LocPosPC20_WekenErvaring . ' week';
		} else {
		$EXP_PC20 = $LocPosPC20_WekenErvaring . ' weken';
		}
		$PVB_PC20 = $LocPosPC20_Competenties;
	}
	//PC21
	if ($LocPosPC21_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPosPC21_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPosPC21_RelatieNr.'.jpg')) {
		$FOTO_PC21 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPosPC21_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_PC21 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_PC21 = $LocPosPC21_VolledigeNaam;
		$AGE_PC21 = $LocPosPC21_Leeftijd . ' jaar';
		if ($LocPosPC21_WekenErvaring < '1') {
		$EXP_PC21 = 'Geen';
		} elseif ($LocPosPC21_WekenErvaring == '1') {
		$EXP_PC21 = $LocPosPC21_WekenErvaring . ' week';
		} else {
		$EXP_PC21 = $LocPosPC21_WekenErvaring . ' weken';
		}
		$PVB_PC21 = $LocPosPC21_Competenties;
	}
	//PC28
	if ($LocPosPC28_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPosPC28_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPosPC28_RelatieNr.'.jpg')) {
		$FOTO_PC28 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPosPC28_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_PC28 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_PC28 = $LocPosPC28_VolledigeNaam;
		$AGE_PC28 = $LocPosPC28_Leeftijd . ' jaar';
		if ($LocPosPC28_WekenErvaring < '1') {
		$EXP_PC28 = 'Geen';
		} elseif ($LocPosPC28_WekenErvaring == '1') {
		$EXP_PC28 = $LocPosPC28_WekenErvaring . ' week';
		} else {
		$EXP_PC28 = $LocPosPC28_WekenErvaring . ' weken';
		}
		$PVB_PC28 = $LocPosPC28_Competenties;
	}
	//191
	if ($LocPos191_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos191_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos191_RelatieNr.'.jpg')) {
		$FOTO_191 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos191_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_191 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_191 = $LocPos191_VolledigeNaam;
		$AGE_191 = $LocPos191_Leeftijd . ' jaar';
		if ($LocPos191_WekenErvaring < '1') {
		$EXP_191 = 'Geen';
		} elseif ($LocPos191_WekenErvaring == '1') {
		$EXP_191 = $LocPos191_WekenErvaring . ' week';
		} else {
		$EXP_191 = $LocPos191_WekenErvaring . ' weken';
		}
		$PVB_191 = $LocPos191_Competenties;
	}
	//201
	if ($LocPos201_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos201_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos201_RelatieNr.'.jpg')) {
		$FOTO_201 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos201_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_201 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_201 = $LocPos201_VolledigeNaam;
		$AGE_201 = $LocPos201_Leeftijd . ' jaar';
		if ($LocPos201_WekenErvaring < '1') {
		$EXP_201 = 'Geen';
		} elseif ($LocPos201_WekenErvaring == '1') {
		$EXP_201 = $LocPos201_WekenErvaring . ' week';
		} else {
		$EXP_201 = $LocPos201_WekenErvaring . ' weken';
		}
		$PVB_201 = $LocPos201_Competenties;
	}
	//CVDS
	if ($LocPosCVDS_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPosCVDS_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPosCVDS_RelatieNr.'.jpg')) {
		$FOTO_CVDS = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPosCVDS_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_CVDS = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_CVDS = $LocPosCVDS_VolledigeNaam;
		$AGE_CVDS = $LocPosCVDS_Leeftijd . ' jaar';
		if ($LocPosCVDS_WekenErvaring < '1') {
		$EXP_CVDS = 'Geen';
		} elseif ($LocPosCVDS_WekenErvaring == '1') {
		$EXP_CVDS = $LocPosCVDS_WekenErvaring . ' week';
		} else {
		$EXP_CVDS = $LocPosCVDS_WekenErvaring . ' weken';
		}
		$PVB_CVDS = $LocPosCVDS_Competenties;
	}
	//CVD
	if ($LocPosCVD_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPosCVD_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPosCVD_RelatieNr.'.jpg')) {
		$FOTO_CVD = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPosCVD_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_CVD = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_CVD = $LocPosCVD_VolledigeNaam;
		$AGE_CVD = $LocPosCVD_Leeftijd . ' jaar';
		if ($LocPosCVD_WekenErvaring < '1') {
		$EXP_CVD = 'Geen';
		} elseif ($LocPosCVD_WekenErvaring == '1') {
		$EXP_CVD = $LocPosCVD_WekenErvaring . ' week';
		} else {
		$EXP_CVD = $LocPosCVD_WekenErvaring . ' weken';
		}
		$PVB_CVD = $LocPosCVD_Competenties;
	}
	//192
	if ($LocPos192_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos192_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos192_RelatieNr.'.jpg')) {
		$FOTO_192 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos192_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_192 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_192 = $LocPos192_VolledigeNaam;
		$AGE_192 = $LocPos192_Leeftijd . ' jaar';
		if ($LocPos192_WekenErvaring < '1') {
		$EXP_192 = 'Geen';
		} elseif ($LocPos192_WekenErvaring == '1') {
		$EXP_192 = $LocPos192_WekenErvaring . ' week';
		} else {
		$EXP_192 = $LocPos192_WekenErvaring . ' weken';
		}
		$PVB_192 = $LocPos192_Competenties;
	}
	//202
	if ($LocPos202_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos202_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos202_RelatieNr.'.jpg')) {
		$FOTO_202 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos202_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_202 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_202 = $LocPos202_VolledigeNaam;
		$AGE_202 = $LocPos202_Leeftijd . ' jaar';
		if ($LocPos202_WekenErvaring < '1') {
		$EXP_202 = 'Geen';
		} elseif ($LocPos202_WekenErvaring == '1') {
		$EXP_202 = $LocPos202_WekenErvaring . ' week';
		} else {
		$EXP_202 = $LocPos202_WekenErvaring . ' weken';
		}
		$PVB_202 = $LocPos202_Competenties;
	}
	//211
	if ($LocPos211_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos211_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos211_RelatieNr.'.jpg')) {
		$FOTO_211 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos211_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_211 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_211 = $LocPos211_VolledigeNaam;
		$AGE_211 = $LocPos211_Leeftijd . ' jaar';
		if ($LocPos211_WekenErvaring < '1') {
		$EXP_211 = 'Geen';
		} elseif ($LocPos211_WekenErvaring == '1') {
		$EXP_211 = $LocPos211_WekenErvaring . ' week';
		} else {
		$EXP_211 = $LocPos211_WekenErvaring . ' weken';
		}
		$PVB_211 = $LocPos211_Competenties;
	}
	//281
	if ($LocPos281_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos281_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos281_RelatieNr.'.jpg')) {
		$FOTO_281 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos281_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_281 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_281 = $LocPos281_VolledigeNaam;
		$AGE_281 = $LocPos281_Leeftijd . ' jaar';
		if ($LocPos281_WekenErvaring < '1') {
		$EXP_281 = 'Geen';
		} elseif ($LocPos281_WekenErvaring == '1') {
		$EXP_281 = $LocPos281_WekenErvaring . ' week';
		} else {
		$EXP_281 = $LocPos281_WekenErvaring . ' weken';
		}
		$PVB_281 = $LocPos281_Competenties;
	}
	//193
	if ($LocPos193_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos193_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos193_RelatieNr.'.jpg')) {
		$FOTO_193 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos193_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_193 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_193 = $LocPos193_VolledigeNaam;
		$AGE_193 = $LocPos193_Leeftijd . ' jaar';
		if ($LocPos193_WekenErvaring < '1') {
		$EXP_193 = 'Geen';
		} elseif ($LocPos193_WekenErvaring == '1') {
		$EXP_193 = $LocPos193_WekenErvaring . ' week';
		} else {
		$EXP_193 = $LocPos193_WekenErvaring . ' weken';
		}
		$PVB_193 = $LocPos193_Competenties;
	}
	//203
	if ($LocPos203_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos203_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos203_RelatieNr.'.jpg')) {
		$FOTO_203 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos203_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_203 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_203 = $LocPos203_VolledigeNaam;
		$AGE_203 = $LocPos203_Leeftijd . ' jaar';
		if ($LocPos203_WekenErvaring < '1') {
		$EXP_203 = 'Geen';
		} elseif ($LocPos203_WekenErvaring == '1') {
		$EXP_203 = $LocPos203_WekenErvaring . ' week';
		} else {
		$EXP_203 = $LocPos203_WekenErvaring . ' weken';
		}
		$PVB_203 = $LocPos203_Competenties;
	}
	//212
	if ($LocPos212_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos212_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos212_RelatieNr.'.jpg')) {
		$FOTO_212 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos212_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_212 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_212 = $LocPos212_VolledigeNaam;
		$AGE_212 = $LocPos212_Leeftijd . ' jaar';
		if ($LocPos212_WekenErvaring < '1') {
		$EXP_212 = 'Geen';
		} elseif ($LocPos212_WekenErvaring == '1') {
		$EXP_212 = $LocPos212_WekenErvaring . ' week';
		} else {
		$EXP_212 = $LocPos212_WekenErvaring . ' weken';
		}
		$PVB_212 = $LocPos212_Competenties;
	}
	//282
	if ($LocPos282_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos282_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos282_RelatieNr.'.jpg')) {
		$FOTO_282 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos282_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_282 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_282 = $LocPos282_VolledigeNaam;
		$AGE_282 = $LocPos282_Leeftijd . ' jaar';
		if ($LocPos282_WekenErvaring < '1') {
		$EXP_282 = 'Geen';
		} elseif ($LocPos282_WekenErvaring == '1') {
		$EXP_282 = $LocPos282_WekenErvaring . ' week';
		} else {
		$EXP_282 = $LocPos282_WekenErvaring . ' weken';
		}
		$PVB_282 = $LocPos282_Competenties;
	}
	//194
	if ($LocPos194_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos194_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos194_RelatieNr.'.jpg')) {
		$FOTO_194 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos194_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_194 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_194 = $LocPos194_VolledigeNaam;
		$AGE_194 = $LocPos194_Leeftijd . ' jaar';
		if ($LocPos194_WekenErvaring < '1') {
		$EXP_194 = 'Geen';
		} elseif ($LocPos194_WekenErvaring == '1') {
		$EXP_194 = $LocPos194_WekenErvaring . ' week';
		} else {
		$EXP_194 = $LocPos194_WekenErvaring . ' weken';
		}
		$PVB_194 = $LocPos194_Competenties;
	}
	//204
	if ($LocPos204_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos204_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos204_RelatieNr.'.jpg')) {
		$FOTO_204 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos204_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_204 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_204 = $LocPos204_VolledigeNaam;
		$AGE_204 = $LocPos204_Leeftijd . ' jaar';
		if ($LocPos204_WekenErvaring < '1') {
		$EXP_204 = 'Geen';
		} elseif ($LocPos204_WekenErvaring == '1') {
		$EXP_204 = $LocPos204_WekenErvaring . ' week';
		} else {
		$EXP_204 = $LocPos204_WekenErvaring . ' weken';
		}
		$PVB_204 = $LocPos204_Competenties;
	}
	//213
	if ($LocPos213_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos213_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos213_RelatieNr.'.jpg')) {
		$FOTO_213 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos213_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_213 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_213 = $LocPos213_VolledigeNaam;
		$AGE_213 = $LocPos213_Leeftijd . ' jaar';
		if ($LocPos213_WekenErvaring < '1') {
		$EXP_213 = 'Geen';
		} elseif ($LocPos213_WekenErvaring == '1') {
		$EXP_213 = $LocPos213_WekenErvaring . ' week';
		} else {
		$EXP_213 = $LocPos213_WekenErvaring . ' weken';
		}
		$PVB_213 = $LocPos213_Competenties;
	}
	//283
	if ($LocPos283_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos283_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos283_RelatieNr.'.jpg')) {
		$FOTO_283 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos283_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_283 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_283 = $LocPos283_VolledigeNaam;
		$AGE_283 = $LocPos283_Leeftijd . ' jaar';
		if ($LocPos283_WekenErvaring < '1') {
		$EXP_283 = 'Geen';
		} elseif ($LocPos283_WekenErvaring == '1') {
		$EXP_283 = $LocPos283_WekenErvaring . ' week';
		} else {
		$EXP_283 = $LocPos283_WekenErvaring . ' weken';
		}
		$PVB_283 = $LocPos283_Competenties;
	}
	//195
	if ($LocPos195_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos195_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos195_RelatieNr.'.jpg')) {
		$FOTO_195 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos195_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_195 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_195 = $LocPos195_VolledigeNaam;
		$AGE_195 = $LocPos195_Leeftijd . ' jaar';
		if ($LocPos195_WekenErvaring < '1') {
		$EXP_195 = 'Geen';
		} elseif ($LocPos195_WekenErvaring == '1') {
		$EXP_195 = $LocPos195_WekenErvaring . ' week';
		} else {
		$EXP_195 = $LocPos195_WekenErvaring . ' weken';
		}
		$PVB_195 = $LocPos195_Competenties;
	}
	//205
	if ($LocPos205_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos205_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos205_RelatieNr.'.jpg')) {
		$FOTO_205 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos205_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_205 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_205 = $LocPos205_VolledigeNaam;
		$AGE_205 = $LocPos205_Leeftijd . ' jaar';
		if ($LocPos205_WekenErvaring < '1') {
		$EXP_205 = 'Geen';
		} elseif ($LocPos205_WekenErvaring == '1') {
		$EXP_205 = $LocPos205_WekenErvaring . ' week';
		} else {
		$EXP_205 = $LocPos205_WekenErvaring . ' weken';
		}
		$PVB_205 = $LocPos205_Competenties;
	}
	//214
	if ($LocPos214_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos214_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos214_RelatieNr.'.jpg')) {
		$FOTO_214 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos214_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_214 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_214 = $LocPos214_VolledigeNaam;
		$AGE_214 = $LocPos214_Leeftijd . ' jaar';
		if ($LocPos214_WekenErvaring < '1') {
		$EXP_214 = 'Geen';
		} elseif ($LocPos214_WekenErvaring == '1') {
		$EXP_214 = $LocPos214_WekenErvaring . ' week';
		} else {
		$EXP_214 = $LocPos214_WekenErvaring . ' weken';
		}
		$PVB_214 = $LocPos214_Competenties;
	}
	//284
	if ($LocPos284_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos284_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos284_RelatieNr.'.jpg')) {
		$FOTO_284 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos284_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_284 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_284 = $LocPos284_VolledigeNaam;
		$AGE_284 = $LocPos284_Leeftijd . ' jaar';
		if ($LocPos284_WekenErvaring < '1') {
		$EXP_284 = 'Geen';
		} elseif ($LocPos284_WekenErvaring == '1') {
		$EXP_284 = $LocPos284_WekenErvaring . ' week';
		} else {
		$EXP_284 = $LocPos284_WekenErvaring . ' weken';
		}
		$PVB_284 = $LocPos284_Competenties;
	}
	//196
	if ($LocPos196_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos196_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos196_RelatieNr.'.jpg')) {
		$FOTO_196 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos196_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_196 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_196 = $LocPos196_VolledigeNaam;
		$AGE_196 = $LocPos196_Leeftijd . ' jaar';
		if ($LocPos196_WekenErvaring < '1') {
		$EXP_196 = 'Geen';
		} elseif ($LocPos196_WekenErvaring == '1') {
		$EXP_196 = $LocPos196_WekenErvaring . ' week';
		} else {
		$EXP_196 = $LocPos196_WekenErvaring . ' weken';
		}
		$PVB_196 = $LocPos196_Competenties;
	}
	//206
	if ($LocPos206_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos206_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos206_RelatieNr.'.jpg')) {
		$FOTO_206 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos206_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_206 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_206 = $LocPos206_VolledigeNaam;
		$AGE_206 = $LocPos206_Leeftijd . ' jaar';
		if ($LocPos206_WekenErvaring < '1') {
		$EXP_206 = 'Geen';
		} elseif ($LocPos206_WekenErvaring == '1') {
		$EXP_206 = $LocPos206_WekenErvaring . ' week';
		} else {
		$EXP_206 = $LocPos206_WekenErvaring . ' weken';
		}
		$PVB_206 = $LocPos206_Competenties;
	}
	//215
	if ($LocPos215_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos215_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos215_RelatieNr.'.jpg')) {
		$FOTO_215 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos215_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_215 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_215 = $LocPos215_VolledigeNaam;
		$AGE_215 = $LocPos215_Leeftijd . ' jaar';
		if ($LocPos215_WekenErvaring < '1') {
		$EXP_215 = 'Geen';
		} elseif ($LocPos215_WekenErvaring == '1') {
		$EXP_215 = $LocPos215_WekenErvaring . ' week';
		} else {
		$EXP_215 = $LocPos215_WekenErvaring . ' weken';
		}
		$PVB_215 = $LocPos215_Competenties;
	}
	//285
	if ($LocPos285_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos285_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos285_RelatieNr.'.jpg')) {
		$FOTO_285 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos285_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_285 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_285 = $LocPos285_VolledigeNaam;
		$AGE_285 = $LocPos285_Leeftijd . ' jaar';
		if ($LocPos285_WekenErvaring < '1') {
		$EXP_285 = 'Geen';
		} elseif ($LocPos285_WekenErvaring == '1') {
		$EXP_285 = $LocPos285_WekenErvaring . ' week';
		} else {
		$EXP_285 = $LocPos285_WekenErvaring . ' weken';
		}
		$PVB_285 = $LocPos285_Competenties;
	}
	//216
	if ($LocPos216_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos216_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos216_RelatieNr.'.jpg')) {
		$FOTO_216 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos216_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_216 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_216 = $LocPos216_VolledigeNaam;
		$AGE_216 = $LocPos216_Leeftijd . ' jaar';
		if ($LocPos216_WekenErvaring < '1') {
		$EXP_216 = 'Geen';
		} elseif ($LocPos216_WekenErvaring == '1') {
		$EXP_216 = $LocPos216_WekenErvaring . ' week';
		} else {
		$EXP_216 = $LocPos216_WekenErvaring . ' weken';
		}
		$PVB_216 = $LocPos216_Competenties;
	}
	//286
	if ($LocPos286_RelatieNr != '') {	
		if (file_exists('./pasfotos/'.$LocPos286_RelatieNr.'.jpg') || file_exists('././clubredders/pasfotos/'.$LocPos286_RelatieNr.'.jpg')) {
		$FOTO_286 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . $LocPos286_RelatieNr . '.jpg')).$FOTO_STYLE;
		} else {
		$FOTO_286 = $FOTO_CODERING.base64_encode(file_get_contents($FOTO_FOLDER . 'pasfoto.jpg')).$FOTO_STYLE;
		}
		$NAAM_286 = $LocPos286_VolledigeNaam;
		$AGE_286 = $LocPos286_Leeftijd . ' jaar';
		if ($LocPos286_WekenErvaring < '1') {
		$EXP_286 = 'Geen';
		} elseif ($LocPos286_WekenErvaring == '1') {
		$EXP_286 = $LocPos286_WekenErvaring . ' week';
		} else {
		$EXP_286 = $LocPos286_WekenErvaring . ' weken';
		}
		$PVB_286 = $LocPos286_Competenties;
	}
	
	//Bereken de gemiddelde ervaring van de posten. LET OP Deze waarden moeten nog gemiddeld worden waarbij lege posities niet meegewogen worden maar lege waarden voor bewakers met geen ervaring wel.
	$EXP_ASCOPL = $EXP_ASC + $EXP_OPL;
	$EXP_Post19 = $EXP_PC19 + $EXP_191 + $EXP_192 + $EXP_193 + $EXP_194 + $EXP_195 + $EXP_196;
	$EXP_Post20 = $EXP_PC20 + $EXP_201 + $EXP_202 + $EXP_203 + $EXP_204 + $EXP_205 + $EXP_206;
	$EXP_Post21 = $EXP_PC21 + $EXP_CVDS + $EXP_211 + $EXP_212 + $EXP_213 + $EXP_214 + $EXP_215 + $EXP_216;
	$EXP_Post28 = $EXP_PC28 + $EXP_CVD + $EXP_281 + $EXP_282 + $EXP_283 + $EXP_284 + $EXP_285 + $EXP_286;

?>
<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif;font-size:15px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:transparent}
.tg th{font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif;font-size:15px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:transparent;font-weight:normal}
.tg .tg-grijzebalk-textcenter{border-color:transparent;text-align:center;vertical-align:top;font-weight:bold;background-color:#d9d9d9}
.tg .tg-grijzebalk-textleft{border-color:transparent;text-align:left;vertical-align:top;font-weight:bold;background-color:#d9d9d9}
.tg .tg-grijzebalk-textright{border-color:transparent;text-align:right;vertical-align:top;font-weight:bold;background-color:#d9d9d9}
.tg .tg-logobalk{border-color:transparent;text-align:center;vertical-align:top}
.tg .tg-foto{border-color:transparent;text-align:left;vertical-align:top;background-color:transparent<!--#f2f2f2-->;width:79px;height:90px}
.tg .tg-naam{border-color:transparent;text-align:center;vertical-align:top;font-weight:bold}
.tg .tg-leeftijd{border-color:transparent;text-align:left;vertical-align:top}
.tg .tg-ervaring{border-color:transparent;text-align:right;vertical-align:top}
.tg .tg-competenties{border-color:transparent;text-align:left;vertical-align:top;font-size:11px}
.tg .tg-spacing{border-color:transparent;text-align:left;vertical-align:top}
</style>
<table class="tg">
  <tr>
    <th class="tg-grijzebalk-textleft">ASC</th>
    <th class="tg-grijzebalk-textright" colspan="2">Gem. erv.: <?php echo $LocPosASC_PostErvaring; ?> weken</th>
    <th class="tg-spacing" rowspan="34"></th>
    <th class="tg-grijzebalk-textcenter" colspan="7"><?php echo 'Postindeling '.substr($_POST["week"],-6, 4).'-'.substr($_POST["week"],-2).' Texel'; ?></th>
    <th class="tg-spacing" rowspan="34"></th>
    <th class="tg-grijzebalk-textleft">OPL</th>
    <th class="tg-grijzebalk-textright" colspan="2">Gem. erv.: <?php echo $LocPosOPL_PostErvaring; ?> weken</th>
  </tr>
  <tr>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_ASC; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_ASC; ?></td>
    <td class="tg-logobalk" colspan="7" rowspan="4"><!--<img src="../../../images/logo-custom.svg" alt="TRB Logo">--></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_OPL; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_OPL; ?></td>
  </tr>
  <tr>
    <td class="tg-leeftijd"><?php echo $AGE_ASC; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_ASC; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_OPL; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_OPL; ?></td>
  </tr>
  <tr>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_ASC; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_OPL; ?></td>
  </tr>
  <tr>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
  </tr>
  <tr>
    <td class="tg-grijzebalk-textleft">Post 19</td>
    <td class="tg-grijzebalk-textright" colspan="2">Gem. erv.: <?php echo $LocPosPC19_PostErvaring; ?> weken</td>
    <td class="tg-grijzebalk-textleft">Post 20</td>
    <td class="tg-grijzebalk-textright" colspan="2">Gem. erv.: <?php echo $LocPosPC20_PostErvaring; ?> weken</td>
    <td class="tg-spacing" rowspan="29"></td>
    <td class="tg-grijzebalk-textleft">Post 21</td>
    <td class="tg-grijzebalk-textright" colspan="2">Gem. erv.: <?php echo $LocPosPC21_PostErvaring; ?> weken</td>
    <td class="tg-grijzebalk-textleft">Post 28</td>
    <td class="tg-grijzebalk-textright" colspan="2">Gem. erv.: <?php echo $LocPosPC28_PostErvaring; ?> weken</td>
  </tr>
  <tr>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_PC19; ?></td>
    <td class="tg-naam" colspan="2">PC19: <?php echo $NAAM_PC19; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_PC20; ?></td>
    <td class="tg-naam" colspan="2">PC20: <?php echo $NAAM_PC20; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_PC21; ?></td>
    <td class="tg-naam" colspan="2">PC21: <?php echo $NAAM_PC21; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_PC28; ?></td>
    <td class="tg-naam" colspan="2">PC28: <?php echo $NAAM_PC28; ?></td>
  </tr>
  <tr>
    <td class="tg-leeftijd"><?php echo $AGE_PC19; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_PC19; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_PC20; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_PC20; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_PC21; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_PC21; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_PC28; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_PC28; ?></td>
  </tr>
  <tr>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_PC19; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_PC20; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_PC21; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_PC28; ?></td>
  </tr>
  <tr>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
  </tr>
  <tr>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_191; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_191; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_201; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_201; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_CVDS; ?></td>
    <td class="tg-naam" colspan="2">CVDS: <?php echo $NAAM_CVDS; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_CVD; ?></td>
    <td class="tg-naam" colspan="2">CVD: <?php echo $NAAM_CVD; ?></td>
  </tr>
  <tr>
    <td class="tg-leeftijd"><?php echo $AGE_191; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_191; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_201; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_201; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_CVDS; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_CVDS; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_CVD; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_CVD; ?></td>
  </tr>
  <tr>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_191; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_201; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_CVDS; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_CVD; ?></td>
  </tr>
  <tr>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
  </tr>
  <tr>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_192; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_192; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_202; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_202; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_211; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_211; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_281; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_281; ?></td>
  </tr>
  <tr>
    <td class="tg-leeftijd"><?php echo $AGE_192; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_192; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_202; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_202; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_211; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_211; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_281; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_281; ?></td>
  </tr>
  <tr>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_192; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_202; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_211; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_281; ?></td>
  </tr>
  <tr>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
  </tr>
  <tr>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_193; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_193; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_203; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_203; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_212; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_212; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_282; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_282; ?></td>
  </tr>
  <tr>
    <td class="tg-leeftijd"><?php echo $AGE_193; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_193; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_203; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_203; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_212; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_212; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_282; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_282; ?></td>
  </tr>
  <tr>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_193; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_203; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_212; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_282; ?></td>
  </tr>
  <tr>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
  </tr>
  <tr>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_194; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_194; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_204; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_204; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_213; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_213; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_283; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_283; ?></td>
  </tr>
  <tr>
    <td class="tg-leeftijd"><?php echo $AGE_194; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_194; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_204; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_204; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_213; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_213; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_283; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_283; ?></td>
  </tr>
  <tr>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_194; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_204; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_213; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_283; ?></td>
  </tr>
  <tr>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
  </tr>
  <tr>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_195; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_195; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_205; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_205; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_214; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_214; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_284; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_284; ?></td>
  </tr>
  <tr>
    <td class="tg-leeftijd"><?php echo $AGE_195; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_195; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_205; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_205; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_214; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_214; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_284; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_284; ?></td>
  </tr>
  <tr>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_195; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_205; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_214; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_284; ?></td>
  </tr>
  <tr>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
  </tr>
  <tr>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_196; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_196; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_206; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_206; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_215; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_215; ?></td>
    <td class="tg-foto" rowspan="3"><?php echo $FOTO_285; ?></td>
    <td class="tg-naam" colspan="2"><?php echo $NAAM_285; ?></td>
  </tr>
  <tr>
    <td class="tg-leeftijd"><?php echo $AGE_196; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_196; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_206; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_206; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_215; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_215; ?></td>
    <td class="tg-leeftijd"><?php echo $AGE_285; ?></td>
    <td class="tg-ervaring"><?php echo $EXP_285; ?></td>
  </tr>
  <tr>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_196; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_206; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_215; ?></td>
    <td class="tg-competenties" colspan="2" rowspan="2"><?php echo $PVB_285; ?></td>
  </tr>
  <tr>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
    <td class="tg-spacing"></td>
  </tr>
</table>
<!--Materialize design-->
<!--echo '</main>';-->
<!--echo '</div>';-->
<!--<?php //include '../../../footer.php'; ?>