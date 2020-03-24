<?php
	/*** Club.Redders - PAGE TEMPLATE TYPE - ExternalDataProvisioning - v0.8.5
	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/
	login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();

	// Include database class
	include 'util/utility.class.php';	
	include 'util/database.class.php';		
?>
<html>
	<head>
		<title>Club.Redders - EDP</title>
	</head>
	<body>
		<div>
			<p><a href="https://trb.nu/wp-login.php?action=logout&redirect_to=https://trb.nu/clubredders">Deze EDP actie is gelogd op naam van <?php echo $user->user_firstname . " " . $user->user_lastname;?></a></p>
			<!---// BEGIN stuff here for all contributors, authors, editors or admins--->
			<?php if(current_user_can('editor') || current_user_can('administrator') ) {  ?>
				<?php
				
				 echo "<html><body><table id=uitgevoerdeboekingen style=display:none>\r\n";
				// echo "<html><body><table id=uitgevoerdeboekingen>\r\n";

				$tableQuery =  'SELECT * FROM `cr_bankexports` ';	
				$tableQuery .= 'WHERE `Rentedatum` >= \'20181101\' AND `Rentedatum` <= \'20191031\'';
							
				$database = new Database();
					
				$database->query($tableQuery);	
				$tableResult = $database->resultset();				
				
				echo '<tr>';
				echo '<td>IBAN</td>';
				echo '<td>Munt</td>';
				echo '<td>BIC</td>';
				echo '<td>Volgnr</td>';
				echo '<td>Datum</td>';
				echo '<td>Rentedatum</td>';
				echo '<td>Bedrag</td>';
				echo '<td>Saldo_na_trn</td>';
				echo '<td>Tegenrekening_IBAN</td>';
				echo '<td>Naam_tegenpartij</td>';
				echo '<td>Naam_uiteindelijke_partij</td>';
				echo '<td>Naam_initierende_partij</td>';
				echo '<td>BIC_tegenpartij</td>';
				echo '<td>Code</td>';
				echo '<td>Batch_ID</td>';
				echo '<td>Transactiereferentie</td>';
				echo '<td>Machtigingskenmerk</td>';
				echo '<td>Incassant_ID</td>';
				echo '<td>Betalingskenmerk</td>';
				echo '<td>Omschrijving-1</td>';
				echo '<td>Omschrijving-2</td>';
				echo '<td>Omschrijving-3</td>';
				echo '<td>Reden_retour</td>';
				echo '<td>Oorspr_bedrag</td>';
				echo '<td>Oorspr_munt</td>';
				echo '<td>Koers</td>';
				echo '<td>BOEKNUMMER</td>';
				echo '<td>BEWIJSLAST</td>';
				echo '<td>ID</td>';
				echo '<tr>';

				foreach ($tableResult as $key => $value) {
					echo '<tr>';
						echo '<td>'.$value["IBAN"] . '</td>';
						echo '<td>'.$value["Munt"] . '</td>';
						echo '<td>'.$value["BIC"] . '</td>';
						echo '<td>'.$value["Volgnr"] . '</td>';
						echo '<td>'.$value["Datum"] . '</td>';
						echo '<td>'.$value["Rentedatum"] . '</td>';
						echo '<td>'.$value["Bedrag"] . '</td>';
						echo '<td>'.$value["Saldo_na_trn"] . '</td>';
						echo '<td>'.$value["Tegenrekening_IBAN"] . '</td>';
						echo '<td>'.$value["Naam_tegenpartij"] . '</td>';
						echo '<td>'.$value["Naam_uiteindelijke_partij"] . '</td>';
						echo '<td>'.$value["Naam_initierende_partij"] . '</td>';
						echo '<td>'.$value["BIC_tegenpartij"] . '</td>';
						echo '<td>'.$value["Code"] . '</td>';
						echo '<td>'.$value["Batch_ID"] . '</td>';
						echo '<td>'.$value["Transactiereferentie"] . '</td>';
						echo '<td>'.$value["Machtigingskenmerk"] . '</td>';
						echo '<td>'.$value["Incassant_ID"] . '</td>';
						echo '<td>'.$value["Betalingskenmerk"] . '</td>';
						echo '<td>'.$value["Omschrijving-1"] . '</td>';
						echo '<td>'.$value["Omschrijving-2"] . '</td>';
						echo '<td>'.$value["Omschrijving-3"] . '</td>';
						echo '<td>'.$value["Reden_retour"] . '</td>';
						echo '<td>'.$value["Oorspr_bedrag"] . '</td>';
						echo '<td>'.$value["Oorspr_munt"] . '</td>';
						echo '<td>'.$value["Koers"] . '</td>';
						echo '<td>'.$value["BOEKNUMMER"] . '</td>';
						echo '<td>'.$value["BEWIJSLAST"] . '</td>';
						echo '<td>'.$value["ID"] . '</td>';
					echo '</tr>';
				}
				
				echo "\n</table></body></html>";
				?>
			<!-- END stuff here for all contributors, authors, editors or admins -->
			<?php } ?>
		</div>
	</body>
</html>