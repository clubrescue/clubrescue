<?php
	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("../../wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/
	login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();
	
	
	// Include database class
	include '../../util/utility.class.php';
	include '../../util/database.class.php';
	
	if(current_user_can('administrator') || current_user_can('administrator') || $user->user_login === 'D271RRP') {	

		if(isset($_POST["boeknummer"]) && isset($_POST["bewijslast"]) && isset($_POST["boeknummer"])){

			if(count($_POST["boeknummer"]) === count($_POST["bewijslast"]) && count($_POST["boeknummer"]) === count($_POST["id"])){
				for($i=0;$i<count($_POST["boeknummer"]);$i++){
					$queries[] = "UPDATE `cr_bankexports` SET `BOEKNUMMER` = '".$_POST['boeknummer'][$i]."', `BEWIJSLAST` = '".$_POST['bewijslast'][$i]."' WHERE `cr_bankexports`.`ID` = ".$_POST['id'][$i]."";
				}
			}

		}
			
		try{
			$database = new Database();
			$database->beginTransaction();
			
			foreach($queries as $query){
				$database->query($query);	
				$database->execute();
			}
			
			if($database->endTransaction()){
				include __DIR__ . '/../../header.php';
				echo '<main><div class="container"><div class="section">';
				echo 'Update succesvol, klik <a href="accounting.php">hier</a> om terug te gaan.';
				echo '</div></div></main>';
				include '../../footer.php';	
			}else{
				include __DIR__ . '/../../header.php';
				echo '<main><div class="container"><div class="section">';
				echo 'Update NIET succesvol, klik <a href="accounting.php">hier</a> om terug te gaan.';
				echo '</div></div></main>';
				include '../../footer.php';	
			}	
		} catch(PDOException $e) {
			$database->cancelTransaction();
			echo $e->getMessage();
		}
	}
?>