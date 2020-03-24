<?php
	if(!isset($user)){
		header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
		require_once("wp-authenticate.php");
		/*** REQUIRE USER AUTHENTICATION ***/
		login();
		/*** RETRIEVE LOGGED IN USER INFORMATION ***/
		$user = wp_get_current_user();
	};

	// Include database class
	require_once 'util/utility.class.php';
	require_once 'util/database.class.php';

	if(getUserName()){

		$lidTableQuery = 'SELECT `cr_leden`.* , DATEDIFF(CURDATE( ),`LidSinds`) as `DagenLid`, SUM(`TransactieBedrag`) as `TransactieBedrag` FROM `cr_leden` left outer join `cr_transacties` on `cr_leden`.`Relatienr`= `cr_transacties`.`Relatienr` where `cr_leden`.`Relatienr` = \''.getUserName().'\'';
		
		$database = new Database();
		$database->query($lidTableQuery);
		$lidTableResult = $database->resultset();
		
		// Build table
		foreach ($lidTableResult as $key => $value) {
			// Intern foto statement
			if(substr_count($_SERVER['REQUEST_URI'], '/') === 2){
				if(file_exists('/srv/home/trbnu/domains/trb.nu/crbin/pasfotos/'.$value["Relatienr"].'.jpg')){
					//HTTP Auth voor de pasfotos zit in de url.
					$path= '/srv/home/trbnu/domains/trb.nu/crbin/pasfotos/'.$value["Relatienr"].'.jpg';
				}else{
					$path = './images/crm-photo-notavailable.svg';
				}
			}
			// External foto statement for root use
			if(substr_count($_SERVER['REQUEST_URI'], '/') === 1){
				if(file_exists('/srv/home/trbnu/domains/trb.nu/crbin/pasfotos/'.$value["Relatienr"].'.jpg')){
					//HTTP Auth voor de pasfotos zit in de url.
					$path= '/srv/home/trbnu/domains/trb.nu/crbin/pasfotos/'.$value["Relatienr"].'.jpg';
				}else{
					$path = './images/crm-photo-notavailable.svg';
				}
			}
			
			if(@fopen($path,"r")==true){
				$type = pathinfo($path, PATHINFO_EXTENSION);
				$data = file_get_contents($path);
			}
			
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
			$lidTable = '<div class="container"><div class="row"><div class="col s3 push-s7">';
			$lidTable .='<img src="'.$base64.'" style="float:left;	position:absolute;right:0;top:0;max-height:230px;"/></div>';
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
		}
	}
?>