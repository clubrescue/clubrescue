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
				$AAD1IDQuery =  'SELECT CONCAT(`Relatienr`,\'@trb.nu\') as `Source_UserPrincipalName`, `Roepnaam` as `Source_GivenName`, LTRIM(CONCAT(`Tussenvoegsels`,\' \',`Achternaam`)) as `Source_Surname`, `VolledigeNaam` as `Source_DisplayName`, `Lidstatus` as `Source_JobTitle`, `Verenigingsfunctie` as `Source_Department`, LOWER(`Email`) as `Source_OtherMails`, null as `Placeholder_TelephoneNumber`, `Mobiel` as `Source_Mobile`, null as `Placeholder_FacsimileTelephoneNumber`, `VolledigAdres` as `Source_StreetAddress`, `Woonplaats` as `Source_City`, CONCAT(LOWER(REPLACE(REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(`VolledigeNaam`, "-", 1),\' \',\'.\'),\'é\',\'e\'),\'ë\',\'e\'),\'ö\',\'o\')), \'@trb.nu\') as `Source_EmailAddresses`, `Postcode` as `Source_PostalCode`, `Land` as `Source_Country` FROM `cr_leden`';
				
				// Code om tzt profiel foto toe te voegen aan Office 365
				// HTTP login for pasfoto's (Logins kunnen worden toegevoegd aan de .htpasswd file in /domains/trb.nu/.htpasswd)
				// $httplogin = 'SU_AZURE:WijWillen1koppeling';
				
				// START SAVE TO CSV CODE
				
				ini_set('display_errors', 1);
				ini_set('display_startup_errors', 1);
				error_reporting(E_ALL);
							
				try {
					$target = 'aad/aad1id.csv';
					$database = new Database();
					$database->query($AAD1IDQuery);
					$database->execute();
					if(file_exists($target)){
						$target_backup = 'backup/aad1id('.time().').csv';
						rename($target,$target_backup);
					}
					$output = fopen('aad/aad1id.csv', 'w');
					$header = true;
					while ($row = $database->fetch()) {
						if ($header) {
							fputcsv($output, array_keys($row));
							$header = false;
						}
						fputcsv($output, $row);
					}
					fclose($output);
				} catch (PDOException $e) {
					// error handler
					var_dump($e);
				}
				//END SAVE TO CSV CODE
			?>
			<!---// END stuff here for all contributors, authors, editors or admins--->
			<?php } ?>
		</div>
	</body>
</html>