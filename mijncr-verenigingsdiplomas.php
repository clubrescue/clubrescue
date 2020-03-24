<?php
	require_once 'util/utility.class.php';
	require_once 'util/database.class.php';
	
if (!isset($user)) {
    header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
    require_once("wp-authenticate.php");
    /*** REQUIRE USER AUTHENTICATION ***/
    login();
    /*** RETRIEVE LOGGED IN USER INFORMATION ***/
    $user = wp_get_current_user();
}
	
if (getUserName()) {
    $verenigingsDiplomasTableQuery = 'SELECT * FROM `cr_diplomas` where `Relatienr` = \'' . getUserName() . '\' and `Type` = \'Verenigingsdiploma\' order by `Ingangsdatum` desc';
    $database = new Database();
    $database->query($verenigingsDiplomasTableQuery);
    $verenigingsDiplomasTableResult = $database->resultset();
    $verenigingsDiplomasTable  = '<table class="mat-resp-striped-table">'; 
	$verenigingsDiplomasTable .= '<tr>';        
	$verenigingsDiplomasTable .= '<th>Naam Diploma</th>';
	$verenigingsDiplomasTable .= '<th>Diploma Soort</th>'; 
	$verenigingsDiplomasTable .= '<th>Ingangsdatum</th>';
	$verenigingsDiplomasTable .= '<th>Vervaldatum</th>';
	$verenigingsDiplomasTable .= '<th>Bijscholing</th>'; 
	$verenigingsDiplomasTable .= '<th>Opmerkingen</th>';
	$verenigingsDiplomasTable .= '</tr>';
    if (count($verenigingsDiplomasTable) === 0) {
        $verenigingsDiplomasTable .= '<tr>';
        $verenigingsDiplomasTable .= '<td>Je hebt (nog) geen verenigingsdiploma\'s . </td><td></td><td></td><td></td><td></td><td></td>';
        $verenigingsDiplomasTable .= '</tr>';
    } else {
        foreach ($verenigingsDiplomasTableResult as $key => $value) {
            if ($value["IngangsDatum"] === '0000-00-00') {
                $IngangsDatum = '';
            } else {
                $IngangsDatum = $value["IngangsDatum"];
            }
            if ($value["EindDatum"] === '0000-00-00') {
                $EindDatum = '';
            } else {
                $EindDatum = $value["EindDatum"];
            }
            if ($value["Bijscholing"] === '0000-00-00') {
                $Bijscholing = '';
            } else {
                $Bijscholing = $value["Bijscholing"];
            }
            $verenigingsDiplomasTable .= '<tr>';
            $verenigingsDiplomasTable .= '<td>' . $value["Diploma"] . '</td>';
            $verenigingsDiplomasTable .= '<td>' . $value["Soort"] . '</td>';
            $verenigingsDiplomasTable .= '<td>' . $IngangsDatum . '</td>';
            $verenigingsDiplomasTable .= '<td>' . $EindDatum . '</td>';
            $verenigingsDiplomasTable .= '<td>' . $Bijscholing . '</td>';
            $verenigingsDiplomasTable .= '<td>' . $value["Opmerkingen"] . '</td>';
            $verenigingsDiplomasTable .= '</tr>';
        }
    }
	
    $verenigingsDiplomasTable .= '</table>';
}
?>