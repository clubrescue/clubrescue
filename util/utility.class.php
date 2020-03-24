<?php
	
	// Dit voegt de filter toe dat activiteiten van het huidige jaar nog niet getoond worden in CR. (true/false)
	define("LaatRoosterZien", true);
	
	function getUserName(){
		return strtok(wp_get_current_user()->user_login, '@');
	};
	
	function deleteInsertCSV($databaseTable, $csvfile,$encloseseparator, $user){
			
		if(file_exists($csvfile)){
		
			if($databaseTable === 'cr_activiteiten'){
				$where1 = 'BWK'.Date("Y").'%';
				$where2 = 'EXAMEN'.Date("Y").'%';
				$deleteQuery = "DELETE FROM `$databaseTable` WHERE `Activiteit` LIKE '$where1' or `Activiteit` LIKE '$where2'";		
			}else{
				$deleteQuery = "DELETE FROM `$databaseTable`";
			}
			
			$fieldseparator = '\';\'';
			$lineseparator = '\'\r\n\'';
			
			$insertQuery = "LOAD DATA LOCAL INFILE '"
			.$csvfile
			."' INTO TABLE `$databaseTable` FIELDS TERMINATED BY "
			.$fieldseparator
			." ENCLOSED BY '"
			.$encloseseparator
			."' "
			." LINES TERMINATED BY "
			//.$lineseparator . " IGNORE 1 LINES"; 
			.$lineseparator;
			//return $insertQuery;
			
			try{
				
				$database = new Database();
				
				// Start Transaction
				$database->beginTransaction();

				$database->query($deleteQuery);	
				$database->execute();
				
				$deleteCount = $database->RowCount();
				
				$database->query($insertQuery);
				$database->execute();
				
				$insertCount = $database->RowCount();
				
				// End Transaction -> Commit
				$database->endTransaction();
				
				$message = "Nieuwe records in : $databaseTable toegevoegd: $insertCount. Records uit $databaseTable verwijderd: $deleteCount";
				
				$logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \''.$message.'\', \''.$user->user_login.'\')';
				$database->query($logQuery);				
				$database->execute();

				return $message;
			
			}
			
			// Catch any errors
			catch(PDOException $e){
				$database->cancelTransaction();			
				$logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \''.$e->getMessage().'\', \''.$user->user_login.'\')';
				$database->query($logQuery);				
				$database->execute();
				return $e->getMessage();

			}
		}else{
			return 'CSV file not found!';
		}
	};
	
	function updatePasfotoTable($user){
		
		try{
			$sql = 'select distinct `Relatienr` from cr_leden where `Soort` = \'Bondslid\'order by `Relatienr`';
			$database = new Database();
			$database->query($sql);	
			$queryResult = $database->resultset();
			$insertCount = 0;
			
			foreach ($queryResult as $key => $value) { 
				if(file_exists('/srv/home/trbnu/domains/trb.nu/crbin/pasfotos/'.$value['Relatienr'].'.jpg')){
					$insertSQL = 'INSERT INTO cr_pasfotos VALUES (\''.$value['Relatienr'].'\',1) ON DUPLICATE KEY UPDATE `Aanwezig`=1;';
				}else{
					$insertSQL = 'INSERT INTO cr_pasfotos VALUES (\''.$value['Relatienr'].'\',0) ON DUPLICATE KEY UPDATE `Aanwezig`=0;';
				}
				$database->query($insertSQL);
				$database->execute();
				$insertCount++;
			}
			
			$message = "Aantal records in cr_pasfotos toegevoegd of geupdate: $insertCount.";				
			$logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \''.$message.'\', \''.$user->user_login.'\')';
			$database->query($logQuery);				
			$database->execute();
				
			return true;
		}
			// Catch any errors
		catch(PDOException $e){
			$database->cancelTransaction();			
			$logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \''.$e->getMessage().'\', \''.$user->user_login.'\')';
			$database->query($logQuery);				
			$database->execute();
			return $e->getMessage();
		}
	};	
	
	function opschonenPasfotos($user){
		
		try{
			$sql = 'select distinct `Relatienr` from cr_leden where `Soort` = \'Bondslid\'order by `Relatienr`';
			$database = new Database();
			$database->query($sql);	
			$queryResult = $database->resultset();
			$haystack = [];
			
			foreach ($queryResult as $key => $value) { 
				$haystack[] = $value['Relatienr'];
			}
			
			$dir = new DirectoryIterator('/srv/home/trbnu/domains/trb.nu/crbin/pasfotos/');
			$count = 0;
			foreach ($dir as $fileinfo) {
				if (!$fileinfo->isDot() && !$fileinfo->isDir()) {
					// Haal relatienummer uit de filename
					$relatienr = substr($fileinfo->getFilename(),0,strrpos($fileinfo->getFilename(), '.'));
					if(in_array($relatienr,$haystack) === false && $relatienr !== 'pasfoto'){
						$success = rename($fileinfo->getPathname(),'/srv/home/trbnu/domains/trb.nu/crbin/pasfotos/delete/'.$relatienr.'.jpg');
						if($success){
							$count++;
						};
					};
				}
			}
			
			$message = "Pasfotos verplaatst naar /delete: $count.";				
			$logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \''.$message.'\', \''.$user->user_login.'\')';
			$database->query($logQuery);				
			$database->execute();
				
			return true;
		}
			// Catch any errors
		catch(PDOException $e){
			$database->cancelTransaction();			
			$logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \''.$e->getMessage().'\', \''.$user->user_login.'\')';
			$database->query($logQuery);				
			$database->execute();
			return $e->getMessage();
		}
	};	
	
	function insertBankexport ($databaseTable, $csvfile,$encloseseparator, $user){

		if(file_exists($csvfile)){
		
			$fieldseparator = '\',\'';
			$lineseparator = '\'\r\n\'';
			
			$insertQuery = "LOAD DATA LOCAL INFILE '"
			.$csvfile
			."' INTO TABLE `$databaseTable` FIELDS TERMINATED BY "
			.$fieldseparator
			." ENCLOSED BY '"
			.$encloseseparator
			."' "
			." LINES TERMINATED BY "
			.$lineseparator . "IGNORE 1 LINES"; 

			$updateQuery = 'UPDATE `cr_bankexports` SET `Datum` = REPLACE(`Datum`, \'-\',\'\'), `Rentedatum` = REPLACE(`Rentedatum`, \'-\',\'\');';
			
			try{
				
				$database = new Database();
				
				// Start Transaction
				$database->beginTransaction();

				$database->query($insertQuery);
				$database->execute();
				
				$insertCount = $database->RowCount();
				
				// End Transaction -> Commit
				$database->endTransaction();
				
				$message = "Nieuwe records in : $databaseTable toegevoegd: $insertCount.";

				$database->query($updateQuery);
				$database->execute();
				
				$logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \''.$message.'\', \''.$user->user_login.'\')';
				$database->query($logQuery);				
				$database->execute();

				return $message;
			
			}
			
			// Catch any errors
			catch(PDOException $e){
				$database->cancelTransaction();			
				$logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \''.$e->getMessage().'\', \''.$user->user_login.'\')';
				$database->query($logQuery);				
				$database->execute();
				return $e->getMessage();

			}
		}else{
			return 'CSV file not found!';
		}
	};
	
	function deleteInsertClubFormsCSV($databaseTable, $csvfile,$encloseseparator, $user){

		if(file_exists($csvfile)){
		
			$deleteQuery = "DELETE FROM `$databaseTable`";
		
			$fieldseparator = '\',\'';
			$lineseparator = '\'\r\n\'';
			
			$insertQuery = "LOAD DATA LOCAL INFILE '"
			.$csvfile
			."' INTO TABLE `$databaseTable` FIELDS TERMINATED BY "
			.$fieldseparator
			." ENCLOSED BY '"
			.$encloseseparator
			."' "
			." LINES TERMINATED BY "
			.$lineseparator . "IGNORE 1 LINES"; 

			//$updateQuery = 'UPDATE `cr_bankexports` SET `Datum` = REPLACE(`Datum`, \'-\',\'\'), `Rentedatum` = REPLACE(`Rentedatum`, \'-\',\'\');';
			
			try{
				
				$database = new Database();
				
				// Start Transaction
				$database->beginTransaction();

				$database->query($insertQuery);
				$database->execute();
				
				$insertCount = $database->RowCount();
				
				// End Transaction -> Commit
				$database->endTransaction();
				
				$message = "Nieuwe records in : $databaseTable toegevoegd: $insertCount.";

				$database->query($updateQuery);
				$database->execute();
				
				$logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \''.$message.'\', \''.$user->user_login.'\')';
				$database->query($logQuery);				
				$database->execute();

				return $message;
			
			}
			
			// Catch any errors
			catch(PDOException $e){
				$database->cancelTransaction();			
				$logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \''.$e->getMessage().'\', \''.$user->user_login.'\')';
				$database->query($logQuery);				
				$database->execute();
				return $e->getMessage();

			}
		}else{
			return 'CSV file not found!';
		}
	};

	function getCountries(){

		return array(
            'AF' => 'Afghanistan',
            'AL' => 'Albanië',
            'DZ' => 'Algerije',
            'AS' => 'Amerikaans-Samoa',
            'VI' => 'Amerikaanse Maagdeneilanden',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AQ' => 'Antarctica',
            'AG' => 'Antigua en Barbuda',
            'AR' => 'Argentinië',
            'AM' => 'Armenië',
            'AW' => 'Aruba',
            'AU' => 'Australië',
            'AZ' => 'Azerbeidzjan',
            'BS' => 'Bahama\'s',
            'BH' => 'Bahrein',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BE' => 'België',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnië en Herzegovina',
            'BW' => 'Botswana',
            'BV' => 'Bouvet',
            'BR' => 'Brazilië',
            'IO' => 'Brits Territorium in de Indische Oceaan',
            'VG' => 'Britse Maagdeneilanden',
            'BN' => 'Brunei',
            'BG' => 'Bulgarije',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodja',
            'CA' => 'Canada',
            'CF' => 'Centraal-Afrikaanse Republiek',
            'CL' => 'Chili',
            'CN' => 'China',
            'CX' => 'Christmaseiland',
            'CC' => 'Cocoseilanden',
            'CO' => 'Colombia',
            'KM' => 'Comoren',
            'CG' => 'Congo-Brazzaville',
            'CD' => 'Congo-Kinshasa',
            'CK' => 'Cookeilanden',
            'CR' => 'Costa Rica',
            'CU' => 'Cuba',
            'CY' => 'Cyprus',
            'DK' => 'Denemarken',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominicaanse Republiek',
            'DE' => 'Duitsland',
            'EC' => 'Ecuador',
            'EG' => 'Egypte',
            'SV' => 'El Salvador',
            'GQ' => 'Equatoriaal-Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estland',
            'ET' => 'Ethiopië',
            'FO' => 'Faeröer',
            'FK' => 'Falklandeilanden',
            'FJ' => 'Fiji',
            'PH' => 'Filipijnen',
            'FI' => 'Finland',
            'FR' => 'Frankrijk',
            'GF' => 'Frans-Guyana',
            'PF' => 'Frans-Polynesië',
            'TF' => 'Franse Zuidelijke en Antarctische Gebieden',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GE' => 'Georgië',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GD' => 'Grenada',
            'GR' => 'Griekenland',
            'GL' => 'Groenland',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GG' => 'Guernsey',
            'GN' => 'Guinee',
            'GW' => 'Guinee-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haïti',
            'HM' => 'Heard en McDonaldeilanden',
            'HN' => 'Honduras',
            'HU' => 'Hongarije',
            'HK' => 'Hongkong',
            'IE' => 'Ierland',
            'IS' => 'IJsland',
            'IN' => 'India',
            'ID' => 'Indonesië',
            'IQ' => 'Irak',
            'IR' => 'Iran',
            'IM' => 'Isle of Man',
            'IL' => 'Israël',
            'IT' => 'Italië',
            'CI' => 'Ivoorkust',
            'JM' => 'Jamaica',
            'JP' => 'Japan',
            'YE' => 'Jemen',
            'JE' => 'Jersey',
            'JO' => 'Jordanië',
            'KY' => 'Kaaimaneilanden',
            'CV' => 'Kaapverdië',
            'CM' => 'Kameroen',
            'KZ' => 'Kazachstan',
            'KE' => 'Kenia',
            'KG' => 'Kirgizië',
            'KI' => 'Kiribati',
            'UM' => 'Kleine Pacifische eilanden van de Verenigde Staten',
            'KW' => 'Koeweit',
            'HR' => 'Kroatië',
            'LA' => 'Laos',
            'LS' => 'Lesotho',
            'LV' => 'Letland',
            'LB' => 'Libanon',
            'LR' => 'Liberia',
            'LY' => 'Libië',
            'LI' => 'Liechtenstein',
            'LT' => 'Litouwen',
            'LU' => 'Luxemburg',
            'MO' => 'Macau',
            'MK' => 'Macedonië',
            'MG' => 'Madagaskar',
            'MW' => 'Malawi',
            'MV' => 'Maldiven',
            'MY' => 'Maleisië',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MA' => 'Marokko',
            'MH' => 'Marshalleilanden',
            'MQ' => 'Martinique',
            'MR' => 'Mauritanië',
            'MU' => 'Mauritius',
            'YT' => 'Mayotte',
            'MX' => 'Mexico',
            'FM' => 'Micronesia',
            'MD' => 'Moldavië',
            'MC' => 'Monaco',
            'MN' => 'Mongolië',
            'ME' => 'Montenegro',
            'MS' => 'Montserrat',
            'MZ' => 'Mozambique',
            'MM' => 'Myanmar',
            'NA' => 'Namibië',
            'NR' => 'Nauru',
            'NL' => 'Nederland',
            'AN' => 'Nederlandse Antillen',
            'NP' => 'Nepal',
            'NI' => 'Nicaragua',
            'NC' => 'Nieuw-Caledonië',
            'NZ' => 'Nieuw-Zeeland',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NU' => 'Niue',
            'KP' => 'Noord-Korea',
            'MP' => 'Noordelijke Marianen',
            'NO' => 'Noorwegen',
            'NF' => 'Norfolk',
            'UG' => 'Oeganda',
            'UA' => 'Oekraïne',
            'UZ' => 'Oezbekistan',
            'OM' => 'Oman',
            'TL' => 'Oost-Timor',
            'AT' => 'Oostenrijk',
            'PK' => 'Pakistan',
            'PW' => 'Palau',
            'PS' => 'Palestijnse Autoriteit',
            'PA' => 'Panama',
            'PG' => 'Papoea-Nieuw-Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PN' => 'Pitcairneilanden',
            'PL' => 'Polen',
            'PT' => 'Portugal',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'RO' => 'Roemenië',
            'RU' => 'Rusland',
            'RW' => 'Rwanda',
            'RE' => 'Réunion',
            'KN' => 'Saint Kitts en Nevis',
            'LC' => 'Saint Lucia',
            'VC' => 'Saint Vincent en de Grenadines',
            'BL' => 'Saint-Barthélemy',
            'PM' => 'Saint-Pierre en Miquelon',
            'SB' => 'Salomonseilanden',
            'WS' => 'Samoa',
            'SM' => 'San Marino',
            'ST' => 'Sao Tomé en Principe',
            'SA' => 'Saoedi-Arabië',
            'SN' => 'Senegal',
            'RS' => 'Servië',
            'SC' => 'Seychellen',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SH' => 'Sint-Helena',
            'MF' => 'Sint-Maarten',
            'SI' => 'Slovenië',
            'SK' => 'Slowakije',
            'SD' => 'Soedan',
            'SO' => 'Somalië',
            'ES' => 'Spanje',
            'SJ' => 'Spitsbergen en Jan Mayen',
            'LK' => 'Sri Lanka',
            'SR' => 'Suriname',
            'SZ' => 'Swaziland',
            'SY' => 'Syrië',
            'TJ' => 'Tadzjikistan',
            'TW' => 'Taiwan',
            'TZ' => 'Tanzania',
            'TH' => 'Thailand',
            'TG' => 'Togo',
            'TK' => 'Tokelau-eilanden',
            'TO' => 'Tonga',
            'TT' => 'Trinidad en Tobago',
            'TD' => 'Tsjaad',
            'CZ' => 'Tsjechië',
            'TN' => 'Tunesië',
            'TR' => 'Turkije',
            'TM' => 'Turkmenistan',
            'TC' => 'Turks- en Caicoseilanden',
            'TV' => 'Tuvalu',
            'UY' => 'Uruguay',
            'VU' => 'Vanuatu',
            'VA' => 'Vaticaanstad',
            'VE' => 'Venezuela',
            'GB' => 'Verenigd Koninkrijk',
            'AE' => 'Verenigde Arabische Emiraten',
            'US' => 'Verenigde Staten',
            'VN' => 'Vietnam',
            'WF' => 'Wallis en Futuna',
            'EH' => 'Westelijke Sahara',
            'BY' => 'Wit-Rusland',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
            'ZA' => 'Zuid-Afrika',
            'GS' => 'Zuid-Georgië en de Zuidelijke Sandwicheilanden',
            'KR' => 'Zuid-Korea',
            'SE' => 'Zweden',
            'CH' => 'Zwitserland',
            'AX' => 'Aland'
        );
	}
	
	function getNationalities(){

		return array(
			'AF' => 'Afghaanse',
			'AL' => 'Albanese',
			'DZ' => 'Algerijnse',
			'AS' => 'LAND: Amerikaans-Samoa',
			'VI' => 'LAND: Amerikaanse Maagdeneilanden',
			'AD' => 'Andorrese',
			'AO' => 'Angolese',
			'AI' => 'LAND: Anguilla',
			'AQ' => 'LAND: Antarctica',
			'AG' => 'Antiguaanse en Barbudaanse',
			'AR' => 'Argentijnse',
			'AM' => 'Armeense',
			'AW' => 'LAND: Aruba',
			'AU' => 'Australische',
			'AZ' => 'Azerbeidszjaanse',
			'BS' => 'Bahamaanse',
			'BH' => 'Bahreinse',
			'BD' => 'Bengalese',
			'BB' => 'Barbadaanse',
			'BE' => 'Belgische',
			'BZ' => 'Belizaanse',
			'BJ' => 'Beninse',
			'BM' => 'LAND: Bermuda',
			'BT' => 'Bhutaanse',
			'BO' => 'Boliviaanse',
			'BA' => 'LAND: Bosnië en Herzegovina',
			'BW' => 'Botswaanse',
			'BV' => 'LAND: Bouvet',
			'BR' => 'Braziliaanse',
			'IO' => 'LAND: Brits Territorium in de Indische Oceaan',
			'VG' => 'LAND: Britse Maagdeneilanden',
			'BN' => 'Bruneise',
			'BG' => 'Bulgaarse',
			'BF' => 'Nationaliteit van Burkina Faso',
			'BI' => 'Burundese',
			'KH' => 'Cambodjaanse',
			'CA' => 'Canadese',
			'CF' => 'Centraalafrikaanse',
			'CL' => 'Chileense',
			'CN' => 'Chinese',
			'CX' => 'LAND: Christmaseiland',
			'CC' => 'LAND: Cocoseilanden',
			'CO' => 'Colombiaanse',
			'KM' => 'LAND: Comoren',
			'CG' => 'LAND: Congo-Brazzaville',
			'CD' => 'LAND: Congo-Kinshasa',
			'CK' => 'LAND: Cookeilanden',
			'CR' => 'Costaricaanse',
			'CU' => 'Cubaanse',
			'CY' => 'Cypriotische',
			'DK' => 'Deense',
			'DJ' => 'Djiboutiaanse',
			'DM' => 'Dominicase',
			'DO' => 'Dominicaanse',
			'DE' => 'Duitse',
			'EC' => 'Ecuadoraanse',
			'EG' => 'Egyptische',
			'SV' => 'Salvadoriaanse',
			'GQ' => 'LAND: Equatoriaal-Guinea',
			'ER' => 'Eritrese',
			'EE' => 'Estlandse',
			'ET' => 'Ethiopische',
			'FO' => 'LAND: Faeröer',
			'FK' => 'LAND: Falklandeilanden',
			'FJ' => 'Fijische',
			'PH' => 'Filipijnse',
			'FI' => 'Finse',
			'FR' => 'Franse',
			'GF' => 'LAND: Frans-Guyana',
			'PF' => 'LAND: Frans-Polynesië',
			'TF' => 'LAND: Franse Zuidelijke en Antarctische Gebieden',
			'GA' => 'Gabonese',
			'GM' => 'Gambiaanse',
			'GE' => 'Georgische',
			'GH' => 'Ghanese',
			'GI' => 'LAND: Gibraltar',
			'GD' => 'Grenadaanse',
			'GR' => 'Griekse',
			'GL' => 'LAND: Groenland',
			'GP' => 'LAND: Guadeloupe',
			'GU' => 'LAND: Guam',
			'GT' => 'Guatamalteekse',
			'GG' => 'LAND: Guernsey',
			'GN' => 'Guinese',
			'GW' => 'Guinee-Bissause',
			'GY' => 'Guyaanse',
			'HT' => 'Haïtiaanse',
			'HM' => 'LAND: Heard en McDonaldeilanden',
			'HN' => 'Hondurese',
			'HU' => 'Hongaarse',
			'HK' => 'LAND: Hongkong',
			'IE' => 'Ierse',
			'IS' => 'IJslandse',
			'IN' => 'Indiase',
			'ID' => 'Indonesische',
			'IQ' => 'Iraakse',
			'IR' => 'Iraanse',
			'IM' => 'LAND: Isle of Man',
			'IL' => 'Israëlische',
			'IT' => 'Italiaanse',
			'CI' => 'Ivoriaanse',
			'JM' => 'Jamaicaanse',
			'JP' => 'Japanse',
			'YE' => 'Jemenitische',
			'JE' => 'LAND: Jersey',
			'JO' => 'Jordaanse',
			'KY' => 'LAND: Kaaimaneilanden',
			'CV' => 'Kaapverdische',
			'CM' => 'Kameroense',
			'KZ' => 'Kazachstaanse',
			'KE' => 'Keniase',
			'KG' => 'Kirgische',
			'KI' => 'Kiribatische',
			'UM' => 'LAND: Kleine Pacifische eilanden van de Verenigde Staten',
			'KW' => 'Koeweitse',
			'HR' => 'Kroatische',
			'LA' => 'Laotiaanse',
			'LS' => 'Lesothaanse',
			'LV' => 'Letse',
			'LB' => 'Libanese',
			'LR' => 'Liberiaanse',
			'LY' => 'Libische',
			'LI' => 'Liechtensteinse',
			'LT' => 'Litouwse',
			'LU' => 'Luxemburgse',
			'MO' => 'LAND: Macau',
			'MK' => 'LAND: Macedonië',
			'MG' => 'Malagassische',
			'MW' => 'Malawische',
			'MV' => 'Maldivische',
			'MY' => 'Maleisische',
			'ML' => 'Malinese',
			'MT' => 'Maltese',
			'MA' => 'Marokkaanse',
			'MH' => 'Marshalleilandse',
			'MQ' => 'LAND: Martinique',
			'MR' => 'Mauritaanse',
			'MU' => 'Mauritiaanse',
			'YT' => 'LAND: Mayotte',
			'MX' => 'Mexicaanse',
			'FM' => 'Micronesische',
			'MD' => 'Moldavische',
			'MC' => 'Monegaskische',
			'MN' => 'Mongoolse',
			'ME' => 'Montenegrijnse',
			'MS' => 'LAND: Montserrat',
			'MZ' => 'Mozambikaanse',
			'MM' => 'LAND: Myanmar',
			'NA' => 'Namibische',
			'NR' => 'Nauruaanse',
			'NL' => 'Nederlandse',
			'AN' => 'LAND: Nederlandse Antillen',
			'NP' => 'Nepalese',
			'NI' => 'Nicaraguaanse',
			'NC' => 'LAND: Nieuw-Caledonië',
			'NZ' => 'Nieuw-Zeelandse',
			'NE' => 'Nigerese',
			'NG' => 'Nigeriaanse',
			'NU' => 'LAND: Niue',
			'KP' => 'Noord-Koreaanse',
			'MP' => 'LAND: Noordelijke Marianen',
			'NO' => 'Noorse',
			'NF' => 'LAND: Norfolk',
			'UG' => 'LAND: Oeganda',
			'UA' => 'Oekraïense',
			'UZ' => 'Oezbeekse',
			'OM' => 'Omanitische',
			'TL' => 'Oost-Timorese',
			'AT' => 'Oostenrijkse',
			'PK' => 'Pakistaanse',
			'PW' => 'LAND: Palau',
			'PS' => 'LAND: Palestijnse Autoriteit',
			'PA' => 'Panamese',
			'PG' => 'Papoea-Nieuw-Guinese',
			'PY' => 'Paraguayaanse',
			'PE' => 'Peruaanse',
			'PN' => 'LAND: Pitcairneilanden',
			'PL' => 'Poolse',
			'PT' => 'Portugese',
			'PR' => 'LAND: Puerto Rico',
			'QA' => 'LAND: Qatar',
			'RO' => 'Roemeense',
			'RU' => 'Russische',
			'RW' => 'Rwandese',
			'RE' => 'LAND: Réunion',
			'KN' => 'Saint Kitts-Nevistaanse',
			'LC' => 'Sintluciaanse',
			'VC' => 'Sint Vincent en Grenadijnse',
			'BL' => 'LAND: Saint-Barthélemy',
			'PM' => 'LAND: Saint-Pierre en Miquelon',
			'SB' => 'LAND: Salomonseilanden',
			'WS' => 'Samoaanse',
			'SM' => 'Sanmarinese',
			'ST' => 'LAND: Sao Tomé en Principe',
			'SA' => 'LAND: Saoedi-Arabië',
			'SN' => 'Senegalese',
			'RS' => 'Servische',
			'SC' => 'Seychelse',
			'SL' => 'Sierra Leoonse',
			'SG' => 'Singaporese',
			'SH' => 'LAND: Sint-Helena',
			'MF' => 'LAND: Sint-Maarten',
			'SI' => 'Sloveense',
			'SK' => 'Slowaakse',
			'SD' => 'LAND: Soedan',
			'SO' => 'Somalische',
			'ES' => 'Spaanse',
			'SJ' => 'LAND: Spitsbergen en Jan Mayen',
			'LK' => 'Srilankaanse',
			'SR' => 'Surinaamse',
			'SZ' => 'LAND: Swaziland',
			'SY' => 'Syrische',
			'TJ' => 'Tadzjiekistaanse',
			'TW' => 'Taiwanese',
			'TZ' => 'Tanzaniaanse',
			'TH' => 'Thaise',
			'TG' => 'Togolese',
			'TK' => 'LAND: Tokelau-eilanden',
			'TO' => 'Tongaanse',
			'TT' => 'Trinidaanse',
			'TD' => 'Tsjadische',
			'CZ' => 'Tsjechische',
			'TN' => 'Tunesische',
			'TR' => 'Turkse',
			'TM' => 'Turkmeense',
			'TC' => 'LAND: Turks- en Caicoseilanden',
			'TV' => 'Tuvaluaanse',
			'UY' => 'Uruguayaanse',
			'VU' => 'Vanuatuse',
			'VA' => 'Vaticaanse',
			'VE' => 'Venezolaanse',
			'GB' => 'LAND: Verenigd Koninkrijk',
			'AE' => 'Nationaliteit van de Verenigde Arabische Emiraten',
			'US' => 'Amerikaanse',
			'VN' => 'Vietnamese',
			'WF' => 'LAND: Wallis en Futuna',
			'EH' => 'LAND: Westelijke Sahara',
			'BY' => 'LAND: Wit-Rusland',
			'ZM' => 'Zambiaanse',
			'ZW' => 'Zimbabwaanse',
			'ZA' => 'Zuid-Afrikaanse',
			'GS' => 'LAND: Zuid-Georgië en de Zuidelijke Sandwicheilanden',
			'KR' => 'Zuid-Koreaanse',
			'SE' => 'Zweedse',
			'CH' => 'Zwitserse',
			'AX' => 'LAND: Aland',
        );
	};
	
	function build_sql_update($table, $data, $where){
		$cols = array();
	 
		foreach($data as $key=>$val) {
			if($key !== 'Relatienr'){
				$escVal = htmlspecialchars($val);
				$cols[] = "`$key` = '$escVal'";
			}
		}
		$sql = "UPDATE `$table` SET " . implode(', ', $cols) . " WHERE $where";
	 
		return($sql);
	};
	
	function build_sql_insert($table, $data){

		$cols = array();
	 	$values = array();

		foreach($data as $key=>$val) {
			$values[] = '\''.htmlspecialchars($val).'\'';
			$cols[] = '`'.htmlspecialchars($key).'`';
		}
		
		$sql = "INSERT INTO `$table` (". implode(', ', $cols) . ") VALUES (" . implode(', ', $values) . ")";
	 
		return($sql);
	};
	
	function getProfilePictureSource($relatienr){
		if(file_exists('/srv/home/trbnu/domains/trb.nu/crbin/pasfotos/'.$relatienr.'.jpg')){
			//HTTP Auth voor de pasfotos zit in de url.
			$path = '/srv/home/trbnu/domains/trb.nu/crbin/pasfotos/'.$relatienr.'.jpg';
		}else{
			$path = $_SERVER['DOCUMENT_ROOT'] . '/clubredders/images/crm-photo-notavailable.svg';
		}	
	
		if(@fopen($path,"r")==true){
			$type = pathinfo($path, PATHINFO_EXTENSION);
			$data = file_get_contents($path);
		}
		
		return 'data:image/' . $type . ';base64,' . base64_encode($data);
	}
?>