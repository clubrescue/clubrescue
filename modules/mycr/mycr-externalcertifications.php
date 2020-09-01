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
		$bondsDiplomasTableQuery =  'SELECT * FROM `cr_diplomas` where `Relatienr` = \''.$userName.'\' and `Type` = \'Bondsdiploma\' order by `Ingangsdatum` desc';
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