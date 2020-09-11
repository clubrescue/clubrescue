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
		$lidTableQuery = 'SELECT `cr_leden`.* , DATEDIFF(CURDATE( ),`LidSinds`) as `DagenLid`, SUM(`TransactieBedrag`) as `TransactieBedrag` FROM `cr_leden` left outer join `cr_transacties` on `cr_leden`.`Relatienr`= `cr_transacties`.`Relatienr` where `cr_leden`.`Relatienr` = \''.$userName.'\'';
		
		$database = new Database();
		$database->query($lidTableQuery);
		$lidTableResult = $database->resultset();
		
		// Build table
		foreach ($lidTableResult as $key => $value) {
			$profielFotoSrc = getProfilePictureSource($value["Relatienr"]);
			
			$lidTable = '<p>Ontbreken of kloppen je gegevens niet? Wijzig je gegevens via het tabblad mijn acties.</p>';
			$lidTable .= '<div class="container"><div class="row"><div class="col s3 push-s7">';
			$lidTable .= '<img src="'.$profielFotoSrc.'" style="float:left;	position:absolute;right:0;top:0;max-height:230px;"/></div>';
			$lidTable .= '<div class="col s9 pull-s5" style="padding-right:185px">';
			$lidTable .= '<table class="striped">'; 

			$lidTable .= '<tr>';
				$lidTable .= '<th>Roepnaam</th>';
				$lidTable .= '<td>' . $value["Roepnaam"] . '</td>';
				$lidTable .= '<th>Relatienr</th>';
				$lidTable .= '<td>' . $value["Relatienr"] . '</td>';
			$lidTable .= '</tr>';

			$lidTable .= '<tr>';
				$lidTable .= '<th>Voorletters</th>';
				$lidTable .= '<td>' . $value["Voorletters"] . '</td>';
				$lidTable .= '<th>Lid van de TRB sinds</th>';
				$lidTable .= '<td>' . $value["LidSinds"] . ' ('.$value["DagenLid"].' dagen)</td>';
			$lidTable .= '</tr>';

			$lidTable .= '<tr>';
				$lidTable .= '<th>Tussenvoegsels</th>';
				$lidTable .= '<td>' . $value["Tussenvoegsels"] . '</td>';
				$lidTable .= '<th>Lidstatus</th>';
				$lidTable .= '<td>' . $value["Lidstatus"] . '</td>';
			$lidTable .= '</tr>';

			$lidTable .= '<tr>';
				$lidTable .= '<th>Achternaam</th>';
				$lidTable .= '<td>' . $value["Achternaam"] . '</td>';
				$lidTable .= '<th>Functie</th>';
				$lidTable .= '<td>' . $value["Verenigingsfunctie"] . '</td>';
			$lidTable .= '</tr>';

			$lidTable .= '<tr>';
				$lidTable .= '<th>Geslacht</th>';
				$lidTable .= '<td>' . $value["Geslacht"] . '</td>';
				$lidTable .= '<th>Geboortedatum</th>';
				$lidTable .= '<td>' . $value["GeboorteDatum"] . '</td>';
			$lidTable .= '</tr>';

			$lidTable .= '<tr>';
				$lidTable .= '<th>Geboorteplaats</th>';
				$lidTable .= '<td>' . ucfirst(strtolower($value["Geboorteplaats"])) . '</td>';
				$lidTable .= '<th>Geboorteland</th>';
				$lidTable .= '<td>' . $value["Geboorteland"] . '</td>';
			$lidTable .= '</tr>';

			$lidTable .= '<tr>';
				$lidTable .= '<th>Nationaliteit</th>';
				$lidTable .= '<td>' . $value["Nationaliteit"] . '</td>';
				$lidTable .= '<th>Legitimatie</th>';
				$lidTable .= '<td>' . $value["Legitimatietype"].' '. $value["Legitimatienr"] . '</td>';
			$lidTable .= '</tr>';

			$lidTable .= '<tr>';
				$lidTable .= '<th>Straat</th>';
				$lidTable .= '<td>' . $value["Straat"] . '</td>';
				$lidTable .= '<th>Huisnr</th>';
				$lidTable .= '<td>' . $value["Huisnr"].' '. $value["HuisnrToev"] . '</td>';
			$lidTable .= '</tr>';

			$lidTable .= '<tr>';
				$lidTable .= '<th>Postcode</th>';
				$lidTable .= '<td>' . $value["Postcode"] . '</td>';
				$lidTable .= '<th>Woonplaats</th>';
				$lidTable .= '<td>' . ucfirst(strtolower($value["Woonplaats"])).'</td>';
			$lidTable .= '</tr>';

			$lidTable .= '<tr>';
				$lidTable .= '<th>Land</th>';
				$lidTable .= '<td>' . $value["Land"] . '</td>';
				$lidTable .= '<th>Telefoon</th>';
				$lidTable .= '<td>' . $value["Telefoon"] . '</td>';
			$lidTable .= '</tr>';

			$lidTable .= '<tr>';
				$lidTable .= '<th>E-mail adres</th>';
				$lidTable .= '<td>' . $value["Email"] . '</td>';
				$lidTable .= '<th>Mobiel</th>';
				$lidTable .= '<td>' . $value["Mobiel"] . '</td>';
			$lidTable .= '</tr>';

			$lidTable .= '<tr>';
				$lidTable .= '<th>Dieet</th>';
				$lidTable .= '<td>' . $value["Dieet"] . '</td>';
				$lidTable .= '<th>Noodcontact(en)</th>';
				$lidTable .= '<td>' . $value["Noodcontact"] . '</td>';
			$lidTable .= '</tr>';

			$lidTable .= '</table>';
			$lidTable .= '<div class="divider"></div>';
			$lidTable .= '<table class="striped">'; 

			$lidTable .= '<tr>';
				$lidTable .= '<th>Bankrek. nr.</th>';
				$lidTable .= '<td>' . $value["BankrekNr"] . '</td>';
				$lidTable .= '<th>Machtigingskenmerk</th>';
				$lidTable .= '<td>' . $value["Machtigingskenmerk"] . '</td>';
			$lidTable .= '</tr>';

			$lidTable .= '<tr>';
				$lidTable .= '<th>BIC</th>';
				$lidTable .= '<td>' . $value["BIC"] . '</td>';
				$lidTable .= '<th>Ondertekend op</th>';
			if(substr($value["Machtigingskenmerk"], 0, 5) === 'TRBDM') {
				$lidTable .= '<td>'.$value["MachtigingOndertekend"].'</td>';
			} else {
				$lidTable .= '<td>2009-11-01</td>';
			}
			$lidTable .= '</tr>';

			$lidTable .= '<tr>';
				$lidTable .= '<th>Bankrek. type</th>';
				$lidTable .= '<td>' . $value["BankrekType"] . '</td>';
				$lidTable .= '<th>Contributie saldo</th>';				
				$lidTable .= '<td>' . $value["TransactieBedrag"] . '</td>';
			$lidTable .= '</tr>';

			$lidTable .= '</table>';
			$lidTable .= '</div></div></div>';
			$lidTable .= '<p>Voor het innen van de contributie maakt de TRB gebruik van de standaard Europese incasso.<br>Het incassant ID van de TRB is NL08ZZZ406365290000.<br><br>Legitimatie(type) afkortingen;<br>RB    Rijbewijs<br>PN    Paspoort<br>NI     Identiteitskaart</p>';
		}
	}
?>