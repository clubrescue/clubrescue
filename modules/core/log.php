<?php
	// Include database class
	include '../../util/utility.class.php';
	include '../../util/database.class.php';

	if(!isset($user)){
		header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
		require_once("../../wp-authenticate.php");
		/*** REQUIRE USER AUTHENTICATION ***/login();
		/*** RETRIEVE LOGGED IN USER INFORMATION ***/
		$user = wp_get_current_user();
	}

	// Temp access voor D271RRP, verder alleen nodig voor administrators.
	if(current_user_can( 'administrator' ) || $user->user_login === 'D271RRP'){

		$logTableQuery = "SELECT `timestamp`,`message`,CASE WHEN `VolledigeNaam` IS NULL THEN `user` ELSE `VolledigeNaam` END `VolledigeNaam` FROM  `cr_log` left join `cr_leden` on SUBSTRING_INDEX(`user`, '@', 1) = `Relatienr` order by `timestamp` desc";

		$database = new Database();
		$database->query($logTableQuery);	
		$tableResult = $database->resultset();

		//echo '<pre>';
		//var_dump($tableResult);
		//echo '</pre>';

		$logTable = '<table class="striped">';
		//$logTable .= '<thead>';
		$logTable .= '<tr>';
		$logTable .= '<th>Tijdstip</th>';
		$logTable .= '<th>Actie</th>';
		$logTable .= '<th>Door</th>';
		$logTable .= '</tr>';
		//$logTable .= '</thead>';
			//$logTable .= '<tbody>';

		if(count($tableResult) === 0){
			$logTable .= '<tr>';
			$logTable .= '<td>Er zijn (nog) geen activiteiten. </td><td></td><td></td>';
			$logTable .= '</tr>';

		}else{
			foreach ($tableResult as $key => $value) {
				$logTable .= '<tr>';
				$logTable .= '<td>'.$value["timestamp"] . '</td>';
				$logTable .= '<td>'.$value["message"] . '</td>';
				$logTable .= '<td>'.$value["VolledigeNaam"] . '</td>';
				$logTable .= '</tr>';
			}
		}

			//$logTable .= '</tbody>';
		$logTable .= "</table>";

	}	
?>
<?php include __DIR__ . '/../../header.php'; ?>
<main>
  <div class="container"><br>

	<div class="row">
      <div class="col s12">
        <ul class="tabs">
          <li class="tab"><a class="active" href="#log">Log gegevens</a></li>
        </ul>
      </div>
      <div id="log" class="col s12"><?php echo $logTable ?></div>
	</div>

  </div>
</main>
<?php include '../../footer.php'; ?>