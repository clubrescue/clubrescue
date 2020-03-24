<?php
	// Include database class
	require_once 'util/utility.class.php';	
	require_once 'util/database.class.php';

	if(!isset($user)){
		header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
		require_once("wp-authenticate.php");
		login();
		$user = wp_get_current_user();
	}

	if($user->ID != 0){

		// Kader = contributor
		// Bestuur = editor

$sqlOptions = 'SELECT `CFAdmEntryNo` FROM `cr_options` WHERE `ID` = 1';
$sqlLeden = 'SELECT `Relatienr`, `VolledigeNaam`, `Achternaam`, `Tussenvoegsels`, `Voorletters`, `Roepnaam`, `Geslacht`, DAY(`GeboorteDatum`) as \'GeboorteDag\', MONTH(`GeboorteDatum`) as \'GeboorteMaand\', YEAR(`GeboorteDatum`) as \'GeboorteJaar\', `Geboorteplaats`, `Geboorteland`, `Nationaliteit`, `Legitimatietype`, `Legitimatienr`, `VolledigAdres`, `Straat`, `Huisnr`, `HuisnrToev`, `Postcode`, `Woonplaats`, `Land`, `Telefoon`, `Mobiel`, `Email`, `BankrekType`, `BankrekNr`, `BIC`, `Dieet`, `Noodcontact`, `Machtigingskenmerk`, `MachtigingOndertekend`, `Verenigingsfunctie` FROM `cr_leden` WHERE `Soort` = \'Bondslid\' and `Relatienr` = \''.substr($user->user_login, 0, 7).'\'';
$sqlVerenigingsdiplomas = 'SELECT DAY(`EindDatum`) as \'EindDag\', MONTH(`EindDatum`) as \'EindMaand\', YEAR(`EindDatum`) as \'EindJaar\', `Opmerkingen` FROM `cr_diplomas` WHERE `Diploma` = \'Levensreddende Handelingen\' and `Relatienr` = \''.$user->user_login.'\'';

		$databaseOptions = new Database();
		$databaseOptions->query($sqlOptions);	
		$CFAdmEntryNo = $databaseOptions->resultset();
		$CurrentCFAdmEntryNo = ($CFAdmEntryNo[0]['CFAdmEntryNo']);
		$NewCFAdmEntryNo = ($CurrentCFAdmEntryNo + 1);
		
		$databaseLeden = new Database();
		$databaseLeden->query($sqlLeden);	
		$result = $databaseLeden->resultset();
		
		$databaseVerenigingsdiplomas = new Database();
		$databaseVerenigingsdiplomas->query($sqlVerenigingsdiplomas);	
		$verenigingsdiplomas = $databaseVerenigingsdiplomas->resultset();
				
		$sqlBondsdiplomasVOG = 'SELECT `Relatienr`, `Type`, `Diploma`, `Soort`, DAY(`IngangsDatum`) as \'IngangsDag\', MONTH(`IngangsDatum`) as \'IngangsMaand\', YEAR(`IngangsDatum`) as \'IngangsJaar\', `Opmerkingen` FROM `cr_diplomas` WHERE `Diploma` = \'Verklaring Omtrent Gedrag\' and `Relatienr` = \''.$user->user_login.'\'';
		$sqlBondsdiplomasRIJ = 'SELECT `Relatienr`, `Type`, `Diploma`, `Soort`, DAY(`IngangsDatum`) as \'IngangsDag\', MONTH(`IngangsDatum`) as \'IngangsMaand\', YEAR(`IngangsDatum`) as \'IngangsJaar\', `Opmerkingen` FROM `cr_diplomas` WHERE `Diploma` = \'Rijbewijs\' and `Relatienr` = \''.$user->user_login.'\'';
		$sqlBondsdiplomasVK1 = 'SELECT `Relatienr`, `Type`, `Diploma`, `Soort`, DAY(`IngangsDatum`) as \'IngangsDag\', MONTH(`IngangsDatum`) as \'IngangsMaand\', YEAR(`IngangsDatum`) as \'IngangsJaar\', `Opmerkingen` FROM `cr_diplomas` WHERE `Diploma` = \'Klein Vaarbewijs I\' and `Relatienr` = \''.$user->user_login.'\'';
		$sqlBondsdiplomasVK2 = 'SELECT `Relatienr`, `Type`, `Diploma`, `Soort`, DAY(`IngangsDatum`) as \'IngangsDag\', MONTH(`IngangsDatum`) as \'IngangsMaand\', YEAR(`IngangsDatum`) as \'IngangsJaar\', `Opmerkingen` FROM `cr_diplomas` WHERE `Diploma` = \'Klein Vaarbewijs II\' and `Relatienr` = \''.$user->user_login.'\'';
		$sqlBondsdiplomasBCM = 'SELECT `Relatienr`, `Type`, `Diploma`, `Soort`, DAY(`IngangsDatum`) as \'IngangsDag\', MONTH(`IngangsDatum`) as \'IngangsMaand\', YEAR(`IngangsDatum`) as \'IngangsJaar\', `Opmerkingen` FROM `cr_diplomas` WHERE `Diploma` = \'Basiscertificaat Marifonie\' and `Relatienr` = \''.$user->user_login.'\'';
		$sqlBondsdiplomasVVW = 'SELECT `Relatienr`, `Type`, `Diploma`, `Soort`, DAY(`IngangsDatum`) as \'IngangsDag\', MONTH(`IngangsDatum`) as \'IngangsMaand\', YEAR(`IngangsDatum`) as \'IngangsJaar\', `Opmerkingen` FROM `cr_diplomas` WHERE `Diploma` = \'Varend redden Voor Waterscooter\' and `Relatienr` = \''.$user->user_login.'\'';
		$sqlBondsdiplomasVEH = 'SELECT `Relatienr`, `Type`, `Diploma`, `Soort`, DAY(`IngangsDatum`) as \'IngangsDag\', MONTH(`IngangsDatum`) as \'IngangsMaand\', YEAR(`IngangsDatum`) as \'IngangsJaar\', `Opmerkingen` FROM `cr_diplomas` WHERE `Diploma` = '.$verenigingsdiplomas[0]['Opmerkingen'].' and `Relatienr` = \''.$user->user_login.'\'';

		$databaseBondsdiplomasVOG = new Database();
		$databaseBondsdiplomasVOG->query($sqlBondsdiplomasVOG);	
		$bondsdiplomasVOG = $databaseBondsdiplomasVOG->resultset();
		
		$databaseBondsdiplomasRIJ = new Database();
		$databaseBondsdiplomasRIJ->query($sqlBondsdiplomasRIJ);	
		$bondsdiplomasRIJ = $databaseBondsdiplomasRIJ->resultset();

		$databaseBondsdiplomasVK1 = new Database();
		$databaseBondsdiplomasVK1->query($sqlBondsdiplomasVK1);	
		$bondsdiplomasVK1 = $databaseBondsdiplomasVK1->resultset();

		$databaseBondsdiplomasVK2 = new Database();
		$databaseBondsdiplomasVK2->query($sqlBondsdiplomasVK2);	
		$bondsdiplomasVK2 = $databaseBondsdiplomasVK2->resultset();

		$databaseBondsdiplomasBCM = new Database();
		$databaseBondsdiplomasBCM->query($sqlBondsdiplomasBCM);	
		$bondsdiplomasBCM = $databaseBondsdiplomasBCM->resultset();

		$databaseBondsdiplomasVVW = new Database();
		$databaseBondsdiplomasVVW->query($sqlBondsdiplomasVVW);	
		$bondsdiplomasVVW = $databaseBondsdiplomasVVW->resultset();

		$databaseBondsdiplomasVEH = new Database();
		$databaseBondsdiplomasVEH->query($sqlBondsdiplomasVEH);	
		$bondsdiplomasVEH = $databaseBondsdiplomasVEH->resultset();		

		$sqlOptions = 'SELECT `VerenigingsNaam`, `Straat`, `Huisnr`, `HuisnrToev`, `Postcode`, `Plaats`, `Land`, `IncassantID` FROM `cr_options` WHERE `Actief` = \'1\'';
		
		$databaseOptions = new Database();
		$databaseOptions->query($sqlOptions);	
		$options = $databaseOptions->resultset();

		$acties_wijzien = '<div id="wijzigen">';
		$acties_strandbewaking = '<div id="strandbewaking">';
		$acties_kader = '<div id="kader">';

		// Categorie Aanmelden - Toegang iedereen als direct link
		$acties_aanmelden = '"https://trb.nu/clubforms/view.php?id=16834"';
				
		// Categorie Gegevens wijzigen - Toegang leden
		$acties_wijzien .= '<br><a href="https://trb.nu/clubforms/view.php?id=18877&element_72='.$result[0]['Relatienr'].'&element_7='.$result[0]['Voorletters'].'&element_8='.$result[0]['Roepnaam'].'&element_9='.$result[0]['Tussenvoegsels'].'&element_10='.$result[0]['Achternaam'].'&element_12_1='.$result[0]['GeboorteDag'].'&element_12_2='.$result[0]['GeboorteMaand'].'&element_12_3='.$result[0]['GeboorteJaar'].'&element_76='.$result[0]['Geslacht'].'&element_14='.$result[0]['Geboorteplaats'].'&element_15='.$result[0]['Geboorteland'].'&element_16='.$result[0]['Nationaliteit'].'&element_11_1='.$result[0]['Straat'].' '.$result[0]['Huisnr'].' '.$result[0]['HuisnrToev'].'&element_11_5='.$result[0]['Postcode'].'&element_11_3='.$result[0]['Woonplaats'].'&element_11_6=Netherlands&element_18='.$result[0]['Email'].'&element_70='.$result[0]['Mobiel'].'&element_71='.$result[0]['Telefoon'].'"target="_blank">Persoonlijke gegevens - ledenadministratie</a>';
		$acties_wijzien .= '<br><a href="https://trb.nu/clubforms/view.php?id=19539&element_72='.$result[0]['Relatienr'].'&element_8='.$result[0]['VolledigeNaam'].'&element_28='.$result[0]['Dieet'].'&element_29='.$result[0]['Noodcontact'].'&element_23='.$result[0]['Legitimatietype'].'&element_24='.$result[0]['Legitimatienr'].'" target="_blank">Persoonlijke gegevens - strandbewaking (Dieet, noodcontacten en legitimatie)</a>';
		$acties_wijzien .= '<br><a href="https://trb.nu/clubforms/view.php?id=15127&element_19=2&element_6=TRBDM'.date("Y").date("m").date("d").$result[0]['Relatienr'].'&element_11='.$result[0]['Voorletters'].'&element_12_1='.$result[0]['Roepnaam'].'&element_12_2='.$result[0]['Tussenvoegsels'].'&element_12_3='.$result[0]['Achternaam'].'&element_13_1='.$result[0]['Straat'].' '.$result[0]['Huisnr'].' '.$result[0]['HuisnrToev'].'&element_13_5='.$result[0]['Postcode'].'&element_13_3='.$result[0]['Woonplaats'].'&element_13_6=Netherlands"target="_blank">Bankrekening nummer (inclusief afgeven digitale machtiging)</a>';
		// Check of de juiste SEPA machtiging prefix reeds in gebruik is
		$MKLegacyPrefix = 'D267XNS';
		$MKUserPrefix = $result[0]['Machtigingskenmerk'];
		$MKLegacyPrefixCheck = substr($MKUserPrefix,0,7);
		if ($MKLegacyPrefix === $MKLegacyPrefixCheck) {
			$acties_wijzien .= '<br><a href="https://trb.nu/clubforms/view.php?id=15127&element_19=3&element_6=TRBDM'.date("Y").date("m").date("d").$result[0]['Relatienr'].'&element_11='.$result[0]['Voorletters'].'&element_12_1='.$result[0]['Roepnaam'].'&element_12_2='.$result[0]['Tussenvoegsels'].'&element_12_3='.$result[0]['Achternaam'].'&element_13_1='.$result[0]['Straat'].' '.$result[0]['Huisnr'].' '.$result[0]['HuisnrToev'].'&element_13_5='.$result[0]['Postcode'].'&element_13_3='.$result[0]['Woonplaats'].'&element_13_6=Netherlands&element_14='.$result[0]['BankrekNr'].'&element_15='.$result[0]['BIC'].'"target="_blank">Afgeven (nieuwe) digitale machtiging</a>';
			} else {
			$acties_wijzien .= '<br><a href="https://trb.nu/clubforms/view.php?id=15127&element_19=3&element_6='.$result[0]['Machtigingskenmerk'].'&element_11='.$result[0]['Voorletters'].'&element_12_1='.$result[0]['Roepnaam'].'&element_12_2='.$result[0]['Tussenvoegsels'].'&element_12_3='.$result[0]['Achternaam'].'&element_13_1='.$result[0]['Straat'].' '.$result[0]['Huisnr'].' '.$result[0]['HuisnrToev'].'&element_13_5='.$result[0]['Postcode'].'&element_13_3='.$result[0]['Woonplaats'].'&element_13_6=Netherlands&element_14='.$result[0]['BankrekNr'].'&element_15='.$result[0]['BIC'].'"target="_blank">Bestaande machtiging (opnieuw) digitaal ondertekenen</a>';
		}
		$acties_wijzien .= '<br><a href="https://trb.nu/clubforms/view.php?id=18290&element_67='.$result[0]['Relatienr'].'&element_68='.$result[0]['VolledigeNaam'].'" target="_blank">Verenigingsdiplomas (Verlenging verklaring Eerste Hulp)</a>';
	//	$acties_wijzien .= '<br>Verenigingsdiploma′s - verlenging verklaring Eerste Hulp (EHBO diploma/Levensreddende Handelingen) doorgeven (Momenteel niet beschikbaar)';
		// LINK ERROR: Waarde van verklaring eerste hulp uit bondsdiploma's word niet gelezen.
		// $acties_wijzien .= '<br><a href="https://trb.nu/clubforms/view.php?id=18290&element_67='.$result[0]['Relatienr'].'&TypeverlengingVEH='.$verenigingsdiplomas[0]['Opmerkingen'].'&NRverlengingVEH='.$bondsdiplomasVEH[0]['Opmerkingen'].'&VEHbehaalduitgegevenOp[day]='.$bondsdiplomasVEH[0]['IngangsDag'].'&VEHbehaalduitgegevenOp[month]='.$bondsdiplomasVEH[0]['IngangsMaand'].'&VEHbehaalduitgegevenOp[year]='.$bondsdiplomasVEH[0]['IngangsJaar'].'"target="_blank">Verenigingsdiploma′s - verlenging verklaring Eerste Hulp (EHBO diploma/Levensreddende Handelingen) doorgeven</a>';	
		$acties_wijzien .= '<br><a href="https://trb.nu/clubforms/view.php?id=17811&element_66='.$result[0]['Relatienr'].'&element_67='.$result[0]['VolledigeNaam'].'" target="_blank">Bondsdiplomas (Toevoegen (nieuwe) verklaring Eerste Hulp, VOG, Klein Vaarbewijs, Marifonie)</a>';
		//	$acties_wijzien .= '<br>Bondsdiploma′s en vaardigheids bewijzen van derde (Momenteel niet beschikbaar)';
		// Bondsdiploma's - Toevoegen/wijzigen bondsdiploma’s, en vaardigheids bewijzen van derde

			//&nrVOG='.$BondsdiplomasVOG[0]['Opmerkingen'].'
			//&ingangsdatumVOG[day]='.$BondsdiplomasVOG[0]['IngangsDag'].'&ingangsdatumVOG[month]='.$BondsdiplomasVOG[0]['IngangsMaand'].'&ingangsdatumVOG[year]='.$BondsdiplomasVOG[0]['IngangsJaar'].'
			//&rijbewijsCategorien=*)
			//&nrRIJ='.$BondsdiplomasRIJ[0]['Opmerkingen'].'
			//&ingangsdatumRIJCatB[day]='.$BondsdiplomasRIJ[0]['IngangsDag'].'&ingangsdatumRIJ[month]='.$BondsdiplomasRIJ[0]['IngangsMaand'].'&ingangsdatumRIJ[year]='.$BondsdiplomasRIJ[0]['IngangsJaar'].'
			//&ingangsdatumRIJCatT[day]='.$BondsdiplomasRIJ[0]['IngangsDag'].'&ingangsdatumRIJ[month]='.$BondsdiplomasRIJ[0]['IngangsMaand'].'&ingangsdatumRIJ[year]='.$BondsdiplomasRIJ[0]['IngangsJaar'].'
			//&nrVK1='.$BondsdiplomasVK1[0]['Opmerkingen'].'
			//&ingangsdatumVK1[day]='.$BondsdiplomasVK1[0]['IngangsDag'].'&ingangsdatumVK1[month]='.$BondsdiplomasVK1[0]['IngangsMaand'].'&ingangsdatumVK1[year]='.$BondsdiplomasVK1[0]['IngangsJaar'].'
			//&nrVK2='.$BondsdiplomasVK2[0]['Opmerkingen'].'
			//&ingangsdatumVK2[day]='.$BondsdiplomasVK2[0]['IngangsDag'].'&ingangsdatumVK2[month]='.$BondsdiplomasVK2[0]['IngangsMaand'].'&ingangsdatumVK2[year]='.$BondsdiplomasVK2[0]['IngangsJaar'].'
			//&nrBCM='.$BondsdiplomasBCM[0]['Opmerkingen'].'
			//&ingangsdatumBCM[day]='.$BondsdiplomasBCM[0]['IngangsDag'].'&ingangsdatumBCM[month]='.$BondsdiplomasBCM[0]['IngangsMaand'].'&ingangsdatumBCM[year]='.$BondsdiplomasBCM[0]['IngangsJaar'].'
			//&nrVVW='.$BondsdiplomasVVW[0]['Opmerkingen'].'
			//&ingangsdatumVVW[day]='.$BondsdiplomasVVW[0]['IngangsDag'].'&ingangsdatumVVW[month]='.$BondsdiplomasVVW[0]['IngangsMaand'].'&ingangsdatumVVW[year]='.$BondsdiplomasVVW[0]['IngangsJaar'].'
			//&typeVEH='.$verenigingsdiplomas[0]['Opmerkingen'].'
			//&nrVEH='.$BondsdiplomasVEH[0]['Opmerkingen'].'
			//&ingangsdatumVEH[day]='.$BondsdiplomasVEH[0]['IngangsDag'].'&ingangsdatumVEH[month]='.$BondsdiplomasVEH[0]['IngangsMaand'].'&ingangsdatumVEH[year]='.$BondsdiplomasVEH[0]['IngangsJaar'].'
			//&einddatumVEH[day]='.$verenigingsdiplomas[0]['EindDag'].'&einddatumVEH[month]='.$verenigingsdiplomas[0]['EindMaand'].'&einddatumVEH[year]='.$verenigingsdiplomas[0]['EindJaar'].'
				
			//*)Als iemand al een bewijs heeft geef deze optie mee als prefill;
			//Heeft B en T:
			//Rijbewijs B uitgegeven vóór 1 juli 2015 (is incl. T)
			//Rijbewijs B én T uitgegeven ná 1 juli 2015
			//Heeft alleen B:
			//Rijbewijs B uitgegeven ná 1 juli 2015 zonder T
				
			//&diplomas= [check diploma's in bezit?]
			  //Verklaring Omtrent Gedrag
			  //Rijbewijs
			  //Klein Vaarbewijs I
			  //Klein Vaarbewijs II
			  //Basiscertificaat Marifonie
			  //Varend redden voor waterscooter (van Reddingsbrigade Nederland)
			  //Verklaring Eerste Hulp (EHBO diploma of BIG registratie)	
			
			//"target="_blank">Bondsdiploma′s en vaardigheids bewijzen van derde</a>';

		$acties_wijzien .= '<br><a href="'.site_url().'/wp-admin/profile.php">Wachtwoord</a>';
		$acties_wijzien .= '<br><a href="' . wp_logout_url( home_url() ).'">Uitloggen</a>';
		$acties_wijzien .= '<br><br><a href="https://trb.nu/clubforms/view.php?id=16147&element_2='.$result[0]['Relatienr'].'&element_3='.$result[0]['VolledigeNaam'].'"target="_blank">Opzeggen lidmaatschap</a>';
		
		// Categorie Strandbewaking - Toegang leden of ASC/809
		$acties_strandbewaking .= '<br><a href="https://trb.nu/clubforms/view.php?id=36352&element_3='.$result[0]['Relatienr'].'&element_4='.$result[0]['VolledigeNaam'].'&element_26='.$result[0]['Email'].'&element_37='.$result[0]['Geslacht'].'&element_36_1='.$result[0]['GeboorteDag'].'&element_36_2='.$result[0]['GeboorteMaand'].'&element_36_3='.$result[0]['GeboorteJaar'].'" target="_blank">Inschrijven strandbewaking</a>';
		$acties_strandbewaking .= '<br><a href="https://trb.nu/clubforms/view.php?id=34671&element_2='.$result[0]['Relatienr'].'&element_3='.$result[0]['VolledigeNaam'].'&element_4='.$result[0]['Email'].'&element_5_1='.$result[0]['GeboorteDag'].'&element_5_2='.$result[0]['GeboorteMaand'].'&element_5_3='.$result[0]['GeboorteJaar'].'&element_13='.$result[0]['Geslacht'].'" target="_blank">Gezondheidsverklaring</a>';
		$acties_strandbewaking .= '<br><a href="https://trb.nu/clubforms/view.php?id=36878" target="_blank">Evaluatie strandbewaking</a>';		
		// Kader of ASC/809 in de week van het huidige seizoen.
// ----- huidige optie zonder ASC/809 check op urls
//		if ( current_user_can('contributor') || current_user_can('editor') || current_user_can('administrator') ) {
//			// Voor Club.Redders hebben ook 809's en ASC's van dit seizoen toegang nodig.
//			$acties_strandbewaking .= '<br><a href="https://trb.nu/clubredders" target="_blank">Club.Redders</a>';
//			$acties_strandbewaking .= '<br><a href="https://trb.nu/wp-content/uploads/documenten/procesflow.pdf" target="_blank">Werkinstructies</a>';
//		}
// ----- nieuwe opzet met check op ASC/809 op urls
			$where1 = 'BWK'.Date("Y").'%';
			$where2 = 'EXAMEN'.Date("Y").'%';
			$sqlActiviteiten = "SELECT count(distinct `Activiteit`) as count FROM  `cr_activiteiten` WHERE  `Relatienr` =  '$user->user_login' AND  `Locatie` IN ('OPL',  'ASC') AND (`Activiteit` LIKE '$where1' or `Activiteit` LIKE '$where2')";			
			$databaseActiviteiten = new Database();
			$databaseActiviteiten->query($sqlActiviteiten);	
			$activiteiten = $databaseActiviteiten->resultset();	
		
		if ( current_user_can('contributor') || current_user_can('editor') || current_user_can('administrator')) {	
			$acties_strandbewaking .= '<br><a href="https://trb.nu/clubredders" target="_blank">Club.Redders</a>';
			$acties_strandbewaking .= '<br><a href="https://trb.nu/clubforms/index.php" target="_blank">Club.Forms</a>';
			$acties_strandbewaking .= '<br><a href="https://trb.nu/wp-content/uploads/documenten/procesflow.pdf" target="_blank">Werkinstructies</a>';
		}elseif ( current_user_can('subscriber') &&  $activiteiten[0]['count'] > 0  ) {
			$acties_strandbewaking .= '<br><a href="https://trb.nu/clubredders" target="_blank">Club.Redders</a>';
		}
// ----- einde optie commit sets
		// Categorie Kader acties - Toegang kader of bestuur of admins
		// Kader acties
		if ( current_user_can('contributor') || current_user_can('editor') || current_user_can('administrator') ) {
			$acties_kader .= '<br><a href="https://trb.nu/clubforms/view.php?id=27192&element_1='.$result[0]['Relatienr'].'&element_2='.$result[0]['VolledigeNaam'].'&element_3='.$result[0]['Email'].'&element_4='.$result[0]['BankrekNr'].'&element_67='.$result[0]['Verenigingsfunctie'].'&element_57=1&element_36=D.'.date("Y.m.d").'.'.$result[0]['Relatienr'].'.'.$NewCFAdmEntryNo.'"target="_self">Declaratie inkopen</a>';
			$acties_kader .= '<br><a href="https://trb.nu/clubforms/view.php?id=27192&element_1='.$result[0]['Relatienr'].'&element_2='.$result[0]['VolledigeNaam'].'&element_3='.$result[0]['Email'].'&element_4='.$result[0]['BankrekNr'].'&element_67='.$result[0]['Verenigingsfunctie'].'&element_57=2&element_36=D.'.date("Y.m.d").'.'.$result[0]['Relatienr'].'.'.$NewCFAdmEntryNo.'"target="_self">Declaratie reiskosten</a>';
			$acties_kader .= '<br><a href="https://trb.nu/clubforms/view.php?id=27192&element_1='.$result[0]['Relatienr'].'&element_2='.$result[0]['VolledigeNaam'].'&element_3='.$result[0]['Email'].'&element_4='.$result[0]['BankrekNr'].'&element_67='.$result[0]['Verenigingsfunctie'].'&element_57=3&element_36=D.'.date("Y.m.d").'.'.$result[0]['Relatienr'].'.'.$NewCFAdmEntryNo.'"target="_self">Declaratie overtochten</a>';
			}

		// Bestuur acties
		if(current_user_can('editor') || current_user_can('administrator') ) {
			$acties_kader .= '<br><a href="https://trb.nu/clubforms/view.php?id=27192&element_1='.$result[0]['Relatienr'].'&element_2='.$result[0]['VolledigeNaam'].'&element_3='.$result[0]['Email'].'&element_67='.$result[0]['Verenigingsfunctie'].'&element_4=NL71RABO0113448716&element_57=4&element_36=F.'.date("Y.m.d").'.DEBITEURNR.FACTUURNR.'.$NewCFAdmEntryNo.'"target="_self">Registratie factuur</a>';
			$acties_kader .= '<br><a href="https://trb.nu/clubforms/view.php?id=27192&element_1='.$result[0]['Relatienr'].'&element_2='.$result[0]['VolledigeNaam'].'&element_3='.$result[0]['Email'].'&element_67='.$result[0]['Verenigingsfunctie'].'&element_4=NL71RABO0113448716&element_57=5&element_36=P.'.date("Y.m.d").'.'.$result[0]['Relatienr'].'.'.$NewCFAdmEntryNo.'"target="_self">Registratie pinbon</a>';
			} // Kader acties --> worden bestuur acties zodra competenties over zijn naar Club.Redders
		if ( current_user_can('contributor') || current_user_can('editor') || current_user_can('administrator') ) {
			$acties_kader .= '<br><a href="https://trb.nu/clubforms/view.php?id=27192&element_1='.$result[0]['Relatienr'].'&element_2='.$result[0]['VolledigeNaam'].'&element_3='.$result[0]['Email'].'&element_67='.$result[0]['Verenigingsfunctie'].'&element_4=NL71RABO0113448716&element_57=6&element_36=K.'.date("Y.m.d").'.D267XNS.'.$NewCFAdmEntryNo.'"target="_self">Registratie kashandeling</a>';
			$acties_kader .= '<br><br><a href="https://outlook.office365.com/owa/?realm=trb.nu" target="_blank">Club.Mail</a>';
			$acties_kader .= '<br><a href="https://texelsereddingsbrigade-my.sharepoint.com/_layouts/15/MySite.aspx?MySiteRedirect=AllDocuments" target="_blank">Club.Drive</a>';
			$acties_kader .= '<br><a href="https://teams.microsoft.com/" target="_blank">Club.Teams</a>';
			$acties_kader .= '<br><a href="https://lastpass.com/?ac=1" target="_blank">Club.Pass</a>';
			$acties_kader .= '<br><a href="https://trb.nu/keeweb" target="_blank">Club.Keys</a>';
			$acties_kader .= '<br><a href="https://club.sportlink.com/apps/club-production/redned.jnlp" target="_blank">Sportlink.Club</a>';
			$acties_kader .= '<br><br><a href="https://support.sportlink.nl/support/solutions" target="_blank">Sportlink Support</a>';
			$acties_kader .= '<br><a href="https://trb.nu/clubredders/support.php" target="_blank">Club.Redders Support</a>';
			$acties_kader .= '<br><a href="https://trb.nu/clubforms/view.php?id=22542&element_2='.$result[0]['Relatienr'].'&element_3='.$result[0]['VolledigeNaam'].'&element_15='.$result[0]['Email'].'&element_6='.$result[0]['Verenigingsfunctie'].'&element_4='.$result[0]['Woonplaats'].'"target="_blank">Vrijwilligersovereenkomst</a>';
		}
		
		// Club.Redders administrator functies
		if(current_user_can( 'administrator' ) || $user->user_login === 'D271RRP') {
			$acties_kader .= '<br><a href="https://trb.nu/clubredders/log.php" target="_blank">Club.Redders Logging</a>';
		}

		// Algemene acties
		$acties_wijzien .= '</div>';
		$acties_strandbewaking .= '</div>';
		$acties_kader .= '</div>';
	} else {
		$acties_aanmelden = '"https://trb.nu/clubforms/view.php?id=16834"';
	}
?>
