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

if( current_user_can('administrator')) {
	
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
				echo '<button class="btn waves-effect waves-light" type="submit" name="action">Open de attributen van de bewaker
							<i class="material-icons right">send</i>
						</button>
					  </form>';

		$lidTableQuery = 'SELECT CONCAT(`Relatienr`,\'@trb.nu\') as `Source_UserPrincipalName`, `Roepnaam` as `Source_GivenName`, LTRIM(CONCAT(`Tussenvoegsels`,\' \',`Achternaam`)) as `Source_Surname`, `VolledigeNaam` as `Source_DisplayName`, `Lidstatus` as `Source_JobTitle`, `Verenigingsfunctie` as `Source_Department`, LOWER(`Email`) as `Source_OtherMails`, null as `Placeholder_TelephoneNumber`, `Mobiel` as `Source_Mobile`, null as `Placeholder_FacsimileTelephoneNumber`, `VolledigAdres` as `Source_StreetAddress`, `Woonplaats` as `Source_City`, CONCAT(LOWER(REPLACE(REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(`VolledigeNaam`, "-", 1),\' \',\'.\'),\'é\',\'e\'),\'ë\',\'e\'),\'ö\',\'o\')), \'@trb.nu\') as `Source_EmailAddresses`, `Postcode` as `Source_PostalCode`, `Land` as `Source_Country` FROM `cr_leden` where `Relatienr` = \''.$_POST["bewaker"].'\'';
		
		$database = new Database();
		$database->query($lidTableQuery);
		$lidTableResult = $database->resultset();
		
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
				
			// Tabbed mutatie formulier
			$lidTable = '</br>';
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
			$lidTable .= '<div class="row">';
			$lidTable .= '<ul class="tabs">
							<li class="tab"><a class="active" href="#accountdata">Account</a></li>
        					<!--<li class="tab"><a href="#apparatendata">Apparaten</a></li>-->
							<!--<li class="tab"><a href="#licenties_en_appsdata">Licenties en apps</a></li>-->
							<!--<li class="tab"><a href="#emaildata">E-mail</a></li>-->
							<!--<li class="tab"><a href="#onedrivedata">OneDrive</a></li>-->
        				</ul>';
			
			$lidTable .= '<div id="accountdata" class="col s12">'; // Start sectie Account
			
				$lidTable .= '<div class="col s2 push-s10">';
				$lidTable .='<img src="'.$base64.'" style="float:left;position:absolute;right:0;top:0;max-height:230px;"/></div>';
				$lidTable .= '<div class="col s10 pull-s2">';
				$lidTable .= '<table class="striped">';
				
				$lidTable .= '<tr>';
					$lidTable .= '<th>Source_UserPrincipalName</th>';
					$lidTable .= '<td>' . $value["Source_UserPrincipalName"] . '</td>';
					$lidTable .= '<th></th>';
					$lidTable .= '<td></td>';
				$lidTable .= '</tr>';
				$lidTable .= '<tr>';
					$lidTable .= '<th>Source_GivenName</th>';
					$lidTable .= '<td>' . $value["Source_GivenName"] . '</td>';
				$lidTable .= '</tr>';
				$lidTable .= '<tr>';	
					$lidTable .= '<th>Source_Surname</th>';
					$lidTable .= '<td>' . $value["Source_Surname"] . '</td>';
				$lidTable .= '</tr>';
				$lidTable .= '<tr>';	
					$lidTable .= '<th>Source_DisplayName</th>';
					$lidTable .= '<td>' . $value["Source_DisplayName"] . '</td>';
				$lidTable .= '</tr>';
				$lidTable .= '<tr>';	
					$lidTable .= '<th>Source_JobTitle</th>';
					$lidTable .= '<td>' . $value["Source_JobTitle"] . '</td>';
				$lidTable .= '</tr>';
				$lidTable .= '<tr>';	
					$lidTable .= '<th>Source_Department</th>';
					$lidTable .= '<td>' . $value["Source_Department"] . '</td>';
				$lidTable .= '</tr>';
				$lidTable .= '<tr>';	
					$lidTable .= '<th>Source_OtherMails</th>';
					$lidTable .= '<td>' . $value["Source_OtherMails"] . '</td>';
				$lidTable .= '</tr>';
			//	$lidTable .= '<tr>';	
			//		$lidTable .= '<th>Placeholder_TelephoneNumber</th>';
			//		$lidTable .= '<td>' . $value["Placeholder_TelephoneNumber"] . '</td>';
			//	$lidTable .= '</tr>';
				$lidTable .= '<tr>';	
					$lidTable .= '<th>Source_Mobile</th>';
					$lidTable .= '<td>' . $value["Source_Mobile"] . '</td>';
				$lidTable .= '</tr>';
			//	$lidTable .= '<tr>';	
			//		$lidTable .= '<th>Placeholder_FacsimileTelephoneNumber</th>';
			//		$lidTable .= '<td>' . $value["Placeholder_FacsimileTelephoneNumber"] . '</td>';
			//	$lidTable .= '</tr>';
				$lidTable .= '<tr>';	
					$lidTable .= '<th>Source_StreetAddress</th>';
					$lidTable .= '<td>' . $value["Source_StreetAddress"] . '</td>';
				$lidTable .= '</tr>';
				$lidTable .= '<tr>';	
					$lidTable .= '<th>Source_City</th>';
					$lidTable .= '<td>' . $value["Source_City"] . '</td>';
				$lidTable .= '</tr>';
				$lidTable .= '<tr>';	
					$lidTable .= '<th>Source_EmailAddresses</th>';
					$lidTable .= '<td>' . $value["Source_EmailAddresses"] . '</td>';
				$lidTable .= '</tr>';
				$lidTable .= '<tr>';	
					$lidTable .= '<th>Source_PostalCode</th>';
					$lidTable .= '<td>' . $value["Source_PostalCode"] . '</td>';
				$lidTable .= '</tr>';
				$lidTable .= '<tr>';	
					$lidTable .= '<th>Source_Country</th>';
					$lidTable .= '<td>' . $value["Source_Country"] . '</td>';
				$lidTable .= '</tr>';

				$lidTable .= '</table>';
			
			$lidTable .= '</div></div>'; // Einde sectie Account

			echo $lidTable;
		}
}
?>