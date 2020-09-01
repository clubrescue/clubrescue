<?php
    session_start();

    if (!isset($_SESSION['token'])) {
        $_SESSION['O365_REDIRECT'] = $_SERVER['REQUEST_URI'];
        include $_SERVER['DOCUMENT_ROOT'] . '/clubredders/auth.php';
    }
    
    require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/msgraph/user.class.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/utility.class.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/database.class.php';
    
    $user = new MSGraphUser($_SESSION['token']);
	//retrieve current user name
	$userNameRequest = $user->getUser();
	$userNameJSON = json_encode($userNameRequest);
	$userNameValue = json_decode($userNameJSON, true);
	$userName = strtok($userNameValue['userPrincipalName'], '@');

    //$welcome = 'MyCR Expenses';
    //include './views/index.view.php';

	if(isset($user)){

		$sqlOptions = 'SELECT `CFAdmEntryNo` FROM `cr_options` WHERE `ID` = 1';
		$sqlLeden = 'SELECT `Relatienr`, `VolledigeNaam`, `Achternaam`, `Tussenvoegsels`, `Voorletters`, `Roepnaam`, `Geslacht`, DAY(`GeboorteDatum`) as \'GeboorteDag\', MONTH(`GeboorteDatum`) as \'GeboorteMaand\', YEAR(`GeboorteDatum`) as \'GeboorteJaar\', `Geboorteplaats`, `Geboorteland`, `Nationaliteit`, `Legitimatietype`, `Legitimatienr`, `VolledigAdres`, `Straat`, `Huisnr`, `HuisnrToev`, `Postcode`, `Woonplaats`, `Land`, `Telefoon`, `Mobiel`, `Email`, `BankrekType`, `BankrekNr`, `BIC`, `Dieet`, `Noodcontact`, `Machtigingskenmerk`, `MachtigingOndertekend`, `Verenigingsfunctie` FROM `cr_leden` WHERE `Soort` = \'Bondslid\' and `Relatienr` = \''.$userName.'\'';
		$sqlVerenigingsdiplomas = 'SELECT DAY(`EindDatum`) as \'EindDag\', MONTH(`EindDatum`) as \'EindMaand\', YEAR(`EindDatum`) as \'EindJaar\', `Opmerkingen` FROM `cr_diplomas` WHERE `Diploma` = \'Levensreddende Handelingen\' and `Relatienr` = \''.$userName.'\'';

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
				
		$sqlBondsdiplomasVOG = 'SELECT `Relatienr`, `Type`, `Diploma`, `Soort`, DAY(`IngangsDatum`) as \'IngangsDag\', MONTH(`IngangsDatum`) as \'IngangsMaand\', YEAR(`IngangsDatum`) as \'IngangsJaar\', `Opmerkingen` FROM `cr_diplomas` WHERE `Diploma` = \'Verklaring Omtrent Gedrag\' and `Relatienr` = \''.$userName.'\'';
		$sqlBondsdiplomasRIJ = 'SELECT `Relatienr`, `Type`, `Diploma`, `Soort`, DAY(`IngangsDatum`) as \'IngangsDag\', MONTH(`IngangsDatum`) as \'IngangsMaand\', YEAR(`IngangsDatum`) as \'IngangsJaar\', `Opmerkingen` FROM `cr_diplomas` WHERE `Diploma` = \'Rijbewijs\' and `Relatienr` = \''.$userName.'\'';
		$sqlBondsdiplomasVK1 = 'SELECT `Relatienr`, `Type`, `Diploma`, `Soort`, DAY(`IngangsDatum`) as \'IngangsDag\', MONTH(`IngangsDatum`) as \'IngangsMaand\', YEAR(`IngangsDatum`) as \'IngangsJaar\', `Opmerkingen` FROM `cr_diplomas` WHERE `Diploma` = \'Klein Vaarbewijs I\' and `Relatienr` = \''.$userName.'\'';
		$sqlBondsdiplomasVK2 = 'SELECT `Relatienr`, `Type`, `Diploma`, `Soort`, DAY(`IngangsDatum`) as \'IngangsDag\', MONTH(`IngangsDatum`) as \'IngangsMaand\', YEAR(`IngangsDatum`) as \'IngangsJaar\', `Opmerkingen` FROM `cr_diplomas` WHERE `Diploma` = \'Klein Vaarbewijs II\' and `Relatienr` = \''.$userName.'\'';
		$sqlBondsdiplomasBCM = 'SELECT `Relatienr`, `Type`, `Diploma`, `Soort`, DAY(`IngangsDatum`) as \'IngangsDag\', MONTH(`IngangsDatum`) as \'IngangsMaand\', YEAR(`IngangsDatum`) as \'IngangsJaar\', `Opmerkingen` FROM `cr_diplomas` WHERE `Diploma` = \'Basiscertificaat Marifonie\' and `Relatienr` = \''.$userName.'\'';
		$sqlBondsdiplomasVVW = 'SELECT `Relatienr`, `Type`, `Diploma`, `Soort`, DAY(`IngangsDatum`) as \'IngangsDag\', MONTH(`IngangsDatum`) as \'IngangsMaand\', YEAR(`IngangsDatum`) as \'IngangsJaar\', `Opmerkingen` FROM `cr_diplomas` WHERE `Diploma` = \'Varend redden Voor Waterscooter\' and `Relatienr` = \''.$userName.'\'';
		$sqlBondsdiplomasVEH = 'SELECT `Relatienr`, `Type`, `Diploma`, `Soort`, DAY(`IngangsDatum`) as \'IngangsDag\', MONTH(`IngangsDatum`) as \'IngangsMaand\', YEAR(`IngangsDatum`) as \'IngangsJaar\', `Opmerkingen` FROM `cr_diplomas` WHERE `Diploma` = '.$verenigingsdiplomas[0]['Opmerkingen'].' and `Relatienr` = \''.$userName.'\'';

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
		$acties_aanmelden = '"https://trb.nu/clubforms/view.php?id=38656"';
				
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
		$acties_wijzien .= '<br><a href="https://trb.nu/clubforms/view.php?id=17811&element_66='.$result[0]['Relatienr'].'&element_67='.$result[0]['VolledigeNaam'].'" target="_blank">Bondsdiplomas (Toevoegen (nieuwe) verklaring Eerste Hulp, VOG, Klein Vaarbewijs, Marifonie)</a>';
		$acties_wijzien .= '<br><a href="https://account.activedirectory.windowsazure.com/ChangePassword.aspx">Wachtwoord</a>';
		$acties_wijzien .= '<br><a href="https://www.office.com/estslogout?ru=%2f%3fref%3dlogout">Uitloggen</a>';
		$acties_wijzien .= '<br><br><a href="https://trb.nu/clubforms/view.php?id=16147&element_2='.$result[0]['Relatienr'].'&element_3='.$result[0]['VolledigeNaam'].'"target="_blank">Opzeggen lidmaatschap</a>';
		
		// Categorie Strandbewaking - Toegang leden of ASC/809
		$acties_strandbewaking .= '<br><a href="https://trb.nu/clubforms/view.php?id=36352&element_3='.$result[0]['Relatienr'].'&element_4='.$result[0]['VolledigeNaam'].'&element_26='.$result[0]['Email'].'&element_37='.$result[0]['Geslacht'].'&element_36_1='.$result[0]['GeboorteDag'].'&element_36_2='.$result[0]['GeboorteMaand'].'&element_36_3='.$result[0]['GeboorteJaar'].'" target="_blank">Inschrijven strandbewaking</a>';
		$acties_strandbewaking .= '<br><a href="https://trb.nu/clubforms/view.php?id=34671&element_2='.$result[0]['Relatienr'].'&element_3='.$result[0]['VolledigeNaam'].'&element_4='.$result[0]['Email'].'&element_5_1='.$result[0]['GeboorteDag'].'&element_5_2='.$result[0]['GeboorteMaand'].'&element_5_3='.$result[0]['GeboorteJaar'].'&element_13='.$result[0]['Geslacht'].'" target="_blank">Gezondheidsverklaring</a>';
		$acties_strandbewaking .= '<br><a href="https://trb.nu/clubforms/view.php?id=36878" target="_blank">Evaluatie strandbewaking</a>';		
		// Kader of ASC/809 in de week van het huidige seizoen.

		// Check op ASC/809 op urls
			$where1 = 'BWK'.Date("Y").'%';
			$where2 = 'EXAMEN'.Date("Y").'%';
			$sqlActiviteiten = "SELECT count(distinct `Activiteit`) as count FROM  `cr_activiteiten` WHERE  `Relatienr` =  \''.$userName.'\'' AND  `Locatie` IN ('OPL',  'ASC') AND (`Activiteit` LIKE '$where1' or `Activiteit` LIKE '$where2')";			
			$databaseActiviteiten = new Database();
			$databaseActiviteiten->query($sqlActiviteiten);	
			$activiteiten = $databaseActiviteiten->resultset();	
		
		if( requiresPrivilege('priv_test_WPC') == true || requiresPrivilege('priv_test_WPE') == true || requiresPrivilege('priv_test_Admin') == true ) {
			$acties_strandbewaking .= '<br><a href="https://trb.nu/clubredders" target="_blank">Club.Redders</a>';
			$acties_strandbewaking .= '<br><a href="https://trb.nu/clubforms/index.php" target="_blank">Club.Forms</a>';
			$acties_strandbewaking .= '<br><a href="https://trb.nu/wp-content/uploads/documenten/procesflow.pdf" target="_blank">Werkinstructies</a>';
		}elseif ( requiresPrivilege('priv_test_Member') == true &&  $activiteiten[0]['count'] > 0  ) {
			$acties_strandbewaking .= '<br><a href="https://trb.nu/clubredders" target="_blank">Club.Redders</a>';
		}

		// Categorie Kader acties - Toegang kader of bestuur of admins
		// Kader acties
		if( requiresPrivilege('priv_test_WPC') == true || requiresPrivilege('priv_test_WPE') == true || requiresPrivilege('priv_test_Admin') == true ) {
			$acties_kader .= '<br><a href="https://trb.nu/clubforms/view.php?id=27192&element_1='.$result[0]['Relatienr'].'&element_2='.$result[0]['VolledigeNaam'].'&element_3='.$result[0]['Email'].'&element_4='.$result[0]['BankrekNr'].'&element_67='.$result[0]['Verenigingsfunctie'].'&element_57=1&element_36=D.'.date("Y.m.d").'.'.$result[0]['Relatienr'].'.'.$NewCFAdmEntryNo.'"target="_self">Declaratie inkopen</a>';
			$acties_kader .= '<br><a href="https://trb.nu/clubforms/view.php?id=27192&element_1='.$result[0]['Relatienr'].'&element_2='.$result[0]['VolledigeNaam'].'&element_3='.$result[0]['Email'].'&element_4='.$result[0]['BankrekNr'].'&element_67='.$result[0]['Verenigingsfunctie'].'&element_57=2&element_36=D.'.date("Y.m.d").'.'.$result[0]['Relatienr'].'.'.$NewCFAdmEntryNo.'"target="_self">Declaratie reiskosten</a>';
			$acties_kader .= '<br><a href="https://trb.nu/clubforms/view.php?id=27192&element_1='.$result[0]['Relatienr'].'&element_2='.$result[0]['VolledigeNaam'].'&element_3='.$result[0]['Email'].'&element_4='.$result[0]['BankrekNr'].'&element_67='.$result[0]['Verenigingsfunctie'].'&element_57=3&element_36=D.'.date("Y.m.d").'.'.$result[0]['Relatienr'].'.'.$NewCFAdmEntryNo.'"target="_self">Declaratie overtochten</a>';
			}

		// Bestuur acties
		if( requiresPrivilege('priv_test_WPE') == true || requiresPrivilege('priv_test_Admin') == true ) {
			$acties_kader .= '<br><a href="https://trb.nu/clubforms/view.php?id=27192&element_1='.$result[0]['Relatienr'].'&element_2='.$result[0]['VolledigeNaam'].'&element_3='.$result[0]['Email'].'&element_67='.$result[0]['Verenigingsfunctie'].'&element_4=NL71RABO0113448716&element_57=4&element_36=F.'.date("Y.m.d").'.DEBITEURNR.FACTUURNR.'.$NewCFAdmEntryNo.'"target="_self">Registratie factuur</a>';
			$acties_kader .= '<br><a href="https://trb.nu/clubforms/view.php?id=27192&element_1='.$result[0]['Relatienr'].'&element_2='.$result[0]['VolledigeNaam'].'&element_3='.$result[0]['Email'].'&element_67='.$result[0]['Verenigingsfunctie'].'&element_4=NL71RABO0113448716&element_57=5&element_36=P.'.date("Y.m.d").'.'.$result[0]['Relatienr'].'.'.$NewCFAdmEntryNo.'"target="_self">Registratie pinbon</a>';
			} // Kader acties --> worden bestuur acties zodra competenties over zijn naar Club.Redders
		if( requiresPrivilege('priv_test_WPC') == true || requiresPrivilege('priv_test_WPE') == true || requiresPrivilege('priv_test_Admin') == true ) {
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
		if( requiresPrivilege('priv_test_Admin') == true ) {
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
