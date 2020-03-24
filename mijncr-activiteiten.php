<?php
if (!isset($user)) {
				header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
				require_once("wp-authenticate.php");
				/*** REQUIRE USER AUTHENTICATION ***/
				login();
				/*** RETRIEVE LOGGED IN USER INFORMATION ***/
				$user = wp_get_current_user();
}
require_once 'util/utility.class.php';	
require_once 'util/database.class.php';
if (getUserName()) {
				if (LaatRoosterZien) {
								$tableQuery = 'SELECT * FROM `cr_activiteiten` where `Relatienr` = \'' . getUserName() . '\' and `Activiteit` not like \'%NI\' order by `Activiteit` desc';
				} else {
								$tableQuery = 'SELECT * FROM `cr_activiteiten` where `Relatienr` = \'' . getUserName() . '\' and `Activiteit` not like \'BWK' . Date('Y') . '%\' and `Activiteit` not like \'%NI\' order by `Activiteit` desc';
				}
				$database = new Database();
				$database->query($tableQuery);
				$tableResult       = $database->resultset();
				$activiteitenTable = '<table class="mat-resp-striped-table">';
				$activiteitenTable .= '<tr>';
				$activiteitenTable .= '<th>Activiteit</th>';
				$activiteitenTable .= '<th>Vanaf Datum</th>';
				$activiteitenTable .= '<th>Tot Datum</th>';
				$activiteitenTable .= '<th>Locatie</th>';
				$activiteitenTable .= '<th>Opmerkingen</th>';
				$activiteitenTable .= '</tr>';
				if (count($tableResult) === 0) {
								$activiteitenTable .= '<tr>';
								$activiteitenTable .= '<td>Je hebt (nog) geen activiteiten. </td><td></td><td></td><td></td><td></td><td></td>';
								$activiteitenTable .= '</tr>';
				} else {
								foreach ($tableResult as $key => $value) {
												$activiteitenTable .= '<tr>';
												$activiteitenTable .= '<td>' . $value["Activiteit"] . '</td>';
												$activiteitenTable .= '<td>' . $value["VanafDatum"] . '</td>';
												$activiteitenTable .= '<td>' . $value["TotDatum"] . '</td>';
												$activiteitenTable .= '<td>' . $value["Locatie"] . '</td>';
												$activiteitenTable .= '<td>' . $value["Opmerkingen"] . '</td>';
												$activiteitenTable .= '</tr>';
								}
				}
				$activiteitenTable .= "</table>";
}
?>