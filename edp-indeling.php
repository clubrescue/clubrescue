<?php
	/*** Club.Redders - PAGE TEMPLATE TYPE - ExternalDataProvisioning - v0.8.5
	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/$user = wp_get_current_user();
	
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
			<?php 
				
				echo "<html><body><table id=indeling style=display:none>\r\n";
				//echo "<html><body><table id=indeling>\r\n";
				
				if( current_user_can('contributor') ||  current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') ) {	
					$tableQuery =  'SELECT * FROM `cr_activiteiten`';
				} else {
					$tableQuery = 'select * FROM `cr_activiteiten` WHERE `Activiteit` IN (SELECT `Activiteit` FROM `cr_activiteiten` WHERE `Relatienr` = \''.$user->user_login.'\' and `Locatie` IN (\'ASC\',\'OPL\') and `Activiteit` LIKE \'BWK'.date('Y').'%\')';			
					
				}

				$database = new Database();
					
				$database->query($tableQuery);	
				$tableResult = $database->resultset();		
				echo '<tr><td>Relatienr</td><td>Activiteit</td><td>VanafDatum</td><td>TotDatum</td><td>Locatie</td><td>Opmerkingen<td></td></tr>';
				foreach ($tableResult as $key => $value) {
					echo '<tr>';
						echo '<td>'.$value["Relatienr"] . '</td>';
						echo '<td>'.$value["Activiteit"] . '</td>';
						echo '<td></td>';//echo '<td>'.$value["VanafDatum"] . '</td>';
						echo '<td></td>';//echo '<td>'.$value["TotDatum"] . '</td>';
						echo '<td>'.$value["Locatie"] . '</td>';
						echo '<td></td>';//echo '<td>'.$value["Opmerkingen"] . '</td>';
					echo '</tr>';
				}

				echo "\n</table></body></html>";
				?>
		</div>
	</body>
</html>