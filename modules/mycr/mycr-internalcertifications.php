<?php
    session_start();

    if (!isset($_SESSION['token'])) {
        $_SESSION['O365_REDIRECT'] = $_SERVER['REQUEST_URI'];
        include $_SERVER['DOCUMENT_ROOT'] . '/clubredders/auth.php';
    }
    
    require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/msgraph/user.class.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/utility.class.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/database.class.php';
    
    $user = new MSGraphUser($_SESSION['token']);
	//retrieve current user name
	$userNameRequest = $user->getUser();
	$userNameJSON = json_encode($userNameRequest);
	$userNameValue = json_decode($userNameJSON, true);
	$userName = strtok($userNameValue['userPrincipalName'], '@');

    //$welcome = 'MyCR Expenses';
    //include './views/index.view.php';

	if(isset($user)){
		
		// LET OP! De gebruikersnaam in de SQL strings waren functies .getUser() en is nu een variabele. Dat moet nog aangepast worden.
		$verenigingsDiplomasTableQuery = 'SELECT * FROM `cr_diplomas` where `Relatienr` = \'' .$userName. '\' and `Type` = \'Verenigingsdiploma\' order by `Ingangsdatum` desc';
		$database = new Database();
		$database->query($verenigingsDiplomasTableQuery);
		$verenigingsDiplomasTableResult = $database->resultset();
		$verenigingsDiplomasTable  = '<p>Verlenging verklaring Eerste Hulp (EHBO diploma/Levensreddende Handelingen) doorgeven? Geef je verlenging door via het tabblad mijn acties.<br>Heb je nog geen Levensreddende Handelingen? Geef je Verklaring Eerste Hulp dan eerst door via het tabblad bondsdiploma’s.</p>'; 
		$verenigingsDiplomasTable .= '<table class="mat-resp-striped-table">'; 
		$verenigingsDiplomasTable .= '<tr>';        
		$verenigingsDiplomasTable .= '<th>Naam Diploma</th>';
		$verenigingsDiplomasTable .= '<th>Diploma Soort</th>'; 
		$verenigingsDiplomasTable .= '<th>Ingangsdatum</th>';
		$verenigingsDiplomasTable .= '<th>Vervaldatum</th>';
		$verenigingsDiplomasTable .= '<th>Bijscholing</th>'; 
		$verenigingsDiplomasTable .= '<th>Opmerkingen</th>';
		$verenigingsDiplomasTable .= '</tr>';
		if (count($verenigingsDiplomasTableResult) === 0) {
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