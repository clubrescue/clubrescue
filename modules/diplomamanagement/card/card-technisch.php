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

	$where4kader = 'BWK%';
	$where4ASCOPL = 'BWK'.Date("Y").'%';
	$whereIsNot = '%NI';
	
	$locations = ['','19','20','21','28','PC19','PC20','PC21','PC28','ASC','OPL','CVD','CVDS'];

	if( current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') || current_user_can('contributor')) {	
		$query = "SELECT distinct `Activiteit` FROM  `cr_activiteiten` WHERE `Activiteit` LIKE '$where4kader' AND `Activiteit` NOT LIKE '$whereIsNot' ORDER BY `ACTIVITEIT` DESC";
	}else{
		$query = "SELECT distinct `Activiteit` FROM  `cr_activiteiten` WHERE  `Relatienr` =  '$user->user_login' AND  `Locatie` IN ('OPL',  'ASC') AND `Activiteit` LIKE '$where4ASCOPL' AND `Activiteit` NOT LIKE '$whereIsNot' ORDER BY `ACTIVITEIT` ASC";
	}
	$database = new Database();
	$database->query($query);	
	$results = $database->resultset();
	
	// Materialize design
	//include __DIR__ . '/../../../header.php';
	//echo '<main>';
	//echo '<div class="container">';
	
	echo '<html>
		<style type="text/css">
		.tg  {border-collapse:collapse;border-spacing:0;width:1100px;table-layout:fixed}
		.tg td{font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif;font-size:15px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black}
		.tg th{font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif;font-size:15px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;font-weight:normal}
		.tg .tg-grijzebalk		{text-align:center;vertical-align:top;border-color:transparent;font-weight:bold;background-color:#d9d9d9}
		.tg .tg-logobalk		{text-align:center;vertical-align:top;border-color:transparent}
		.tg .tg-afkortingenbalk	{text-align:left;vertical-align:top;border-color:transparent;font-size:11px}
		.tg .tg-competentie		{text-align:center;vertical-align:top;border-top;width:100px;height:100px}'                          /* Nog te behalen competenties of lege posities */ .'
		.tg .tg-competentie-bmv	{text-align:center;vertical-align:top;border-top;width:100px;height:100px;background-color:#d9d9d9}' /* Behaald met vervaldatum */ .'
		.tg .tg-competentie-bzv	{text-align:center;vertical-align:top;border-top;width:100px;height:100px;background-color:#808080}' /* Behaald zonder vervaldatum */ .'
		.tg .tg-footer			{text-align:left;vertical-align:top;border-color:transparent}
		.tg .tg-footersig		{text-align:right;vertical-align:top;border-color:transparent;font-size:11px;font-style:italic} div{border:1px solid;width:210px;height:50px}
		</style>
		<body>';
	
	if(is_null($_POST["bewaker"])) {
				
					$tableQuery = ' SELECT
									\'D123ABC\' as `Relatienr`,
									\'Naam van de Bewaker\' as `VolledigeNaam`,
									beheer.`Volgordenr`,
									beheer.`Naam` as `Diploma`,
									null as `Status`,
									null as `CompetentiesRegel1`,
									null as `CompetentiesRegel2`,
									beheer.`Row`,
									beheer.`Column`,
									beheer.`Label`
								FROM
									`cr_leden` leden
									JOIN `cr_diplomabeheer` beheer ON `Row` <> 0 and `Column` <> 0
									LEFT JOIN `cr_diplomas` diplomas ON beheer.`Naam` = diplomas.`Diploma`
									and diplomas.`Relatienr` = leden.`Relatienr`
									left join (
										SELECT
											diplomas.`Relatienr`,
											GROUP_CONCAT(
												DISTINCT beheer.`Afkorting`
												ORDER BY
													beheer.`Volgordenr` SEPARATOR \' \'
											) as `CompetentiesRegel1`
										FROM
											`cr_diplomas` diplomas
											JOIN `cr_diplomabeheer` beheer ON beheer.`Naam` = diplomas.`Diploma`
										WHERE
											-- UPDATE HIER WELKE SOORTEN DIPLOMAS ER OP REGEL 1 MOETEN, DIPLOMA MOET GELDIG ZIJN OF GEEN EINDDATUM HEBBEN
											diplomas.`Soort` IN (\'PvB\', \'Niveau\')
											and (
												`EindDatum` = \'0000-00-00\'
												OR `EindDatum` >= NOW()
											)
											and beheer.`Afkorting` <> \'\'
										GROUP BY
											diplomas.`Relatienr`
									) regel1 ON regel1.`Relatienr` = leden.`RelatieNr`
									left join (
										SELECT
											diplomas.`Relatienr`,
											GROUP_CONCAT(
												DISTINCT beheer.`Afkorting`
												ORDER BY
													beheer.`Volgordenr` SEPARATOR \' \'
											) as `CompetentiesRegel2`
										FROM
											`cr_diplomas` diplomas
											JOIN `cr_diplomabeheer` beheer ON beheer.`Naam` = diplomas.`Diploma`
										WHERE
											-- UPDATE HIER WELKE SOORTEN DIPLOMAS ER OP REGEL 2 MOETEN, DIPLOMA MOET GELDIG ZIJN OF GEEN EINDDATUM HEBBEN
											diplomas.`Soort` IN (\'Bondsdiploma\', \'EvC\')
											and (
												`EindDatum` = \'0000-00-00\'
												OR `EindDatum` >= NOW()
											)
											and beheer.`Afkorting` <> \'\'
										GROUP BY
											diplomas.`Relatienr`
									) regel2 ON regel2.`Relatienr` = leden.`RelatieNr`
								WHERE
									beheer.`Soort` = \'PvB\'
									and leden.`VolledigeNaam` = \'Ruud Borghouts\'
									OR beheer.`Soort` = \'Label\'
									and leden.`VolledigeNaam` = \'Ruud Borghouts\'
									ORDER BY
									beheer.`Volgordenr`';
		
		$database->query($tableQuery);	
		$tableResult = $database->resultset();
		}
	
	if($database->RowCount() > 0){

		echo '<form action="" method="POST"><select class="form-control" name="week">';
		echo '<option value="'.date("d-m-Y").'">'.date("d-m-Y").'</option>';
		foreach ($results as $key => $value) {
			if ($_POST["week"] === $value["Activiteit"]){
				echo '<option value="'.$value["Activiteit"].'" selected="selected">'.$value["Activiteit"].'</option>';
			}else{
				echo '<option value="'.$value["Activiteit"].'">'.$value["Activiteit"].'</option>';
			}
		}
		echo '</select>';
		if(isset($_POST["week"]) AND $_POST["week"] != date("d-m-Y")) {
			//Zodra de week is gekozen kun je een bewaker kiezen:
			
			$query2 = ' SELECT leden.`RelatieNr`, leden.`VolledigeNaam`, leden.`Achternaam`, act.`Locatie` FROM `cr_leden` leden join `cr_activiteiten` act on leden.`RelatieNr` = act.`RelatieNr` and act.`Activiteit` = \''.$_POST["week"].'\' ORDER BY leden.`Achternaam` ASC';
			$database2 = new Database();
			$database2->query($query2);	
			$results2 = $database2->resultset();

				echo '<select class="form-control" name="bewaker">';
				foreach ($results2 as $key2 => $value2) {
					if ($_POST["bewaker"] === $value2["VolledigeNaam"]){
						echo '<option value="'.$value2["VolledigeNaam"].'" selected="selected">'.$value2["VolledigeNaam"].'</option>';
					}else{
						echo '<option value="'.$value2["VolledigeNaam"].'">'.$value2["VolledigeNaam"].'</option>';	
					}
				}
				echo '</select>';

		}else{
			//Zodra de week is gekozen kun je een bewaker kiezen:
			
			$query2 = ' SELECT `RelatieNr`, `VolledigeNaam`, `Achternaam` FROM `cr_leden` ORDER BY `Achternaam` ASC';
			$database2 = new Database();
			$database2->query($query2);	
			$results2 = $database2->resultset();
			
				echo '<select class="form-control" name="bewaker">';
				foreach ($results2 as $key2 => $value2) {
					if ($_POST["bewaker"] === $value2["VolledigeNaam"]){
						echo '<option value="'.$value2["VolledigeNaam"].'" selected="selected">'.$value2["VolledigeNaam"].'</option>';
					}else{
						echo '<option value="'.$value2["VolledigeNaam"].'">'.$value2["VolledigeNaam"].'</option>';	
					}
				}
				echo '</select>';
		}
		echo '<button class="btn waves-effect waves-light" type="submit" name="action">Open de geselecteerde week
					<i class="material-icons right">send</i>
				</button>
			  </form>';
	
		if(isset($_POST["bewaker"])) {
				
					$tableQuery = ' SELECT
									leden.`Relatienr`,
									leden.`VolledigeNaam`,
									beheer.`Volgordenr`,
									beheer.`Naam` as `Diploma`,
									CASE
										WHEN diplomas.`Relatienr` IS NOT NULL
										AND beheer.`Maandengeldig` = \'0\' THEN \'Behaald\'
										WHEN diplomas.`Relatienr` IS NOT NULL
										AND DATE_ADD(
											diplomas.`IngangsDatum`,
											INTERVAL beheer.`Maandengeldig` MONTH
										) >= NOW() THEN DATE_ADD(
											diplomas.`IngangsDatum`,
											INTERVAL beheer.`Maandengeldig` MONTH
										)
										ELSE \'\'
									END AS `Status`,
									regel1.`CompetentiesRegel1`,
									regel2.`CompetentiesRegel2`,
									beheer.`Row`,
									beheer.`Column`,
									beheer.`Label`
								FROM
									`cr_leden` leden
									JOIN `cr_diplomabeheer` beheer ON `Row` <> 0 and `Column` <> 0
									LEFT JOIN `cr_diplomas` diplomas ON beheer.`Naam` = diplomas.`Diploma`
									and diplomas.`Relatienr` = leden.`Relatienr`
									left join (
										SELECT
											diplomas.`Relatienr`,
											GROUP_CONCAT(
												DISTINCT beheer.`Afkorting`
												ORDER BY
													beheer.`Volgordenr` SEPARATOR \' \'
											) as `CompetentiesRegel1`
										FROM
											`cr_diplomas` diplomas
											JOIN `cr_diplomabeheer` beheer ON beheer.`Naam` = diplomas.`Diploma`
										WHERE
											-- UPDATE HIER WELKE SOORTEN DIPLOMAS ER OP REGEL 1 MOETEN, DIPLOMA MOET GELDIG ZIJN OF GEEN EINDDATUM HEBBEN
											diplomas.`Soort` IN (\'PvB\', \'Niveau\')
											and (
												`EindDatum` = \'0000-00-00\'
												OR `EindDatum` >= NOW()
											)
											and beheer.`Afkorting` <> \'\'
										GROUP BY
											diplomas.`Relatienr`
									) regel1 ON regel1.`Relatienr` = leden.`RelatieNr`
									left join (
										SELECT
											diplomas.`Relatienr`,
											GROUP_CONCAT(
												DISTINCT beheer.`Afkorting`
												ORDER BY
													beheer.`Volgordenr` SEPARATOR \' \'
											) as `CompetentiesRegel2`
										FROM
											`cr_diplomas` diplomas
											JOIN `cr_diplomabeheer` beheer ON beheer.`Naam` = diplomas.`Diploma`
										WHERE
											-- UPDATE HIER WELKE SOORTEN DIPLOMAS ER OP REGEL 2 MOETEN, DIPLOMA MOET GELDIG ZIJN OF GEEN EINDDATUM HEBBEN
											diplomas.`Soort` IN (\'Bondsdiploma\', \'EvC\')
											and (
												`EindDatum` = \'0000-00-00\'
												OR `EindDatum` >= NOW()
											)
											and beheer.`Afkorting` <> \'\'
										GROUP BY
											diplomas.`Relatienr`
									) regel2 ON regel2.`Relatienr` = leden.`RelatieNr`
								WHERE
									beheer.`Soort` = \'PvB\'
									and leden.`VolledigeNaam` = \''.$_POST["bewaker"].'\'
									OR beheer.`Soort` = \'Label\'
									and leden.`VolledigeNaam` = \''.$_POST["bewaker"].'\'
									ORDER BY
									beheer.`Volgordenr`';
		
		$database->query($tableQuery);	
		$tableResult = $database->resultset();
		}
	}else{
		// Geen kaart om in te laden omdat er geen week is.
		echo 'Je hebt helaas geen kaart om in te laden! W';
	}
	
// We gaan nu onze array uitpakken door een variabele te maken voor elke waarde in de array.
// De eerste regel van de array krijgt een eigen prefix. Deze gaan we later matchen met de benodigde gegevens in de kaart.

	//Bewaker array
		if ($tableResult[0] != '') {
			extract($tableResult[0], EXTR_PREFIX_ALL, "Bewaker");
		}
	
	//Bewaker
	if ($Bewaker_Relatienr != '') {	
		$NAAM_Bewaker = $Bewaker_VolledigeNaam;
		$PVB1_Bewaker = $Bewaker_CompetentiesRegel1;
		$PVB2_Bewaker = $Bewaker_CompetentiesRegel2;
	}
	
	if ($_POST["week"] === date("d-m-Y")) {	
		$Bewakingsweek = date("d-m-Y");
	}else{
		$Bewakingsweek = substr($_POST["week"],-6, 4).'-'.substr($_POST["week"],-2);	
	}
	
    $array= [];
    $Columns = [];


    $Column1 = (object) ["Column" => 1, "Name" => "C1"];
    array_push($Columns, $Column1);

    $Column2 = (object) ["Column" => 2, "Name" => "C2"];
    array_push($Columns, $Column2);

    $Column3 = (object) ["Column" => 3, "Name" => "C3"];
    array_push($Columns, $Column3);
	
	$Column4 = (object) ["Column" => 4, "Name" => "C4"];
    array_push($Columns, $Column4);
	
	$Column5 = (object) ["Column" => 5, "Name" => "C5"];
    array_push($Columns, $Column5);
	
	$Column6 = (object) ["Column" => 6, "Name" => "C6"];
    array_push($Columns, $Column6);
	
	$Column7 = (object) ["Column" => 7, "Name" => "C7"];
    array_push($Columns, $Column7);
	
	$Column8 = (object) ["Column" => 8, "Name" => "C8"];
    array_push($Columns, $Column8);
	
	$Column9 = (object) ["Column" => 9, "Name" => "C9"];
    array_push($Columns, $Column9);
	
	$Column10 = (object) ["Column" => 10, "Name" => "C10"];
    array_push($Columns, $Column10);
	
	//$Column11 = (object) ["Column" => 11, "Name" => "C11"];
    //array_push($Columns, $Column11);

    $Row1 = (object) ['Row' => 1, 'Name' => 'R1', 'Columns' => $Columns];
    $Row2 = (object) ['Row' => 2, 'Name' => 'R2', 'Columns' => $Columns];
    $Row3 = (object) ['Row' => 3, 'Name' => 'R3', 'Columns' => $Columns];
	$Row4 = (object) ['Row' => 4, 'Name' => 'R4', 'Columns' => $Columns];
	$Row5 = (object) ['Row' => 5, 'Name' => 'R5', 'Columns' => $Columns];
	$Row6 = (object) ['Row' => 6, 'Name' => 'R6', 'Columns' => $Columns];
	$Row7 = (object) ['Row' => 7, 'Name' => 'R7', 'Columns' => $Columns];
	$Row8 = (object) ['Row' => 8, 'Name' => 'R8', 'Columns' => $Columns];
	$Row9 = (object) ['Row' => 9, 'Name' => 'R9', 'Columns' => $Columns];
	$Row10 = (object) ['Row' => 10, 'Name' => 'R10', 'Columns' => $Columns];
	$Row11 = (object) ['Row' => 11, 'Name' => 'R11', 'Columns' => $Columns];
	
    array_push($array, $Row1);
    array_push($array, $Row2);
	array_push($array, $Row3);
	array_push($array, $Row4);
	array_push($array, $Row5);
	array_push($array, $Row6);
	array_push($array, $Row7);
	array_push($array, $Row8);
	array_push($array, $Row9);
	array_push($array, $Row10);
	array_push($array, $Row11);
	
	foreach ( $tableResult as $competentie ) {
		${'R'.$competentie[Row].'C'.$competentie[Column]} = $competentie[Label].'</br><span style="font-size:11px;font-weight:bold;color:#5f5f5f">'.$competentie[Diploma].'</span></br>'.$competentie[Status];
	}
	
?>

<!--<html>-->
<!--<body>-->
		<!--					 
		Afdruk instellingen;
							 - Rooster, 1e pagina liggend.
							 - Kaart, staand.
							 - Kleur.
							 - A4, 1 blad per pagina.
							 - Marges standaard
							 - Schaal 80%
							 - Achtergrond afbeeldingen: Ja
							 
		Gebruikte font size;
							 - Normaal, 11pt = 15px = 95%
							 - Klein,    8pt = 11px = 70%
		
		Selectie criteria:
		- Lid mag niets selecteren maar ziet zijn eigen kaart.
		- Lid welke dit jaar ASC/OPL is mag de week selecteren waarin hij/zij ASC/OPL is en daarna de bewakers in die week.
		- Kaderleden mogen initieel alle weken kiezen of alle bewakers. Kiezen zijn een week, dan kunnen ze daarna enkel de bewakers in die week selecteren.
		-->
		<table class="tg">
			<tr>
				<th class="tg-grijzebalk">Competentiekaart <?php echo $NAAM_Bewaker; ?></th>
			</tr>
			<tr>
				<td class="tg-logobalk" rowspan="4"><img src="../../../images/logo-custom.svg" alt="TRB Logo"></td>
			</tr>
			<tr>
			</tr>
			<tr>
			</tr>			
			<tr>
			</tr>
			<tr>
				<td class="tg-grijzebalk"><?php echo $Bewakingsweek.' Texel'; ?></td>
			</tr>
			<tr>
				<td class="tg-afkortingenbalk"><span style="font-weight:bold">Niveau/functies: </span><?php echo $PVB1_Bewaker; ?></td>
			</tr>
			<tr>
				<td class="tg-afkortingenbalk"><span style="font-weight:bold">Diploma's: </span><?php echo $PVB2_Bewaker; ?></td>
			</tr>
		</table>	
		<table class="tg">
			<?php foreach ($array as $row) { ?>
			<tr>
				<?php foreach ($row->Columns as $column) { ?>
				<td class="<?php if(substr(${$row->Name . $column->Name},-7) === 'Behaald'){echo 'tg-competentie-bzv';}elseif(is_numeric(substr(${$row->Name . $column->Name},-2))){echo 'tg-competentie-bmv';}else{echo 'tg-competentie';} ?>"><?php echo ${$row->Name . $column->Name};?></td>
				<?php }; ?>
			</tr>
			<?php }; ?>
		</table>	
		<table class="tg">
			<tr>
				<td class="tg-footer" colspan="2">P1.4 - Aangetoont met:<br><span style="font-size:11px;font-weight:bold;color:#5f5f5f">Diploma van: OK | RK3 | RK4 | LPEV | FAI | MFA | OK-Inst.<br>Diploma nr.:<br>Geldig van/tot:</span></td>
				<td class="tg-footer"><span style="font-size:11px;font-weight:bold;color:#5f5f5f">400m zwemmen:<br><br>Run Swim Run:<br></span></td>
				<td class="tg-footer"><span style="font-size:11px;font-weight:bold">Door deze kaart te ondertekenen<br>geef je aan jezelf competent te<br>achten voor de vermelde<br>competenties.</span></td>
				<td class="tg-footersig"><div>Handtekening <?php echo $NAAM_Bewaker; ?> (<?php echo $Bewaker_Relatienr; ?>) </div></td>
			</tr>
		</table>
	</body>
</html>