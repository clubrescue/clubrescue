<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

/*** Club.Redders - PAGE TEMPLATE TYPE - InternalDataMutations - v0.8.5
	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();
	
	// Include database class
	include 'util/utility.class.php';	
	include 'util/database.class.php';	
	/* ABSOLUUT NIET MEER AANZETEN!!! IIG NIET ZONDER OVERLEG MET RUUD!!!
	if(isset($_FILES['EDP-Leden'])){
		$errors= array();
		$file_name_backup = 'leden('.time().').csv';
		$file_name = 'leden.csv';
		$file_size =$_FILES['EDP-Leden']['size'];
		$file_tmp =$_FILES['EDP-Leden']['tmp_name'];
		echo $file_tmp;
		$file_type=$_FILES['EDP-Leden']['type'];   
		$file_ext=strtolower(end(explode('.',$_FILES['EDP-Leden']['name'])));
		
		$expensions= array("csv");
		if(in_array($file_ext,$expensions)=== false){
			$errors[]="Het gekozen EDP-Leden export-import bestand is geen CSV. Alleen export-imports op basis van CSV zijn toegestaan.";
		}
		if($file_size > 8388608){
			$errors[]="EDP-Leden CSV bestanden mogen (momenteel) niet groter zijn dan 8 MB.";
		}				
		if(empty($errors)==true){
			$target = "/srv/home/trbnu/domains/trb.nu/crbin/import/".$file_name;
			$target_backup = "/srv/home/trbnu/domains/trb.nu/crbin/backup/".$file_name_backup;
			rename($target,$target_backup);
			move_uploaded_file($file_tmp,$target);			
			$ledenMessage = deleteInsertCSV('cr_leden', $target, '\"', $user);
			echo nl2br($ledenMessage.'<br>');
			echo nl2br("Het EDP-Leden export-import CSV bestand is geupload, het oude bestand is overschreven.\n");
		}else{
			// print_r($errors);
		}
	}
	*/

	if(isset($_FILES['EDP-Diplomas'])){
		$errors= array();
		$file_name_backup = 'diplomas('.time().').csv';
		$file_name = 'diplomas.csv';		
		$file_size =$_FILES['EDP-Diplomas']['size'];
		$file_tmp =$_FILES['EDP-Diplomas']['tmp_name'];
		$file_type=$_FILES['EDP-Diplomas']['type'];   
		$file_ext=strtolower(end(explode('.',$_FILES['EDP-Diplomas']['name'])));
		
		$expensions= array("csv");	
		if(in_array($file_ext,$expensions)=== false){
			$errors[]="Het gekozen EDP-Diplomas export-import bestand is geen CSV. Alleen export-imports op basis van CSV zijn toegestaan.";
		}
		if($file_size > 8388608){
			$errors[]="EDP-Diplomas CSV bestanden mogen (momenteel) niet groter zijn dan 8 MB.";
		}				
		if(empty($errors)==true){
			$target = "/srv/home/trbnu/domains/trb.nu/crbin/import/".$file_name;
			$target_backup = "/srv/home/trbnu/domains/trb.nu/crbin/backup/".$file_name_backup;
			rename($target,$target_backup);
			move_uploaded_file($file_tmp,$target);				
			$diplomaMessage = deleteInsertCSV('cr_diplomas', $target, '\"',$user);
			echo nl2br($diplomaMessage.'<br>');			
			echo nl2br("Het EDP-Diplomas export-import CSV bestand is geupload, het oude bestand is overschreven.\n");
		}else{
			// print_r($errors);
		}
	}

	if(isset($_FILES['EDP-Indeling'])){
		$errors= array();
		$file_name = 'indeling.csv';
		$file_name_backup = 'indeling('.time().').csv';
		$file_size =$_FILES['EDP-Indeling']['size'];
		$file_tmp =$_FILES['EDP-Indeling']['tmp_name'];
		$file_type=$_FILES['EDP-Indeling']['type'];   
		$file_ext=strtolower(end(explode('.',$_FILES['EDP-Indeling']['name'])));
		
		$expensions= array("csv");		
		if(in_array($file_ext,$expensions)=== false){
			$errors[]="Het gekozen EDP-Indeling export-import bestand is geen CSV. Alleen export-imports op basis van CSV zijn toegestaan.";
		}
		if($file_size > 8388608){
			$errors[]="EDP-Indeling CSV bestanden mogen (momenteel) niet groter zijn dan 8 MB.";
		}				
		if(empty($errors)==true){
			$target = "/srv/home/trbnu/domains/trb.nu/crbin/import/".$file_name;
			$target_backup = "/srv/home/trbnu/domains/trb.nu/crbin/backup/".$file_name_backup;
			rename($target,$target_backup);
			move_uploaded_file($file_tmp,$target);			
			$indelingMessage = deleteInsertCSV('cr_activiteiten', $target, '',$user);
			echo nl2br($indelingMessage.'<br>');
			echo nl2br("Het EDP-Bondsfuncties export-import CSV bestand is geupload, het oude bestand is overschreven.\n");
		}else{
			// print_r($errors);
		}
	}
	
	if(isset($_FILES['EDP-Bondsfuncties'])){
		$errors= array();
		$file_name = 'bondsfuncties.csv';
		$file_name_backup = 'bondsfuncties('.time().').csv';
		$file_size =$_FILES['EDP-Bondsfuncties']['size'];
		$file_tmp =$_FILES['EDP-Bondsfuncties']['tmp_name'];
		$file_type=$_FILES['EDP-Bondsfuncties']['type'];   
		$file_ext=strtolower(end(explode('.',$_FILES['EDP-Bondsfuncties']['name'])));
		
		$expensions= array("csv");		
		if(in_array($file_ext,$expensions)=== false){
			$errors[]="Het gekozen EDP-Bondsfuncties export-import bestand is geen CSV. Alleen export-imports op basis van CSV zijn toegestaan.";
		}
		if($file_size > 8388608){
			$errors[]="EDP-Bondsfuncties CSV bestanden mogen (momenteel) niet groter zijn dan 8 MB.";
		}				
		if(empty($errors)==true){
			$target = "/srv/home/trbnu/domains/trb.nu/crbin/import/".$file_name;
			$target_backup = "/srv/home/trbnu/domains/trb.nu/crbin/backup/".$file_name_backup;
			rename($target,$target_backup);
			move_uploaded_file($file_tmp,$target);			
			$indelingMessage = deleteInsertCSV('cr_bondsfuncties', $target, '',$user);
			echo nl2br($indelingMessage.'<br>');
			echo nl2br("Het EDP-Bondsfuncties export-import CSV bestand is geupload, het oude bestand is overschreven.\n");
		}else{
			// print_r($errors);
		}
	}
	
	if(isset($_FILES['EDP-Bankexport'])){
		$errors= array();
		$file_name = 'bankexport.csv';
		$file_name_backup = 'bankexport('.time().').csv';
		$file_size =$_FILES['EDP-Bankexport']['size'];
		$file_tmp =$_FILES['EDP-Bankexport']['tmp_name'];
		$file_type=$_FILES['EDP-Bankexport']['type'];   
		$file_ext=strtolower(end(explode('.',$_FILES['EDP-Bankexport']['name'])));
		
		$expensions= array("csv");		
		if(in_array($file_ext,$expensions)=== false){
			$errors[]="Het gekozen EDP-Bankexport export-import bestand is geen CSV. Alleen export-imports op basis van CSV zijn toegestaan.";
		}
		if($file_size > 8388608){
			$errors[]="EDP-Bankexport CSV bestanden mogen (momenteel) niet groter zijn dan 8 MB.";
		}				
		if(empty($errors)==true){
			$target = "/srv/home/trbnu/domains/trb.nu/crbin/import/".$file_name;
			$target_backup = "/srv/home/trbnu/domains/trb.nu/crbin/backup/".$file_name_backup;
			rename($target,$target_backup);
			move_uploaded_file($file_tmp,$target);			
			$indelingMessage = insertBankexport('cr_bankexports', $target, '"',$user);
			echo nl2br($indelingMessage.'<br>');
			echo nl2br("Het EDP-Bankexport export-import CSV bestand is geupload, het oude bestand is overschreven.\n");
		}else{
			// print_r($errors);
		}
	}
	
		if(isset($_FILES['EDP-Administratieexport'])){
		$errors= array();
		$file_name = 'Administratieformulier.csv';
		$file_name_backup = 'administratieformulier('.time().').csv';
		$file_size =$_FILES['EDP-Administratieexport']['size'];
		$file_tmp =$_FILES['EDP-Administratieexport']['tmp_name'];
		$file_type=$_FILES['EDP-Administratieexport']['type'];   
		$file_ext=strtolower(end(explode('.',$_FILES['EDP-Administratieexport']['name'])));
		
		$expensions= array("csv");		
		if(in_array($file_ext,$expensions)=== false){
			$errors[]="Het gekozen EDP-Administratieexport export-import bestand is geen CSV. Alleen export-imports op basis van CSV zijn toegestaan.";
		}
		if($file_size > 8388608){
			$errors[]="EDP-Administratieexport CSV bestanden mogen (momenteel) niet groter zijn dan 8 MB.";
		}				
		if(empty($errors)==true){
			$target = "/srv/home/trbnu/domains/trb.nu/crbin/import/".$file_name;
			$target_backup = "/srv/home/trbnu/domains/trb.nu/crbin/backup/".$file_name_backup;
			rename($target,$target_backup);
			move_uploaded_file($file_tmp,$target);			
			$indelingMessage = deleteInsertClubFormsCSV('cr_declaraties', $target, '"',$user);
			echo nl2br($indelingMessage.'<br>');
			echo nl2br("Het EDP-Administratieexport export-import CSV bestand is geupload, het oude bestand is overschreven.\n");
		}else{
			// print_r($errors);
		}
	}

?>
<?php include 'header.php'; ?>
<main>
<div class="container">
		<div class="section">
			<p>Deze IDM actie is gelogd op naam van <?php echo $user->user_firstname . " " . $user->user_lastname;?></p>
			<!---// BEGIN stuff here for all contributors, authors, editors or admins--->
			<?php if( current_user_can('contributor') ||  current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') ) {  ?>
			<p>LET OP: Met onderstaande velden kan de Sportlink data<br>worden geupdate in de website, foutieve uploads<br>kunnen de website stuk maken!</p>
				<form action="" method="POST" enctype="multipart/form-data">
					<!--<div class="file-field input-field">   NOOT: ABSOLUUT NIET MEER AANZETTEN IIG NIET ZONDER OVERLEG MET RUUD!!!
						<div class="btn">
							<span>leden.csv test</span>
							<input type="file" name="EDP-Leden">
						</div>
						<div class="file-path-wrapper">
							<input class="file-path validate" type="text">
						</div>
					</div>-->
					<div class="file-field input-field">
						<div class="btn">
							<span>diplomas.csv</span>
							<input type="file" name="EDP-Diplomas">
						</div>
						<div class="file-path-wrapper">
							<input class="file-path validate" type="text">
						</div>
					</div>
					<div class="file-field input-field">
						<div class="btn">
							<span>indeling.csv</span>
							<input type="file" name="EDP-Indeling">
						</div>
						<div class="file-path-wrapper">
							<input class="file-path validate" type="text">
						</div>
					</div>
					<div class="file-field input-field">
						<div class="btn">
							<span>bondsfuncties.csv</span>
							<input type="file" name="EDP-Bondsfuncties">
						</div>
						<div class="file-path-wrapper">
							<input class="file-path validate" type="text">
						</div>
					</div>
					<?php if(current_user_can('editor') || current_user_can('administrator') ) { ?> 
					<div class="file-field input-field">
						<div class="btn">
							<span>transactions.csv</span>
							<input type="file" name="EDP-Bankexport">
						</div>
						<div class="file-path-wrapper">
							<input class="file-path validate" type="text">
						</div>
					</div>
					<div class="file-field input-field">
						<div class="btn">
							<span>administratieformulier.csv</span>
							<input type="file" name="EDP-Administratieexport">
						</div>
						<div class="file-path-wrapper">
							<input class="file-path validate" type="text">
						</div>
					</div>
					<?php } ?>
					<button class="btn waves-effect waves-light" type="submit" name="action">uploaden
						<i class="material-icons right">file_upload</i>
					</button>
				</form>
			<!---// END stuff here for all contributors, authors, editors or admins--->
			<?php } ?>
		</div>
</div>
</main>
<?php include 'footer.php'; ?>