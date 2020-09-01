<?php

	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("../../../wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/
	login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();
	
	// Include database class
	include '../../../util/utility.class.php';
	include '../../../util/database.class.php';
	
	if(current_user_can('administrator') || $user->user_login === 'D271RRP') {			if(isset($_POST["boeknummer"]) && isset($_POST["bewijslast"]) && isset($_POST["boeknummer"])){			if(count($_POST["boeknummer"]) === count($_POST["bewijslast"]) && count($_POST["boeknummer"]) === count($_POST["id"])){				for($i=0;$i<count($_POST["boeknummer"]);$i++){					$queries[] = "UPDATE `cr_diplomabeheer` SET `BOEKNUMMER` = '".$_POST['boeknummer'][$i]."', `BEWIJSLAST` = '".$_POST['bewijslast'][$i]."' WHERE `cr_diplomabeheer`.`ID` = ".$_POST['id'][$i]."";				}			}		}
		try{			$database = new Database();			$database->beginTransaction();			foreach($queries as $query){				$database->query($query);					$database->execute();			}				if($database->endTransaction()){				echo 'Update succesvol, klik <a href="alt-diplomabeheer.php">hier</a> om terug te gaan.';			}else{				echo 'Update NIET succesvol, klik <a href="alt-diplomabeheer.php">hier</a> om terug te gaan.';			}			} catch(PDOException $e) {			$database->cancelTransaction();			echo $e->getMessage();		}	}?>
