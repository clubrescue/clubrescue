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

if( current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') || current_user_can('contributor')) {
	
			$query2 = ' SELECT `Relatienr`, `VolledigeNaam`, `Achternaam` FROM `cr_leden` ORDER BY `Achternaam` ASC';
			$database2 = new Database();
			$database2->query($query2);	
			$results2 = $database2->resultset();
			
				echo '<form action="" method="POST"><select class="form-control" name="bewaker">';
				foreach ($results2 as $key2 => $value2) {
					if ($_POST["bewaker"] === $value2["Relatienr"]){
						echo '<option value="'.$value2["Relatienr"].'" selected="selected">'.$value2["VolledigeNaam"].'</option>';
					}else{
						echo '<option value="'.$value2["Relatienr"].'">'.$value2["VolledigeNaam"].'</option>';	
					}
				}
				echo '</select>';
				echo '<button class="btn waves-effect waves-light" type="submit" name="action">Open de geselecteerde bewaker
							<i class="material-icons right">send</i>
						</button>
					  </form>';

		$lidTableQuery = 'SELECT `cr_leden`.* , SUM(`TransactieBedrag`) as `TransactieBedrag` FROM `cr_leden` left outer join `cr_transacties` on `cr_leden`.`Relatienr`= `cr_transacties`.`Relatienr` where `cr_leden`.`Relatienr` = \''.$_POST["bewaker"].'\'';
		
		$database = new Database();
		$database->query($lidTableQuery);
		$lidTableResult = $database->resultset();
		
		//	Maak hier dynamische dropdown query's.
			// Dropdown - Geslacht
			$tableQuery3 =  'SELECT DISTINCT `Geslacht` FROM `cr_leden` ORDER BY `Geslacht` ASC';
			$database3 = new Database();        
			$database3->query($tableQuery3);	
			$tableResult3 = $database3->resultset();
			// Dropdown - Legitimatie type
			$tableQuery4 =  'SELECT DISTINCT `Legitimatietype` FROM `cr_leden` ORDER BY `Legitimatietype` ASC';
			$database4 = new Database();        
			$database4->query($tableQuery4);	
			$tableResult4 = $database4->resultset();
			// Dropdown - Bankrekening type
			$tableQuery5 =  'SELECT DISTINCT `BankrekType` FROM `cr_leden` ORDER BY `BankrekType` ASC';
			$database5 = new Database();        
			$database5->query($tableQuery5);	
			$tableResult5 = $database5->resultset();
			// Dropdown - Lidstatus - Statische lijst
			//$Lidstatus = array( "Jeugdlid", "Lid", "Lid-van-verdienste", "Erelid", "Beschermheer-of-vrouwe" );
			$tableQuery6 =  'SELECT DISTINCT `Lidstatus` FROM `cr_leden` ORDER BY `Lidstatus` ASC';
			$database6 = new Database();        
			$database6->query($tableQuery6);	
			$tableResult6 = $database6->resultset();
			// Dropdown - Functie - Statische lijst
			//$Verenigingsfunctie = array( "Bestuur-VZ", "Bestuur-SE", "Bestuur-PM", "Bestuur-OC", "Bestuur-AB", "Kascommissie", "Commissie-Strandzaken", "Commissie-Opleidingen", "Functionaris-VCP", "Functionaris-IT", "Functionaris-RVR", "Werkgroep-Scenarios", "Ledenraad" );
			$tableQuery7 =  'SELECT DISTINCT `Verenigingsfunctie` FROM `cr_leden` ORDER BY `Verenigingsfunctie` ASC';
			$database7 = new Database();        
			$database7->query($tableQuery7);	
			$tableResult7 = $database7->resultset();
			// Dropdown - Landenlijst - Statische lijst - identiek houden aan nationaliteiten lijst!
			$Landen = array( "Nederland", "België", "Duitsland", "Zwitserland", "Nepal" );
			// Dropdown - Nationatliteiten lijst - Statische lijst - identiek houden aan landen lijst!
			$Nationaliteiten = array( "Nederlands", "Belgisch", "Duits", "Zwitsers", "Nepalees" );

		// Build table
		foreach ($lidTableResult as $key => $value) {
			// Intern foto statement
			if(substr_count($_SERVER['REQUEST_URI'], '/') === 2){
				if(file_exists('./pasfotos/'.$value["Relatienr"].'.jpg')){
					//HTTP Auth voor de pasfotos zit in de url.
					$path= './pasfotos/'.$value["Relatienr"].'.jpg';
				}else{
					$path = './pasfotos/pasfoto.jpg';
				}
			}
			// External foto statement for root use
			if(substr_count($_SERVER['REQUEST_URI'], '/') === 1){
				if(file_exists('././clubredders/pasfotos/'.$value["Relatienr"].'.jpg')){
					//HTTP Auth voor de pasfotos zit in de url.
					$path= './clubredders/pasfotos/'.$value["Relatienr"].'.jpg';
				}else{
					$path = './clubredders/pasfotos/pasfoto.jpg';
				}
			}
			
			if(@fopen($path,"r")==true){
				$type = pathinfo($path, PATHINFO_EXTENSION);
				$data = file_get_contents($path);
			}
			
			
		//	Maak hier dynamische dropdown menu's.
			// Dropdown - Geslacht		
			$lidTableGeslacht = '<td><select class="form-control" name="Geslacht">';
			foreach ($tableResult3 as $key3 => $value3) {
				if ($value["Geslacht"] === $value3["Geslacht"]){
					$lidTableGeslacht .= '<option value="'.$value3["Geslacht"].'" selected="selected">'.$value3["Geslacht"].'</option>';
				}else{
					$lidTableGeslacht .= '<option value="'.$value3["Geslacht"].'">'.$value3["Geslacht"].'</option>';	
				}
			}
			$lidTableGeslacht .= '</select></td>';
			
			// Dropdown - Legitimatie type
			$lidTableLegitimatietype = '<td><select class="form-control" name="Legitimatietype">';
			foreach ($tableResult4 as $key4 => $value4) {
				if ($value["Legitimatietype"] === $value4["Legitimatietype"]){
					$lidTableLegitimatietype .= '<option value="'.$value4["Legitimatietype"].'" selected="selected">'.$value4["Legitimatietype"].'</option>';
				}else{
					$lidTableLegitimatietype .= '<option value="'.$value4["Legitimatietype"].'">'.$value4["Legitimatietype"].'</option>';	
				}
			}
			$lidTableLegitimatietype .= '</select></td>';

			// Dropdown - Bankrekening type
			$lidTableBankrekType = '<td><select class="form-control" name="BankrekType">';
			foreach ($tableResult5 as $key5 => $value5) {
				if ($value["BankrekType"] === $value5["BankrekType"]){
					$lidTableBankrekType .= '<option value="'.$value5["BankrekType"].'" selected="selected">'.$value5["BankrekType"].'</option>';
				}else{
					$lidTableBankrekType .= '<option value="'.$value5["BankrekType"].'">'.$value5["BankrekType"].'</option>';	
				}
			}
			$lidTableBankrekType .= '</select></td>';

			// Dropdown - Lidstatus
			$lidTableLidstatus = '<td><select class="form-control" name="Lidstatus">';
			foreach ($tableResult6 as $key6 => $value6) {
				if ($value["Lidstatus"] === $value6["Lidstatus"]){
					$lidTableLidstatus .= '<option value="'.$value6["Lidstatus"].'" selected="selected">'.$value6["Lidstatus"].'</option>';
				}else{
					$lidTableLidstatus .= '<option value="'.$value6["Lidstatus"].'">'.$value6["Lidstatus"].'</option>';	
				}
			}
			$lidTableLidstatus .= '</select></td>';

			// Dropdown - Functie
			$lidTableVerenigingsfunctie = '<td><select class="form-control" name="Verenigingsfunctie">';
			foreach ($tableResult7 as $key7 => $value7) {
				if ($value["Verenigingsfunctie"] === $value7["Verenigingsfunctie"]){
					$lidTableVerenigingsfunctie .= '<option value="'.$value7["Verenigingsfunctie"].'" selected="selected">'.$value7["Verenigingsfunctie"].'</option>';
				}else{
					$lidTableVerenigingsfunctie .= '<option value="'.$value7["Verenigingsfunctie"].'">'.$value7["Verenigingsfunctie"].'</option>';	
				}
			}
			$lidTableVerenigingsfunctie .= '</select></td>';

			// Dropdown - Landenlijst
			$lidTableLanden = '<td><select class="form-control" name="Land">';
			foreach ($Landen as $land => $landvalue) {
				if ($value["Land"] === $landvalue){
					$lidTableLanden .= '<option value="'.$landvalue.'" selected="selected">'.$landvalue.'</option>';
				}else{
					$lidTableLanden .= '<option value="'.$landvalue.'">'.$landvalue.'</option>';	
				}
			}
			$lidTableLanden .= '</select></td>';
			
				// Dropdown - Landenlijst kopie t.b.v. matchen Geboorteland.
				$lidTableGeboorteland = '<td><select class="form-control" name="Geboorteland">';
				foreach ($Landen as $land => $landvalue) {
					if ($value["Geboorteland"] === $landvalue){
						$lidTableGeboorteland .= '<option value="'.$landvalue.'" selected="selected">'.$landvalue.'</option>';
					}else{
						$lidTableGeboorteland .= '<option value="'.$landvalue.'">'.$landvalue.'</option>';	
					}
				}
				$lidTableGeboorteland .= '</select></td>';
			
			// Dropdown - Nationatliteiten lijst
			$lidTableNationaliteit = '<td><select class="form-control" name="Nationaliteit">';
			foreach ($Nationaliteiten as $Nationaliteit => $Nationaliteitvalue) {
				if ($value["Nationaliteit"] === $Nationaliteitvalue){
					$lidTableNationaliteit .= '<option value="'.$Nationaliteitvalue.'" selected="selected">'.$Nationaliteitvalue.'</option>';
				}else{
					$lidTableNationaliteit .= '<option value="'.$Nationaliteitvalue.'">'.$Nationaliteitvalue.'</option>';	
				}
			}
			$lidTableNationaliteit .= '</select></td>';
			
			// Tabbed mutatie formulier
			$lidTable = '</br>';
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
			$lidTable .= '<div class="row">';
			$lidTable .= '<ul class="tabs">
							<li class="tab"><a class="active" href="#membersdata">Persoonsgegevens</a></li>
        					<li class="tab"><a href="#addressdata">Adresgegevens</a></li>
							<li class="tab"><a href="#communicationdata">Communicatie gegevens</a></li>
							<li class="tab"><a href="#paymentdata">Betaalgegevens</a></li>
							<li class="tab"><a href="#lifeguardingdata">Strandbewakingsgegevens</a></li>
							<li class="tab"><a href="#securitydata">Functie (rechten)</a></li>
        				</ul>';
			
			$lidTable .= '<div id="membersdata" class="col s12">'; // Start sectie Persoonsgegevens
			
				$lidTable .= '<div class="col s2 push-s10">';
				$lidTable .='<img src="'.$base64.'" style="float:left;position:absolute;right:0;top:0;max-height:230px;"/></div>';
				$lidTable .= '<div class="col s10 pull-s2">';
				$lidTable .= '<table class="striped">';
			
				$lidTable .= '<tr>';
					$lidTable .= '<th>Relatienr</th>';
					$lidTable .= '<td>' . $value["Relatienr"] . '</td>';
					$lidTable .= '<th>Soort</th>';
					$lidTable .= '<td><input name="Soort[]" type="text" value="'.$value["Soort"].'"></td>';
				$lidTable .= '</tr>';
				
				$lidTable .= '<tr>';
					$lidTable .= '<th>Achternaam</th>';
					$lidTable .= '<td><input name="Achternaam[]" type="text" value="'.$value["Achternaam"].'"></td>';
					$lidTable .= '<th>Tussenvoegsels</th>';
					$lidTable .= '<td><input name="Tussenvoegsels[]" type="text" value="'.$value["Tussenvoegsels"].'"></td>';
				$lidTable .= '</tr>';
				
				$lidTable .= '<tr>';
					$lidTable .= '<th>Roepnaam</th>';
					$lidTable .= '<td><input name="Relatienr[]" type="text" value="'.$value["Roepnaam"].'"></td>';
					$lidTable .= '<th>Voorletters</th>';
					$lidTable .= '<td><input name="Voorletters[]" type="text" value="'.$value["Voorletters"].'"></td>';
				$lidTable .= '</tr>';
				
				$lidTable .= '<tr>';
					$lidTable .= '<th>Volledige naam</th>';
					$lidTable .= '<td>' . $value["VolledigeNaam"] . '</td>';
					$lidTable .= '<th>Geslacht</th>';
					//$lidTable .= '<td><input name="Geslacht[]" type="text" value="'.$value["Geslacht"].'"></td>';
					$lidTable .= $lidTableGeslacht;
				$lidTable .= '</tr>';

				$lidTable .= '<tr>';
					$lidTable .= '<th>Geboortedatum</th>';
					$lidTable .= '<td><input name="GeboorteDatum[]" type="date" value="'.$value["GeboorteDatum"].'"></td>';
					$lidTable .= '<th>Geboorteplaats</th>';
					$lidTable .= '<td><input name="Geboorteplaats[]" type="text" value="'.ucfirst(strtolower($value["Geboorteplaats"])).'"></td>';
				$lidTable .= '</tr>';

				$lidTable .= '<tr>';
					$lidTable .= '<th>Lid van de TRB sinds</th>';
					$lidTable .= '<td><input name="LidSinds[]" type="date" value="'.$value["LidSinds"].'"></td>';
					$lidTable .= '<th>Lidstatus</th>';
					//$lidTable .= '<td><input name="Lidstatus[]" type="text" value="'.$value["Lidstatus"].'"></td>';
					$lidTable .= $lidTableLidstatus;
				$lidTable .= '</tr>';	
			
				$lidTable .= '</table>';
			
			$lidTable .= '</div></div>'; // Einde sectie Persoonsgegevens
			$lidTable .= '<div id="addressdata" class="col s12">'; // Start sectie Adresgegevens
			
				$lidTable .= '<div class="col s12">';
				$lidTable .= '<table class="striped">';
				
				$lidTable .= '<tr>';
					$lidTable .= '<th>Postcode</th>';
					$lidTable .= '<td><input name="Postcode[]" type="text" value="'.$value["Postcode"].'"></td>';
					$lidTable .= '<th>Huisnr</th>';
					$lidTable .= '<td><input name="Huisnr[]" type="text" value="'.$value["Huisnr"].'"></td>';
					$lidTable .= '<th>Woonplaats</th>';
					$lidTable .= '<td><input name="Woonplaats[]" type="text" value="'.ucfirst(strtolower($value["Woonplaats"])).'"></td>';
				$lidTable .= '</tr>';
				
				$lidTable .= '<tr>';
					$lidTable .= '<th>Straat</th>';
					$lidTable .= '<td><input name="Straat[]" type="text" value="'.$value["Straat"].'"></td>';
					$lidTable .= '<th>HuisnrToe</th>';
					$lidTable .= '<td><input name="HuisnrToe[]" type="text" value="'.$value["HuisnrToev"].'"></td>';
					$lidTable .= '<th>Land</th>';
					//$lidTable .= '<td><input name="Land[]" type="text" value="'.$value["Land"].'"></td>';
					$lidTable .= $lidTableLanden;
				$lidTable .= '</tr>';
				
				$lidTable .= '<tr>';
					$lidTable .= '<th>Volledig Adres</th>';
					$lidTable .= '<td>' . $value["VolledigAdres"] . '</td>';
					$lidTable .= '<th></th>';
					$lidTable .= '<td></td>';
					$lidTable .= '<th></th>';
					$lidTable .= '<td></td>';
				$lidTable .= '</tr>';
				
				$lidTable .= '</table>';
			
			$lidTable .= '</div></div>'; // Einde sectie Adresgegevens
			$lidTable .= '<div id="communicationdata" class="col s12">'; // Start sectie Communicatie gegevens
			
				$lidTable .= '<div class="col s12">';
				$lidTable .= '<table class="striped">';
				
				$lidTable .= '<tr>';
					$lidTable .= '<th>Telefoon</th>';
					$lidTable .= '<td><input name="Telefoon[]" type="text" value="'.$value["Telefoon"].'"></td>';
					$lidTable .= '<th>Mobiel</th>';
					$lidTable .= '<td><input name="Mobiel[]" type="text" value="'.$value["Mobiel"].'"></td>';
					$lidTable .= '<th>E-mail adres</th>';
					$lidTable .= '<td><input name="Email[]" type="text" value="'.$value["Email"].'"></td>';
				$lidTable .= '</tr>';
				
				$lidTable .= '<tr>';
					$lidTable .= '<th>Nieuwsbrief</th>';
					$lidTable .= '<td><input name="Opt-ins[]" type="number" value="'.$value["Opt-ins"].'"></td>';
					$lidTable .= '<th></th>';
					$lidTable .= '<td></td>';
					$lidTable .= '<th></th>';
					$lidTable .= '<td></td>';
				$lidTable .= '</tr>';
				
				$lidTable .= '</table>';
			
			$lidTable .= '</div></div>'; // Einde sectie Communicatie gegevens
			$lidTable .= '<div id="paymentdata" class="col s12">'; // Start sectie Betaalgegevens
			
				$lidTable .= '<div class="col s12">';
				$lidTable .= '<table class="striped">';
				
				$lidTable .= '<tr>';
					$lidTable .= '<th>Bankrek. type</th>';
					//$lidTable .= '<td><input name="BankrekType[]" type="text" value="'.$value["BankrekType"].'"></td>';
					$lidTable .= $lidTableBankrekType;
					$lidTable .= '<th></th>';
					$lidTable .= '<td></td>';
				$lidTable .= '</tr>';
				
				$lidTable .= '<tr>';
					$lidTable .= '<th>Bankrek. nr.</th>';
					$lidTable .= '<td><input name="BankrekNr[]" type="text" value="'.$value["BankrekNr"].'"></td>';
					$lidTable .= '<th></th>';
					$lidTable .= '<td></td>';
				$lidTable .= '</tr>';

				$lidTable .= '<tr>';
					$lidTable .= '<th>BIC</th>';
					$lidTable .= '<td><input name="BIC[]" type="text" value="'.$value["BIC"].'"></td>';
					$lidTable .= '<th>Contributie saldo</th>';				
					$lidTable .= '<td>' . $value["TransactieBedrag"] . '</td>';
				$lidTable .= '</tr>';

				$lidTable .= '<tr>';
					$lidTable .= '<th>Machtigingskenmerk</th>';
					$lidTable .= '<td>' . $value["Machtigingskenmerk"] . '</td>';
					$lidTable .= '<th>Ondertekend op</th>';
					if(substr($value["Machtigingskenmerk"], 0, 5) === 'TRBDM') {
						$lidTable .= '<td>'.$value["MachtigingOndertekend"].'</td>';
					} else {
						$lidTable .= '<td>2009-11-01</td>';
					}
				$lidTable .= '</tr>';
				
				$lidTable .= '</table>';
			
			$lidTable .= '</div></div>'; // Einde sectie Betaalgegevens
			$lidTable .= '<div id="lifeguardingdata" class="col s12">'; // Start sectie Strandbewakingsgegevens
			
				$lidTable .= '<div class="col s12">';
				$lidTable .= '<table class="striped">';

				$lidTable .= '<tr>';
					$lidTable .= '<th>Nationaliteit</th>';
					//$lidTable .= '<td><input name="Nationaliteit[]" type="text" value="'.$value["Nationaliteit"].'"></td>';
					$lidTable .= $lidTableNationaliteit;
					$lidTable .= '<th>Dieet</th>';
					$lidTable .= '<td><input name="Dieet[]" type="text" value="'.$value["Dieet"].'"></td>';
				$lidTable .= '</tr>';

				$lidTable .= '<tr>';
					$lidTable .= '<th></th>';
					$lidTable .= '<td></td>';
					$lidTable .= '<th>Noodcontact(en)</th>';
					$lidTable .= '<td><input name="Noodcontact[]" type="text" value="'.$value["Noodcontact"].'"></td>';
				$lidTable .= '</tr>';
				
				$lidTable .= '<tr>';
					$lidTable .= '<th>Geboorteland</th>';
					//$lidTable .= '<td><input name="Geboorteland[]" type="text" value="'.$value["Geboorteland"].'"></td>';
					$lidTable .= $lidTableGeboorteland;
					$lidTable .= '<th></th>';
					$lidTable .= '<td></td>';
				$lidTable .= '</tr>';
				
				$lidTable .= '</tr>';
					$lidTable .= '<th>Legitimatie</th>';
					//$lidTable .= '<td><input name="Legitimatietype[]" type="text" value="'.$value["Legitimatietype"].'"></td>';
					$lidTable .= $lidTableLegitimatietype;
					$lidTable .= '<th></th>';
					$lidTable .= '<td></td>';
				$lidTable .= '</tr>';

				$lidTable .= '</tr>';
					$lidTable .= '<th>Legitimatie nr.</th>';
					$lidTable .= '<td><input name="Legitimatienr[]" type="text" value="'.$value["Legitimatienr"].'"></td>';
					$lidTable .= '<th></th>';
					$lidTable .= '<td></td>';
				$lidTable .= '</tr>';

				$lidTable .= '</table>';
			
			$lidTable .= '</div></div>'; // Einde sectie Strandbewakingsgegevens
			$lidTable .= '<div id="securitydata" class="col s12">'; // Start sectie Functie (rechten)
			
				$lidTable .= '<div class="col s12">';
				$lidTable .= '<table class="striped">';
			
				$lidTable .= '<tr>';
					$lidTable .= '<th>Functie</th>';
					//$lidTable .= '<td><input name="Verenigingsfunctie[]" type="text" value="'.$value["Verenigingsfunctie"].'"></td>';
					$lidTable .= $lidTableVerenigingsfunctie;
				$lidTable .= '</tr>';
			
				$lidTable .= '</table>';
			
			$lidTable .= '</div></div>'; // Einde sectie Functie (rechten)

			$lidTable .= '<div class="divider"></div></br>
						  <button class="btn waves-effect waves-light" type="submit" name="action">Update het lid
						  </button>
						  </form>';
			$lidTable .= '</div>';
		}
}
echo $lidTable;
 
?>