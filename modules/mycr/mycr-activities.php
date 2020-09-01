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
		if (LaatRoosterZien) {
			$tableQuery = 'SELECT * FROM `cr_activiteiten` where `Relatienr` = \'' .$userName. '\' and `Activiteit` not like \'%NI\' order by `Activiteit` desc';
		} else {
			$tableQuery = 'SELECT * FROM `cr_activiteiten` where `Relatienr` = \'' .$userName. '\' and `Activiteit` not like \'BWK' . Date('Y') . '%\' and `Activiteit` not like \'%NI\' order by `Activiteit` desc';
		}
		$database = new Database();
		$database->query($tableQuery);
		$tableResult       = $database->resultset();
		$activiteitenTable = '<table class="mat-resp-striped-table">';
		$activiteitenTable .= '<tr>';
		$activiteitenTable .= '<th>Activiteit</th>';
		$activiteitenTable .= '<th>Vanaf Datum</th>';
		$activiteitenTable .= '<th>Tot Datum</th>';
		$activiteitenTable .= '<th>Locatie</th>';
		$activiteitenTable .= '<th>Opmerkingen</th>';
		$activiteitenTable .= '</tr>';
		if (count($tableResult) === 0) {
			$activiteitenTable .= '<tr>';
			$activiteitenTable .= '<td>Je hebt (nog) geen activiteiten. </td><td></td><td></td><td></td><td></td><td></td>';
			$activiteitenTable .= '</tr>';
		} else {
			foreach ($tableResult as $key => $value) {
				$activiteitenTable .= '<tr>';
				$activiteitenTable .= '<td>' . $value["Activiteit"] . '</td>';
				$activiteitenTable .= '<td>' . $value["VanafDatum"] . '</td>';
				$activiteitenTable .= '<td>' . $value["TotDatum"] . '</td>';
				//Locatie niet weergeven als de datum nog niet geweest is.
				$VanafDatum = $value["VanafDatum"];
				$Vandaag = date('j-M-y',time());
				$DatumVanaf = strtotime($VanafDatum);
				$DatumVandaag = strtotime($Vandaag);
				if ($DatumVanaf < $DatumVandaag) {
					$activiteitenTable .= '<td>' . $value["Locatie"] . '</td>';
				} else {
					$activiteitenTable .= '<td>' . '' . '</td>';
				}
					$activiteitenTable .= '<td>' . $value["Opmerkingen"] . '</td>';
					$activiteitenTable .= '</tr>';
				}
		}
				$activiteitenTable .= "</table>";
	}
?>