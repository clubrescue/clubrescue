<?php
	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	
	require_once("../../wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/
	login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();	
	
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 'On');  //On or Off	
	
	// Include database class
	require_once '../../util/utility.class.php';	
	require_once '../../util/database.class.php';	
	
	if($user->ID !== 0){
	 
	include __DIR__ . '/../../header.php';
	
?>

<?php
	
	$database = new Database();
	
	$creditCount = '999';
	$debitCount = '999'; 

	
	if(isset($_POST['mailLijst'])){
		$mail = $_POST['mailLijst'];
			
		switch ($mail) {
			case 1:
				// Alle leden met email adres	
				$sql = 'insert into `cr_transacties` (`Relatienr`,`Omschrijving`,`TransactieBedrag`) select `Relatienr`,\'Contributie Credit '.Date('Y').'\', -`BasisContributie` from `cr_leden` left outer join `cr_options` on 1=1 where `LidStatus` in (\'Lid\',\'JeugdLid\')';
				$sqlLog = 'INSERT INTO `cr_log`(`message`, `user`) VALUES (\'Contributie Credit run gedraaid.\',\''.$user->user_login.'\')';
				break;
			case 2:
				$sql = 'insert into `cr_transacties` (`Relatienr`,`Omschrijving`,`TransactieBedrag`) select `Relatienr`,\'Contributie Debit '.Date('Y').'\', `BasisContributie` from `cr_transacties` left outer join cr_options on 1=1 where `Omschrijving` = \'Contributie Credit '.Date('Y').'\';';
				$sqlLog = 'INSERT INTO `cr_log`(`message`, `user`) VALUES (\'Contributie Debit run gedraaid.\',\''.$user->user_login.'\')';
				break;
			default:
				$sql = '';
				$sqlLog = '';
		}
		
		$database->query($sql);	
		$results = $database->execute();	
		if($results){
			echo 'Incasso run is goed verwerkt.';
			$database->query($sqlLog);	
			$logResult = $database->execute();			
		}
					
		$credit = 'select count(*) as `CreditCount` from `cr_transacties` where `Omschrijving` = \'Contributie Credit '.Date('Y').'\'';
		$database->query($credit);
		$creditCount = $database->single();	
			
		$debit = 'select count(*) as `DebitCount` from `cr_transacties` where `Omschrijving` = \'Contributie Debit '.Date('Y').'\'';
		$database->query($debit);
		$debitCount = $database->single();	
	}
	
	if(isset($_POST['Relatienr'])){
		
		$relatienr = $_POST['Relatienr'];
		$debitCredit = $_POST['debitCredit'];
			
		if($relatienr === '' || $debitCredit === '' || $debitCredit === null){
			echo 'Vul zowel relatie nummer als Debit/Credit in.';
		}else{
			if($debitCredit === '+'){$text = 'Debit';}elseif($debitCredit === '-'){$text = 'Credit';};
			$opmerking = '\'Contributie '.$text.' '.Date('Y').'\'';
			$sql = 'insert into cr_transacties (`Relatienr`,`Omschrijving`,`TransactieBedrag`) select \''.$relatienr.'\','.$opmerking.', '.$debitCredit.'`BasisContributie` from cr_options;';
			$database->query($sql);	
			$results = $database->execute();
			if($results){
				$resultMessage = $text.' contributie '.Date('Y').' voor '.$relatienr.' is verwerkt.';
				
				echo $resultMessage;
				$sqlLog = 'INSERT INTO `cr_log`(`message`, `user`) VALUES (\''.$resultMessage.'\',\''.$user->user_login.'\')';
				$database->query($sqlLog);	
				$logResult = $database->execute();	
				
			}
		}
			
		$credit = 'select count(*) as `CreditCount` from `cr_transacties` where `Omschrijving` = \'Contributie Credit '.Date('Y').'\'';
		$database->query($credit);
		$creditCount = $database->single();	
			
		$debit = 'select count(*) as `DebitCount` from `cr_transacties` where `Omschrijving` = \'Contributie Debit '.Date('Y').'\'';
		$database->query($debit);
		$debitCount = $database->single();	
	}
	
	$credit = 'select count(*) as `CreditCount` from `cr_transacties` where `Omschrijving` = \'Contributie Credit '.Date('Y').'\'';
	$database->query($credit);
	$creditCount = $database->single();	

	$debit = 'select count(*) as `DebitCount` from `cr_transacties` where `Omschrijving` = \'Contributie Debit '.Date('Y').'\'';
	$database->query($debit);
	$debitCount = $database->single();
	
	?>

		<div class="section">
			<!---// BEGIN stuff here for all contributors, authors, editors or admins--->
			<?php if(current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') ) {  ?>
				<div class="row">
					<p>Met het onderstaande formulier kan een incassorun gedaan worden voor de contributie van het jaar <?php echo Date("Y"); ?>.</p>
					<div class="input-field col s6">
						<form action="" method="POST">
							<select name="mailLijst">
								<option value="" disabled selected>Kies de gewenste lijst</option>
								<option value="1" <?php if($creditCount["CreditCount"] !== '0'){ echo 'disabled';};?>>Initiele incassorun voor 'Contributie <?php echo Date("Y"); ?>'.</option>
								<option value="2" <?php if($debitCount["DebitCount"]  !== '0' || $creditCount["CreditCount"] === '0'){ echo 'disabled';};?>>Initiele incassorun voor 'Contributie <?php echo Date("Y"); ?>' ontvangen.</option>
							</select>
							<input class="waves-effect waves-light btn" type="submit"/>
						</form>
					</div>
					<div class="input-field col s6">
						<form action="" method="POST">
							<input name="Relatienr" type="text" placeholder="Relatienummer"/>
							<select name="debitCredit">
								<option value="" disabled selected>Kies de gewenste post</option>
								<option value="+" <?php if($debitCount["DebitCount"]  === '0' || $creditCount["CreditCount"] === '0'){ echo 'disabled';};?>> Crediteuren betaling </option>
								<option value="-" <?php if($debitCount["DebitCount"]  === '0' || $creditCount["CreditCount"] === '0'){ echo 'disabled';};?>> Stornering </option>
							</select>
							<?php //echo 'Debit:'.$debitCount["DebitCount"].'<br>'; ?>
							<?php //echo 'Credit:'.$debitCount["CreditCount"]; ?>
							<input class="waves-effect waves-light btn" type="submit" <?php if($debitCount["DebitCount"]  === '0' || $creditCount["CreditCount"] === '0'){ echo 'disabled';};?>/>
						</form>				
					</div>
				</div>
					<div class="input-field col s6">
					</div>
				</div>
			<!---// END stuff here for all contributors, authors, editors or admins--->
	<?php } ?>	
<?php
	include '../../footer.php';	
	}
?>	
<!--

insert into cr_transacties (`Relatienr`,`Omschrijving`,`TransactieBedrag`) select `Relatienr`,'Contributie Credit 2017', -`BasisContributie` from cr_leden left outer join cr_options on 1=1 where `LidStatus` in ('Lid','JeugdLid');

insert into cr_transacties (`Relatienr`,`Omschrijving`,`TransactieBedrag`) select `Relatienr`,'Contributie 2017', `BasisContributie` from cr_transacties left outer join cr_options on 1=1 where `Omschrijving` = 'Contributie 2017';

insert into cr_transacties (`Relatienr`,`Omschrijving`,`TransactieBedrag`) select 'D290BLU','Contributie 2017', -`BasisContributie` from cr_options;

-->