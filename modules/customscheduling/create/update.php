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
	
	$where1 = 'BWK'.Date("Y").'%';
	$where2 = 'EXAMEN'.Date("Y").'%';
	
	$locations = ['','19','20','21','28','PC19','PC20','PC21','PC28','ASC','OPL','CVD','CVDS'];
	
	if( current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') || current_user_can('contributor')) {	
		$query = "SELECT distinct `Activiteit` FROM  `cr_activiteiten` WHERE `Activiteit` LIKE '$where1' or `Activiteit` LIKE '$where2' ORDER BY `ACTIVITEIT` ASC";
	}else{
		$query = "SELECT distinct `Activiteit` FROM  `cr_activiteiten` WHERE  `Relatienr` =  '$user->user_login' AND  `Locatie` IN ('OPL',  'ASC') AND (`Activiteit` LIKE '$where1' or `Activiteit` LIKE '$where2')";
	}
	$database = new Database();
	$database->query($query);	
	$results = $database->resultset();
	
	// Materialize design
	//include __DIR__ . '/../../../header.php';
	//echo '<main>'
	
	if($database->RowCount() > 0){

		echo '<form action="" method="POST"><select class="form-control" name="week">';
		foreach ($results as $key => $value) {
			echo '<option value="'.$value["Activiteit"].'">'.$value["Activiteit"].'</option>';	
		}
		echo '</select>
				<button class="btn waves-effect waves-light" type="submit" name="action">Open de geselecteerde week
					<i class="material-icons right">send</i>
				</button>
			  </form>';
		
		if(isset($_POST["week"])) {
		
			$tableQuery =  'SELECT a.*, b.`VolledigeNaam`';
			$tableQuery .= ' FROM `cr_activiteiten` as a';
			$tableQuery .= ' left outer join `cr_leden` as b on a.`Relatienr` = b.`Relatienr`';
			$tableQuery .= ' where a.`Activiteit` = \''.$_POST["week"].'\' ORDER BY a.`Locatie`';
				
			$database->query($tableQuery);	
			$tableResult = $database->resultset();
			
			echo '<form action="roosterupdate.php" method="post" name="location">';
			echo '<table class="striped">';
			echo '<tr><th>Activiteit</th><th>Relatienummer</th><th>Volledige Naam</th><th>Locatie</th><th>Opmerkingen</th></tr>';
			foreach ($tableResult as $key => $value) {
				echo '<tr>';
				echo '<td>'.$value["Activiteit"] . '</td>';
				echo '<td>'.$value["Relatienr"] . '</td>';
				echo '<td>'.$value["VolledigeNaam"] . '</td>';
				echo '<td><select name="location[]"';
				if(strpos($value["Activiteit"], 'EXAMEN') !== false){
					echo 'disabled>';
				}else{
					echo '>';
				}
				foreach ($locations as $location) {
					if($location === $value["Locatie"]){
						echo '<option value="'.$value["Activiteit"].';'.$value["Relatienr"].';'.$location.'" selected>'.$location.'</option>';							
					}else{
						echo '<option value="'.$value["Activiteit"].';'.$value["Relatienr"].';'.$location.'">'.$location.'</option>';	
					}
				}
				echo '</select></td>';
				// Default value uit de database weergeven
				echo '<td>'.$value["Opmerkingen"] . '</td>';
				echo '</tr>';
			}
			echo '</table>
					<button class="btn waves-effect waves-light" type="submit" name="action">Update de weekindeling
						<i class="material-icons right">sync</i>
					</button>
				  </form>';

		}
	}else{
		// Geen weken om aan te passen
		echo 'Je hebt helaas geen weken om aan te passen!';
	}
	
	// Materialize design
	//echo '</main>'
	//include '../../../footer.php';
?>