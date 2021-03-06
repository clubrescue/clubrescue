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
		$sqlInkopen = 'SELECT `Administratie nr.`, `Entry #`, `Date`, `IBAN`, `Omschrijving-1`, `Doel-1`, `Post-1`, `Bedrag-1`, `Bonnen toevoegen`, `Status` FROM `cr_declaraties` WHERE `Actie` = \'Declaratie-inkopen\' and `Relatiecode` = \''.$userName.'\' order by `Administratie nr.` desc';
		$sqlReiskosten = 'SELECT `Administratie nr.`, `Entry #`, `Date`, `IBAN`, `Vergadering`, `Datum-2`, `Post-2`, `Bedrag-2`, `Reisgegevens toevoegen-2`, `Status` FROM `cr_declaraties` WHERE `Actie` = \'Declaratie-reiskosten\' and `Relatiecode` = \''.$userName.'\' order by `Administratie nr.` desc';
		$sqlOvertochten = 'SELECT `Administratie nr.`, `Entry #`, `Date`, `IBAN`, `Overtocht`, `Datum-3`, `Post-3`, `Bedrag-3`, `Reisgegevens toevoegen-3`, `Status` FROM `cr_declaraties` WHERE `Actie` = \'Declaratie-overtochten\' and `Relatiecode` = \''.$userName.'\' order by `Administratie nr.` desc';

		$databaseInkopen = new Database();
		$databaseInkopen->query($sqlInkopen);	
		$resultInkopen = $databaseInkopen->resultset();
		
		$databaseReiskosten = new Database();
		$databaseReiskosten->query($sqlReiskosten);	
		$resultReiskosten = $databaseReiskosten->resultset();
		
		$databaseOvertochten = new Database();
		$databaseOvertochten->query($sqlOvertochten);	
		$resultOvertochten = $databaseOvertochten->resultset();

		$DeclaratiesHeader = '<p>Op deze pagina tref je een overzicht aan van de door jouw ingediende declaraties.<br>Dit overzicht wordt maandelijks bijgewerkt en is nog in ontwikkeling. Het status veld wordt momenteel nog niet gebruikt.</p>';
		
		$InkopenTable .= '<table class="mat-resp-striped-table">';
		$InkopenTable .= '<tr>';
		$InkopenTable .= '<th>Administratie nr.</th>';
		$InkopenTable .= '<th>Volg nr.</th>';
		$InkopenTable .= '<th>Gedeclareerd op</th>';
		$InkopenTable .= '<th>Uitbetalen op IBAN</th>';
		$InkopenTable .= '<th>Omschrijving</th>';
		$InkopenTable .= '<th>Doel</th>';
		$InkopenTable .= '<th>Post</th>';
		$InkopenTable .= '<th>Bedrag</th>';
		$InkopenTable .= '<th>Bewijslast</th>';
		$InkopenTable .= '<th>Status</th>';
		$InkopenTable .= '</tr>';
			
		$ReiskostenTable = '<table class="mat-resp-striped-table">';
		$ReiskostenTable .= '<tr>';
		$ReiskostenTable .= '<th>Administratie nr.</th>';
		$ReiskostenTable .= '<th>Volg nr.</th>';
		$ReiskostenTable .= '<th>Gedeclareerd op</th>';
		$ReiskostenTable .= '<th>Uitbetalen op IBAN</th>';
		$ReiskostenTable .= '<th>Vergadering</th>';
		$ReiskostenTable .= '<th>Datum</th>';
		$ReiskostenTable .= '<th>Post</th>';
		$ReiskostenTable .= '<th>Bedrag</th>';
		$ReiskostenTable .= '<th>Reisgegevens</th>';
		$ReiskostenTable .= '<th>Status</th>';
		$ReiskostenTable .= '</tr>';

		$OvertochtenTable = '<table class="mat-resp-striped-table">';
		$OvertochtenTable .= '<tr>';
		$OvertochtenTable .= '<th>Administratie nr.</th>';
		$OvertochtenTable .= '<th>Volg nr.</th>';
		$OvertochtenTable .= '<th>Gedeclareerd op</th>';
		$OvertochtenTable .= '<th>Uitbetalen op IBAN</th>';
		$OvertochtenTable .= '<th>Overtocht</th>';
		$OvertochtenTable .= '<th>Datum</th>';
		$OvertochtenTable .= '<th>Post</th>';
		$OvertochtenTable .= '<th>Bedrag</th>';
		$OvertochtenTable .= '<th>Reisgegevens</th>';
		$OvertochtenTable .= '<th>Status</th>';
		$OvertochtenTable .= '</tr>';
		
		$DeclaratiesFooter = '<p>Betekenis status;<br>Wacht op OC/SE   Na het indienen van een declaratie wacht deze op goedkeuring door de OC (CSz/CieO) of Secretaris (bestuur/overig).<br>Wacht op PM        Na goedkeuring door de OC/Secretaris wacht de declaratie op betaling door de penningmeester.<br>Wacht op 2e          Na betaling door de penningmeester wacht de uitvoering hiervan op een 2e handtekening van een bestuurder.<br>Afgekeurd              De declaratie is afgekeurd door de OC/Secretaris of penningmeester.<br>Betaald                   De declaratie is uitbetaald.</p>';

		if(count($resultInkopen) === 0){
			$InkopenTable .= '<tr>';
			$InkopenTable .= '<td>Je hebt (nog) geen declaraties in/aankopen gedaan.</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
			$InkopenTable .= '</tr>';

		}else{
			foreach ($resultInkopen as $key => $value) {
		
				$InkopenTable .= '<tr>';
				$InkopenTable .= '<td>'.$value["Administratie nr."] . '</td>';
				$InkopenTable .= '<td>'.$value["Entry #"] . '</td>';
				$InkopenTable .= '<td>'.$value["Date"] . '</td>';
				$InkopenTable .= '<td>'.$value["IBAN"] . '</td>';
				$InkopenTable .= '<td>'.$value["Omschrijving-1"] . '</td>';
				$InkopenTable .= '<td>'.$value["Doel-1"] . '</td>';
				$InkopenTable .= '<td>'.$value["Post-1"] . '</td>';
				$InkopenTable .= '<td>'.$value["Bedrag-1"] . '</td>';
				$InkopenTable .= '<td>'.$value["Bonnen toevoegen"] . '</td>';
				$InkopenTable .= '<td>'.$value["Status"] . '</td>';
				$InkopenTable .= '</tr>';
			}
		}
		$InkopenTable .= "</table>";
		
		if(count($resultReiskosten) === 0){
			$ReiskostenTable .= '<tr>';
			$ReiskostenTable .= '<td>Je hebt (nog) geen declaraties reiskosten gedaan.</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
			$ReiskostenTable .= '</tr>';

		}else{
			foreach ($resultReiskosten as $key => $value) {
		
				$ReiskostenTable .= '<tr>';
				$ReiskostenTable .= '<td>'.$value["Administratie nr."] . '</td>';
				$ReiskostenTable .= '<td>'.$value["Entry #"] . '</td>';
				$ReiskostenTable .= '<td>'.$value["Date"] . '</td>';
				$ReiskostenTable .= '<td>'.$value["IBAN"] . '</td>';
				$ReiskostenTable .= '<td>'.$value["Vergadering"] . '</td>';
				$ReiskostenTable .= '<td>'.$value["Doel-2"] . '</td>';
				$ReiskostenTable .= '<td>'.$value["Post-2"] . '</td>';
				$ReiskostenTable .= '<td>'.$value["Bedrag-2"] . '</td>';
				$ReiskostenTable .= '<td>'.$value["Reisgegevens toevoegen-2"] . '</td>';
				$ReiskostenTable .= '<td>'.$value["Status"] . '</td>';
				$ReiskostenTable .= '</tr>';
			}
		}
		$ReiskostenTable .= "</table>";
		
		if(count($resultOvertochten) === 0){
			$OvertochtenTable .= '<tr>';
			$OvertochtenTable .= '<td>Je hebt (nog) geen declaraties overtochten gedaan.</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
			$OvertochtenTable .= '</tr>';

		}else{
			foreach ($resultOvertochten as $key => $value) {
		
				$OvertochtenTable .= '<tr>';
				$OvertochtenTable .= '<td>'.$value["Administratie nr."] . '</td>';
				$OvertochtenTable .= '<td>'.$value["Entry #"] . '</td>';
				$OvertochtenTable .= '<td>'.$value["Date"] . '</td>';
				$OvertochtenTable .= '<td>'.$value["IBAN"] . '</td>';
				$OvertochtenTable .= '<td>'.$value["Overtocht"] . '</td>';
				$OvertochtenTable .= '<td>'.$value["Doel-3"] . '</td>';
				$OvertochtenTable .= '<td>'.$value["Post-3"] . '</td>';
				$OvertochtenTable .= '<td>'.$value["Bedrag-3"] . '</td>';
				$OvertochtenTable .= '<td>'.$value["Reisgegevens toevoegen-3"] . '</td>';
				$OvertochtenTable .= '<td>'.$value["Status"] . '</td>';
				$OvertochtenTable .= '</tr>';
			}
		}
		$OvertochtenTable .= "</table>";
		
	}
?>