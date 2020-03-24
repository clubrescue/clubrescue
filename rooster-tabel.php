<?php

	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/
	login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();
	
	
	// Include database class
	include 'util/utility.class.php';	
	include 'util/database.class.php';	

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
	//include 'header.php';
	echo '</br></br>';
	echo '<main>';
	echo '<div class="container">';

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
									leden.`Email`,
									leden.`Dieet`,
									act.`Opmerkingen`,
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

			echo '<form action="rooster-tabel.php" method="post" name="location">';
			echo '<table class="striped">';
			echo '<tr><th>Relatienummer</th><th>Volledige Naam</th><th>Leeftijd</th><th>Ervaring</th><th>PvBs - Niveau/functies</th><th>PvBs - Diploma\'s</th><th>IsPC?</th><th>IsPCHP?</th><th>E-mail</th><th>Opmerkingen</th><th>Dieet</th><th>Examen</th></tr>';
			foreach ($tableResult as $key => $value) {
				echo '<tr>';
				echo '<td>'.$value["RelatieNr"] . '</td>';
				echo '<td>'.$value["VolledigeNaam"] . '</td>';
				echo '<td>'.$value["Leeftijd"] . '</td>';
				echo '<td>'.$value["WekenErvaring"] . '</td>';
				echo '<td>'.$value["CompetentiesRegel1"] . '</td>';
				echo '<td>'.$value["CompetentiesRegel2"] . '</td>';
				echo '<td>Is PC?</td>';
				echo '<td>Is PCHP?</td>';
				echo '<td>'.$value["Email"] . '</td>';
				echo '<td>'.$value["Opmerkingen"] . '</td>';
				echo '<td>'.$value["Dieet"] . '</td>';
				echo '<td>Doet examen?</td>';
				echo '</tr>';
			}
			echo '</table>';
		}
	}else{
		// Geen roosters om in te laden.
		echo 'Je hebt helaas geen roosters om in te laden!';
	}
	
	echo '</div>';
	echo '</main>';
?>