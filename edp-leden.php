<?php
	/*** Club.Redders - PAGE TEMPLATE TYPE - ExternalDataProvisioning - v0.8.5
	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/login();
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
			<html><body><table id=leden style=display:none>
			<?php 
								
				//echo "<html><body><table id=leden style=display:none>\r\n";
				//echo "<html><body><table id=leden>\r\n";
				if( current_user_can('contributor') ||  current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') ) {
					$tableQuery =  'SELECT * FROM `cr_leden`';				
				} else {
					$tableQuery = 'select * from `cr_leden` where `Relatienr` in ( SELECT `Relatienr` FROM `cr_activiteiten` WHERE `Activiteit` IN (SELECT `Activiteit` FROM `cr_activiteiten` WHERE `Relatienr` = \''.$user->user_login.'\' and `Locatie` IN (\'ASC\',\'OPL\') and `Activiteit` LIKE \'BWK'.date('Y').'%\'))';			
				}
								
				$database = new Database();
				
				//HTTP login for pasfoto's (Logins kunnen worden toegevoegd aan de .htpasswd file in /domains/trb.nu/.htpasswd)
				$httplogin = 'SU_EXCEL:Excelwilgraaggezichtenzien';
									
				$database->query($tableQuery);	
				$tableResult = $database->resultset();		
				echo '<tr><td>Relatienr</td><td>Soort</td><td>VolledigeNaam</td><td>RoepNaam</td><td>Geslacht</td><td>GeboorteDatum</td><td>GeboortePlaats</td><td>Geboorteland</td><td>Nationaliteit</td><td>Legitimatietype</td><td>Legitimatienr</td><td>HeeftFoto</td><td>VolledigAdres</td><td>Woonplaats</td><td>Land</td><td>Telefoon</td><td>Mobiel</td><td>Email</td><td>LidSinds</td><td>Dieet</td><td>Noodcontact</td></tr>';
				foreach ($tableResult as $key => $value) {
					echo '<tr>';
						echo '<td>'.$value["Relatienr"] . '</td>';
						echo '<td></td>';//echo '<td>'.$value["Soort"] . '</td>';
						echo '<td>'.$value["VolledigeNaam"] . '</td>';
						echo '<td></td>';//echo '<td>'.$value["Roepnaam"] . '</td>';
						echo '<td></td>';//echo '<td>'.$value["Geslacht"] . '</td>';
						echo '<td>'.$value["GeboorteDatum"] . '</td>';
						echo '<td></td>';//echo '<td>'.$value["Geboorteplaats"] . '</td>';
						echo '<td></td>';//echo '<td>'.$value["Geboorteland"] . '</td>';
						echo '<td></td>';//echo '<td>'.$value["Nationaliteit"] . '</td>';
						echo '<td></td>';//echo '<td>'.$value["Legitimatietype"] . '</td>';
						echo '<td></td>';//echo '<td>'.$value["Legitimatienr"] . '</td>';
						echo '<td></td>';//echo '<td>https://@trb.nu/clubredders/pasfotos/pasfoto.jpg</td>';	
						echo '<td></td>';//echo '<td>'.$value["VolledigAdres"] . '</td>';
						echo '<td></td>';//echo '<td>'.$value["Woonplaats"] . '</td>';
						echo '<td></td>';//echo '<td>'.$value["Land"] . '</td>';
						echo '<td></td>';//echo '<td>'.$value["Telefoon"] . '</td>';
						echo '<td></td>';//echo '<td>'.$value["Mobiel"] . '</td>';
						echo '<td></td>';//echo '<td>'.$value["Email"] . '</td>';
						echo '<td></td>';//echo '<td>'.$value["LidSinds"] . '</td>';
						echo '<td></td>';//echo '<td>'.$value["Dieet"] . '</td>';
						echo '<td></td>';//echo '<td>'.$value["Noodcontact"] . '</td>';
					echo '</tr>';
				}
				
				//echo "\n</table></body></html>";
				?>
			</table></body></html>
		</div>
	</body>
</html>