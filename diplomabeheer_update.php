<?php
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
	
	if(current_user_can('contributor') ||  current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') ) {

		//if(isset($_POST["naam"]) && isset($_POST["soort"]) && isset($_POST["omschrijving"]) && isset($_POST["extraomschrijving"]) && isset($_POST["volgordenr"]) && isset($_POST["maandengeldig"]) && isset($_POST["afkorting"])){

			//if(count($_POST["naam"]) === count($_POST["soort"]) && count($_POST["omschrijving"]) === count($_POST["id"])){
				for($i=0;$i<count($_POST["naam"]);$i++){
					$queries[] = "UPDATE `cr_diplomabeheer` SET `Naam` = '".$_POST['naam'][$i]."', `Soort` = '".$_POST['soort'][$i]."', `Omschrijving` = '".$_POST['omschrijving'][$i]."' ,`Extraomschrijving` = '".$_POST['extraomschrijving'][$i]."' ,`Volgordenr` = '".$_POST['volgordenr'][$i]."' ,`Maandengeldig` = '".$_POST['maandengeldig'][$i]."' ,`Afkorting` = '".$_POST['afkorting'][$i]."' WHERE `cr_diplomabeheer`.`ID` = ".$_POST['id'][$i]."";
				}
			//}

		//}
			
		try{
			$database = new Database();
			$database->beginTransaction();
			
			foreach($queries as $query){
				$database->query($query);	
				$database->execute();
			}
			
			if($database->endTransaction()){
				echo 'Update succesvol, klik <a href="diplomabeheer.php">hier</a> om terug te gaan.';
				echo '<pre>';var_dump($queries);echo '</pre>';
			}else{
				echo 'Update NIET succesvol, klik <a href="diplomabeheer.php">hier</a> om terug te gaan.';
			}	
		} catch(PDOException $e) {
			$database->cancelTransaction();
			echo $e->getMessage();
		}
	}
?>
