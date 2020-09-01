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
		$bondsFunctiesTableQuery =  'SELECT * FROM `cr_bondsfuncties` where `Relatienr` = \''.$userName.'\' order by `Functie`';				$database = new Database();		$database->query($bondsFunctiesTableQuery);		$bondsFunctiesTableResult = $database->resultset();
		$bondsFunctiesTable = '<table class="mat-resp-striped-table">';		$bondsFunctiesTable .= '<tr>';		$bondsFunctiesTable .= '<th>Bondsfunctie</th>';		$bondsFunctiesTable .= '</tr>';
		if(count($bondsFunctiesTableResult) === 0){			$bondsFunctiesTable .= '<tr>';			$bondsFunctiesTable .= '<td>Je hebt (nog) geen bondsfuncties. </td>';			$bondsFunctiesTable .= '</tr>';		}else{			foreach ($bondsFunctiesTableResult as $key => $value) {				$bondsFunctiesTable .= '<tr>';				$bondsFunctiesTable .= '<td>'.$value["Functie"] . '</td>';				$bondsFunctiesTable .= '</tr>';			}		}		$bondsFunctiesTable .= "</table>";	
	}
?>