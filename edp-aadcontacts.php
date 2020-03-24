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
			<?php if( current_user_can('contributor') ||  current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') ) {  ?>
				<?php

				echo "<html><body><table id=aad1id>\r\n";

				$tableQuery =  'SELECT `Relatienr`, `Roepnaam` as `First Name`, `Tussenvoegsels` as `Middle Name`, `Achternaam` as `Last Name`, `VolledigeNaam`, `Lidstatus`, `Verenigingsfunctie`, `Email` as `E_mail_Address`, `Mobiel` as `Mobile_Phone`, `VolledigAdres` as `Home_Street`, `Woonplaats` as `Home_City`, REPLACE(`VolledigeNaam`,\' \',\'.\') as `E_mail_2_Address`, `Postcode` as `Home_Postal_Code`, `Land` as `Home_Country_Region`, `Noodcontact` as `Emergency_Spouse`, `GeboorteDatum` as `Birthday` FROM `cr_leden`';

				$database = new Database();
				$database->query($tableQuery);	
				$tableResult = $database->resultset();

				echo '<tr><td>First Name</td><td>Middle Name</td><td>Last Name</td><td>E-mail Address</td><td>E-mail 2 Address</td><td>Mobile Phone</td><td>Home Street</td><td>Home City</td><td>Home Postal Code</td><td>Home Country/Region</td><td>Spouse</td><td>Birthday</td></tr>';
				foreach ($tableResult as $key => $value) {

					echo '<tr>';
						echo '<td>'.$value["First Name"] . '</td>';
						echo '<td>'.$value["Middle Name"] . '</td>';
						echo '<td>'.$value["Last Name"] . '</td>';
						echo '<td>'.strtolower($value["E_mail_Address"]) . '</td>';
						echo '<td>'.strtolower($value["E_mail_2_Address"]) . '@trb.nu</td>';
						echo '<td>'.$value["Mobile_Phone"] . '</td>';
						echo '<td>'.$value["Home_Street"] . '</td>';
						echo '<td>'.$value["Home_City"] . '</td>';
						echo '<td>'.$value["Home_Postal_Code"] . '</td>';
						echo '<td>'.$value["Home_Country_Region"] . '</td>';
						echo '<td>'.$value["Emergency_Spouse"] . '</td>';
						echo '<td>'.$value["Birthday"] . '</td>';
					echo '</tr>';
				}

				echo "\n</table></body></html>";
				?>
			<!---// END stuff here for all contributors, authors, editors or admins--->
			<?php } ?>
		</div>
	</body>
</html>