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
			<?php if(current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') ) {  ?>
				<?php
				
				echo "<html><body><table id=incassorun style=display:none>\r\n";
				//echo "<html><body><table id=incassorun>";

				$tableQuery =  'SELECT b.`BankrekNr` as `VerenigingsRekeningnummer` , b.`BIC` as `VerenigingsBIC` , b.`VerenigingsNaam`, a.`BankrekNr`, a.`BIC`, a.`VolledigeNaam`, b.`BasisContributie`, \'\' as `Omschrijving`, a.`Machtigingskenmerk`, a.`MachtigingOndertekend`,\'\' as `Sequence`,\'\' as `Datum`,\'\' as `End to End` FROM `cr_leden` as a left outer join `cr_options` as b on 1=1 where `Lidstatus` IN (\'Lid\', \'JeugdLid\')';
	
				$database = new Database();
					
				$database->query($tableQuery);	
				$tableResult = $database->resultset();		
				echo '<tr>
						<td>VerenigingsRekeningnummer</td>
						<td>VerenigingsBIC</td>
						<td>VerenigingsNaam</td>
						<td>BankrekNr</td>
						<td>BIC</td>
						<td>VolledigeNaam</td>
						<td>BasisContributie</td>
						<td>Omschrijving</td>
						<td>Machtigingskenmerk</td>
						<td>MachtigingOndertekend</td>
						<td>Suquence</td>
						<td>Datum</td>
						<td>End to End</td>
					</tr>';	
				
				foreach ($tableResult as $key => $value) {
					
					echo '<tr>';
						echo '<td>'.$value["VerenigingsRekeningnummer"] . '</td>';
						echo '<td>'.$value["VerenigingsBIC"] . '</td>';
						echo '<td>'.$value["VerenigingsNaam"] . '</td>';
						echo '<td>'.$value["BankrekNr"] . '</td>';
						echo '<td>'.$value["BIC"] . '</td>';
						echo '<td>'.$value["VolledigeNaam"] . '</td>';
						echo '<td>'.$value["BasisContributie"] . '</td>';
						echo '<td>'.$value["Omschrijving"] . '</td>';
						echo '<td>'.$value["Machtigingskenmerk"] . '</td>';
						echo '<td>'.$value["MachtigingOndertekend"] . '</td>';
						echo '<td>'.$value["Suquence"] . '</td>';
						echo '<td>'.$value["Datum"] . '</td>';
						echo '<td>'.$value["End to End"] . '</td>';	
					echo '</tr>';
				}			
				echo "</table></body></html>";
				?>
			<!---// END stuff here for all contributors, authors, editors or admins--->
			<?php } ?>
		</div>
	</body>
</html>