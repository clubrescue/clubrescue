<?php

	define("DB_HOST_CR", "sql6.pcextreme.nl");
	define("DB_USER_CR", "73994clubredders");
	define("DB_PASS_CR", "AarS9YrJVlzfEzFa");
	define("DB_NAME_CR", "73994clubredders");
	
	//ini_set('log_errors',TRUE);
	//ini_set('error_reporting', E_ALL);
	
	// Dit voegt de filter toe dat activiteiten van het huidige jaar nog niet getoond worden in CR. (true/false)
	define("LaatRoosterZien", true);

	function getUserName(){
		return 'D290BLU';
	}
	
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
				if(file_exists('pasfotos/'.$value['Relatienr'].'.jpg')){
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
			
			$dir = new DirectoryIterator('./pasfotos/');
			$count = 0;
			foreach ($dir as $fileinfo) {
				if (!$fileinfo->isDot() && !$fileinfo->isDir()) {
					// Haal relatienummer uit de filename
					$relatienr = substr($fileinfo->getFilename(),0,strrpos($fileinfo->getFilename(), '.'));
					if(in_array($relatienr,$haystack) === false && $relatienr !== 'pasfoto'){
						$success = rename($fileinfo->getPathname(),'./pasfotos/delete/'.$relatienr.'.jpg');
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
?>