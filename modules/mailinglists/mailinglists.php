<?php
	/*** Club.Redders - PAGE TEMPLATE TYPE - ExternalDataModule - v0.8.6
	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("../../wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();

	// Include database class
	include '../../util/utility.class.php';
	include '../../util/database.class.php';

	//ini_set('error_reporting', E_ALL);
	//ini_set('display_errors', 'On');  //On or Off
	if($user->ID !== 0){

	include __DIR__ . '/../../header.php';
?>
<?php
	if(isset($_POST['mailLijst'])){
		$mail = $_POST['mailLijst'];
		switch ($mail) {
			case 1:
				// Alle leden met email adres
				$sql =  'select distinct `Email` from  `cr_leden` where `Email` != \'\' order by `Relatienr`';
				break;
			case 2:
				// Alle ingeschreven leden
				$sql = 'select distinct `Email` from `cr_leden` as a inner join (select distinct `Relatienr` from `cr_activiteiten` where `Activiteit` LIKE \'BWK'.Date('Y').'%\' and `Activiteit` != \'BWK'.Date('Y').'NI\') as b on a.`Relatienr` = b.`Relatienr` where `Email` != \'\'';
				break;
			case 3:
				// Alle niet ingeroosterde leden
				$sql = 'select distinct `Email` from `cr_leden` as a inner join (select distinct `Relatienr` from `cr_activiteiten` where `Activiteit` = \'BWK'.Date('Y').'NI\') as b on a.`Relatienr` = b.`Relatienr`where `Email` != \'\'';
				break;
			case 4:
				// Alle examenkandidaten
				$sql = 'select distinct `Email` from `cr_leden` as a inner join (select distinct `Relatienr` from `cr_activiteiten` where `Activiteit` LIKE \'EXAMEN'.Date('Y').'%\') as b on a.`Relatienr` = b.`Relatienr`where `Email` != \'\'';
				break;
			case 5:
				// Opleiders
				$sql = 'select distinct `Email` from `cr_leden` as a inner join (select distinct `Relatienr` from `cr_diplomas` where `Diploma` IN (\'RWS Opleider\',\'Opleider\',\'Vaaropleider\')) as b on a.`Relatienr` = b.`Relatienr` where `Email` != \'\'';
				break;
			case 6:
				// Alle 809's
				$sql = 'select distinct `Email` from `cr_leden` as a inner join (select distinct `Relatienr` from `cr_diplomas` where `Diploma` = \'809\') as b on a.`Relatienr` = b.`Relatienr` where `Email` != \'\'';
				break;
			case 7:
				// ASC's
				$sql = 'select distinct `Email` from `cr_leden` as a inner join (select distinct `Relatienr` from `cr_diplomas` where `Diploma` = \'ASC\') as b on a.`Relatienr` = b.`Relatienr` where `Email` != \'\'';
				break;
			case 8:
				// 809 en ASC samen per seizoen
				$sql = 'select distinct `Email` from `cr_leden` as a inner join (select distinct `Relatienr` from `cr_activiteiten` where (`Locatie` LIKE \'%ASC%\' or `Locatie` LIKE \'%809%\' or `Locatie` LIKE \'%OPL%\') and `Activiteit` LIKE \'BWK'.Date('Y').'%\') as b on a.`Relatienr` = b.`Relatienr` where `Email` != \'\'';
				break;
			case 9:
				$sql = 'select distinct `Email` from `cr_leden`  where `Mobiel` =  \'\' and `Email` != \'\'';
				break;
			case 10:
				$sql = 'select `VolledigeNaam` as Email from `cr_leden`  where `Email` = \'\'';
				break;
			case 11:
				$sql = 'select distinct `Email` from `cr_leden` as a inner join (select distinct `Relatienr` from `cr_diplomas` where `Diploma` LIKE \'%niveau 3%\') as b on a.`Relatienr` = b.`Relatienr` where `Email` != \'\'';
				break;
			case 12:
				if(opschonenPasfotos($user)){
					if(updatePasfotoTable($user)){
						$sql = 'select distinct `Email` from `cr_leden` as a inner join (select distinct `Relatienr` from `cr_pasfotos` where `Aanwezig` = 0) as b on a.`Relatienr` = b.`Relatienr` where `Email` != \'\'';
					}
				}
				break;
			case 13:
				$sql = "select distinct `Email` from `cr_leden` as a"
				. " inner join (select distinct `Relatienr` from `cr_diplomas` where `Diploma` = 'Beoordelaar (2)') as b"
				. " on a.`Relatienr` = b.`Relatienr`"
				. " inner join (select distinct `Relatienr` from `cr_activiteiten` where `Activiteit` LIKE 'BWK".Date('Y')."%') as c"
				. " on b.`Relatienr` = c.`Relatienr`"
				. " where `Email` != ''";
				break;
			case 14:
				$sql = "select distinct `Email` from `cr_leden` as a "
				. " inner join (select distinct `Relatienr` from `cr_activiteiten` where `Activiteit` LIKE 'BWK".Date('Y')."%') as c"
				. " on a.`Relatienr` = c.`Relatienr`"
				. " where `Email` != '' and `Noodcontact` = ''";
				break;
			default:
				$sql = '';
		}

		$database = new Database();
		$database->query($sql);
		$queryResult = $database->resultset();
	}
?>
<main>
<div class="container">
		<div class="section">
			<!---// BEGIN stuff here for all contributors, authors, editors or admins--->
			<?php if( current_user_can('contributor') ||  current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') ) {  ?>
				<div class="row">
					<label>Met het onderstaande formulier kan een lijst van mail adressen worden opgevraagd. Als er 3 keer snel op de mailadressen geklikt wordt, worden ze allemaal geselecteerd.</label>
					<div class="input-field col s6">
						<form action="" method="POST">
							<select name="mailLijst">
								<option value="" disabled selected>Kies de gewenste lijst</option>
								<option value="1">Alle leden</option>
								<option value="2">Alle ingeschreven leden voor seizoen <?php echo Date("Y"); ?></option>
								<option value="3">Alle niet ingeroosterde leden voor seizoen <?php echo Date("Y"); ?></option>
								<option value="4">Alle examenkandidaten voor seizoen <?php echo Date("Y"); ?></option>
								<option value="5">Alle Opleiders</option>
								<option value="6">Alle 809's</option>
								<option value="7">Alle ASC's</option>
								<option value="8">Alle ASC's en 809's voor seizoen <?php echo Date("Y"); ?></option>
								<option value="11">Alle niveau 3 instructeurs</option>
								<option value="9">Alle leden zonder mobiel nummer</option>
								<option value="10">Alle leden zonder e-mailadres</option>
								<option value="12">Alle leden zonder pasfoto</option>
								<option value="13">Alle leden met PvB Beoordelaar (2) voor seizoen <?php echo Date("Y"); ?></option>
								<option value="14">Alle bewakers voor seizoen <?php echo Date("Y"); ?> zonder noodcontact</option>
							</select>
							<button class="btn waves-effect waves-light" type="submit" name="action">Maillijst opvragen<i class="material-icons right">submit</i></button>
						</form>
					</div>
					<div class="input-field col s6"></div>
				</div>
				<div class="row">
					<div class="input-field col s12">
						<?php
							if(isset($queryResult)) {
								echo '<pre>';
								foreach ($queryResult as $key => $value) {
									echo $value['Email'] . ';';
								}
								echo '</pre>';
							}
						?>
					</div>
				</div>
			<!---// END stuff here for all contributors, authors, editors or admins--->
			<?php } ?>
		</div>
</div>
</main>
<?php
	include '../../footer.php';
	}
?>