<?php
	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/
	login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();
	
	
	// Include database class
	include 'util/utility.class.php';	
	include 'util/database.class.php';

	$week = '';
	
	if(isset($_POST["location"])){
	
		$updateArray = $_POST["location"];
		foreach($updateArray as $row){
			$array = explode(";", $row);
			$queries[] = "UPDATE `cr_activiteiten` SET `Locatie`='$array[2]'  WHERE `Relatienr` = '$array[1]' and `Activiteit` = '$array[0]'";
			$week = $array[0];
		}
		
		try{
			$database = new Database();
			$database->beginTransaction();
			
			foreach($queries as $query){
				$database->query($query);	
				$database->execute();
			}
			$database->endTransaction();
						
			$tableQuery =  'SELECT a.*, b.`VolledigeNaam`';
			$tableQuery .= ' FROM `cr_activiteiten` as a';
			$tableQuery .= ' left outer join `cr_leden` as b on a.`Relatienr` = b.`Relatienr`';
			$tableQuery .= ' where a.`Activiteit` = \''.$week.'\' ORDER BY a.`Locatie`';
				
			$database->query($tableQuery);	
			$tableResult = $database->resultset();
			
			echo '<table class="striped">';
			echo '<tr><td>Tabel succesvol geupdatet!</td></tr>';
			echo '<tr><th>Activiteit</th><th>Relatienummer</th><th>Volledige Naam</th><th>Locatie</th><th>Opmerkingen</th></tr>';
			foreach ($tableResult as $key => $value) {
				echo '<tr>';
				echo '<td>'.$value["Activiteit"] . '</td>';
				echo '<td>'.$value["Relatienr"] . '</td>';
				echo '<td>'.$value["VolledigeNaam"] . '</td>';
				echo '<td>'.$value["Locatie"] . '</td>';
				echo '<td>'.$value["Opmerkingen"] . '</td>';
				echo '</tr>';
			}
			echo '</table><br><a href="roosteren.php">Klik hier om meer aan te passen</a><br><a href="index.php">Klik hier om terug te gaan naar het menu</a>';			
			
		}catch(PDOException $e){
			$database->cancelTransaction();
			echo $e->getMessage();
		}
	}
?>
