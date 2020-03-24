<?php	// Include database class	
require_once 'util/utility.class.php';	
require_once 'util/database.class.php';	
if(!isset($user)){
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/
	login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();
}	
if(getUserName()){
		$bondsDiplomasTableQuery =  'SELECT * FROM `cr_diplomas` where `Relatienr` = \''.getUserName().'\' and `Type` = \'Bondsdiploma\' order by `Ingangsdatum` desc';
		$database = new Database();
		$database->query($bondsDiplomasTableQuery);
		$bondsDiplomasTableResult = $database->resultset();
		
		$bondsDiplomasTable = '<table class="mat-resp-striped-table">';
		$bondsDiplomasTable .= '<tr>';
		$bondsDiplomasTable .= '<th>Naam Diploma</th>';
		$bondsDiplomasTable .= '<th>Ingangsdatum</th>';
		$bondsDiplomasTable .= '<th>Vervaldatum</th>';
		$bondsDiplomasTable .= '<th>Bijscholing</th>';
		$bondsDiplomasTable .= '<th>Opmerkingen</th>';
		$bondsDiplomasTable .= '</tr>';
		if(count($bondsDiplomasTableResult) === 0){	
			$bondsDiplomasTable .= '<tr>';	
			$bondsDiplomasTable .= '<td>Je hebt (nog) geen bondsdiploma\'s. </td><td></td><td></td><td></td><td></td>';
			$bondsDiplomasTable .= '</tr>';	
		}else{
			foreach ($bondsDiplomasTableResult as $key => $value) {	
				if($value["IngangsDatum"] === '0000-00-00'){ $IngangsDatum = ''; }else{ $IngangsDatum = $value["IngangsDatum"]; }
				if($value["EindDatum"] === '0000-00-00'){ $EindDatum = ''; }else{ $EindDatum = $value["EindDatum"]; }
				if($value["Bijscholing"] === '0000-00-00'){ $Bijscholing = ''; }else{ $Bijscholing = $value["Bijscholing"]; }
				$bondsDiplomasTable .= '<tr>';
				$bondsDiplomasTable .= '<td>'.$value["Soort"] . '</td>';
				$bondsDiplomasTable .= '<td>'.$IngangsDatum. '</td>';
				$bondsDiplomasTable .= '<td>'.$EindDatum. '</td>';
				$bondsDiplomasTable .= '<td>'.$Bijscholing. '</td>';
				$bondsDiplomasTable .= '<td>'.$value["Opmerkingen"] . '</td>';
				$bondsDiplomasTable .= '</tr>';	
			}
		}
		$bondsDiplomasTable .= "</table>";
}
?>