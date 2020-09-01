<?php
	/*** Club.Redders - PAGE TEMPLATE TYPE - InternalDataMutations - v0.8.5
	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("../../wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/
	login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();
	
	// Include database class
	include '../../util/utility.class.php';
	include '../../util/database.class.php';

	$crbinRoot = '/home/u70472p67165/domains/trb.nu';

	//ini_set('error_reporting', E_ALL);
	//ini_set('display_errors', 'On');  //On or Off

	/* Deze module is vervallen en vervangen door het online roosteren, rooster en kaart module.
	if(isset($_FILES['ExcelBewakingsTool'])){
		$errors= array();
		$file_name = 'clubredders-ebt.xlsb';
		$file_name_backup = 'clubredders-ebt'.time().'.xlsb';
		$file_size =$_FILES['ExcelBewakingsTool']['size'];
		$file_tmp =$_FILES['ExcelBewakingsTool']['tmp_name'];
		$file_type=$_FILES['ExcelBewakingsTool']['type']; 
		$file_ext=strtolower(end(explode('.',$_FILES['ExcelBewakingsTool']['name'])));
		$expensions= array("xlsb");
		
		if(in_array($file_ext,$expensions)=== false){
			$errors[]="De gekozen Excel Bewakings Tool update is geen XLSB. Alleen updates op basis van XLSB bestanden zijn toegestaan.";
		}
		if($file_size > 8388608){
			$errors[]="Excel Bewakings Tool update bestanden mogen (momenteel) niet groter zijn dan 8 MB.";
		}				
		if(empty($errors)==true){
			echo rename($crbinRoot."/crbin/excel/".$file_name,$crbinRoot."/crbin/backup/".$file_name_backup );
			move_uploaded_file($file_tmp,$crbinRoot."/crbin/excel/".$file_name);
			echo nl2br("De Excel Bewakings Tool update is geupload, de oude versie is geback-upt.\n");
			$database = new Database();
			$logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \'Excel Bewakings Tool geupload.\', \''.$user->user_login.'\')';
			$database->query($logQuery);				
			$database->execute();			
		}else{
			print_r($errors);
		}
	}
	*/
	if(isset($_FILES['ExcelInschrijvingsTool'])){
		$errors= array();
		//$file_name = $_FILES['ExcelInschrijvingsTool']['name'];
		$file_name = 'clubredders-eit.xlsb';
		$file_name_backup = 'clubredders-eit'.time().'.xlsb';
		$file_size =$_FILES['ExcelInschrijvingsTool']['size'];
		$file_tmp =$_FILES['ExcelInschrijvingsTool']['tmp_name'];
		$file_type=$_FILES['ExcelInschrijvingsTool']['type'];   
		$file_ext=strtolower(end(explode('.',$_FILES['ExcelInschrijvingsTool']['name'])));
		
		$expensions= array("xlsb");	
		if(in_array($file_ext,$expensions)=== false){
			$errors[]="De gekozen Excel Inschrijvings Tool update is geen XLSB. Alleen updates op basis van XLSB bestanden zijn toegestaan.";
		}
		if($file_size > 8388608){
			$errors[]="Excel Inschrijvings Tool update bestanden mogen (momenteel) niet groter zijn dan 8 MB.";
		}				
		if(empty($errors)==true){
			rename($crbinRoot."/crbin/excel/".$file_name, $crbinRoot."/crbin/backup/".$file_name_backup);
			move_uploaded_file($file_tmp,$crbinRoot."/crbin/excel/".$file_name);
			echo nl2br("De ExcelInschrijvingsTool update is geupload, de oude versie is geback-upt.\n");
			$database = new Database();
			$logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \'Excel Inschrijvings Tool geupload.\', \''.$user->user_login.'\')';
			$database->query($logQuery);				
			$database->execute();				
		}else{
			print_r($errors);
		}
	}

		if(isset($_FILES['ExcelDiplomacheckTool'])){
		$errors= array();
		//$file_name = $_FILES['ExcelDiplomacheckTool']['name'];
		$file_name = 'clubredders-edt.xlsb';
		$file_name_backup = 'clubredders-edt'.time().'.xlsb';
		$file_size =$_FILES['ExcelDiplomacheckTool']['size'];
		$file_tmp =$_FILES['ExcelDiplomacheckTool']['tmp_name'];
		$file_type=$_FILES['ExcelDiplomacheckTool']['type'];   
		$file_ext=strtolower(end(explode('.',$_FILES['ExcelDiplomacheckTool']['name'])));
		
		$expensions= array("xlsb");	
		if(in_array($file_ext,$expensions)=== false){
			$errors[]="De gekozen Excel Diplomacheck Tool update is geen XLSB. Alleen updates op basis van XLSB bestanden zijn toegestaan.";
		}
		if($file_size > 8388608){
			$errors[]="Excel Diplomacheck Tool update bestanden mogen (momenteel) niet groter zijn dan 8 MB.";
		}				
		if(empty($errors)==true){
			rename($crbinRoot."/crbin/excel/".$file_name, $crbinRoot."/crbin/backup/".$file_name_backup);
			move_uploaded_file($file_tmp,$crbinRoot."/crbin/excel/".$file_name);
			echo nl2br("De ExcelDiplomacheckTool update is geupload, de oude versie is geback-upt.\n");
			$database = new Database();
			$logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \'Excel Diplomacheck Tool geupload.\', \''.$user->user_login.'\')';
			$database->query($logQuery);				
			$database->execute();				
		}else{
			print_r($errors);
		}
	}
	
		if(isset($_FILES['ExcelFinancieleTool'])){
		$errors= array();
		//$file_name = $_FILES['ExcelFinancieleTool']['name'];
		$file_name = 'clubredders-eft.xlsb';
		$file_name_backup = 'clubredders-eft'.time().'.xlsb';
		$file_size =$_FILES['ExcelFinancieleTool']['size'];
		$file_tmp =$_FILES['ExcelFinancieleTool']['tmp_name'];
		$file_type=$_FILES['ExcelFinancieleTool']['type'];   
		$file_ext=strtolower(end(explode('.',$_FILES['ExcelFinancieleTool']['name'])));
		
		$expensions= array("xlsb");	
		if(in_array($file_ext,$expensions)=== false){
			$errors[]="De gekozen Excel Financiele Tool update is geen XLSB. Alleen updates op basis van XLSB bestanden zijn toegestaan.";
		}
		if($file_size > 8388608){
			$errors[]="Excel Financiele Tool update bestanden mogen (momenteel) niet groter zijn dan 8 MB.";
		}				
		if(empty($errors)==true){
			rename($crbinRoot."/crbin/excel/".$file_name, $crbinRoot."/crbin/backup/".$file_name_backup);
			move_uploaded_file($file_tmp,$crbinRoot."/crbin/excel/".$file_name);
			echo nl2br("De ExcelFinancieleTool update is geupload, de oude versie is geback-upt.\n");
			$database = new Database();
			$logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \'Excel Financiele Tool geupload.\', \''.$user->user_login.'\')';
			$database->query($logQuery);				
			$database->execute();				
		}else{
			print_r($errors);
		}
	}
	
		if(isset($_FILES['ExcelPAINTool'])){
		$errors= array();
		//$file_name = $_FILES['ExcelPAINTool']['name'];
		$file_name = 'clubredders-ept.xlsm';
		$file_name_backup = 'clubredders-ept'.time().'.xlsm';
		$file_size =$_FILES['ExcelPAINTool']['size'];
		$file_tmp =$_FILES['ExcelPAINTool']['tmp_name'];
		$file_type=$_FILES['ExcelPAINTool']['type'];   
		$file_ext=strtolower(end(explode('.',$_FILES['ExcelPAINTool']['name'])));
		
		$expensions= array("xlsm");	
		if(in_array($file_ext,$expensions)=== false){
			$errors[]="De gekozen Excel PAIN Tool update is geen XLSM. Alleen updates op basis van XLSM bestanden zijn toegestaan.";
		}
		if($file_size > 8388608){
			$errors[]="Excel PAIN Tool update bestanden mogen (momenteel) niet groter zijn dan 8 MB.";
		}				
		if(empty($errors)==true){
			rename($crbinRoot."/crbin/excel/".$file_name, $crbinRoot."/crbin/backup/".$file_name_backup);
			move_uploaded_file($file_tmp,$crbinRoot."/crbin/excel/".$file_name);
			echo nl2br("De ExcelPAINTool update is geupload, de oude versie is geback-upt.\n");
			$database = new Database();
			$logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \'Excel PAIN Tool geupload.\', \''.$user->user_login.'\')';
			$database->query($logQuery);				
			$database->execute();				
		}else{
			print_r($errors);
		}
	}

?>
<?php include __DIR__ . '/../../header.php'; ?>
<main>
<div class="container">
		<div class="section">
			<!---// <p>Deze IDM actie is gelogd op naam van <?php echo $user->user_firstname . " " . $user->user_lastname;?>.</p>--->
			<p>Onderstaande tools maken gebruik van CSV bestanden die door Club.Redders worden gegenereerd.<br>Deze tools worden niet door de ontwikkelaars van Club.Redders ondersteund.</p>
			<!---// BEGIN stuff here for all roles--->
			<?php if( /***current_user_can('subscriber') || ***/ current_user_can('contributor') ||  current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') ) {  ?>
			<a href="sdem-tools-downloader.php?filename=clubredders-edt.xlsb">Start de Excel Competenties support Tool</a> en download <a href="sdem-csv-competenties-support-tool.php">het CSV bronbestand.</a><br>
			<a href="sdem-tools-downloader.php?filename=clubredders-eft.xlsb">Start de Excel Boekhouding Tool</a> en download <a href="sdem-csv-boekhouding-tool.php">het CSV bronbestand.</a><br>
			<a href="sdem-tools-downloader.php?filename=clubredders-eit.xlsb">Start de Excel Jaarrooster Tool</a> en download <a href="sdem-csv-jaarrooster-tool.php">het CSV bronbestand.</a><br>
			<!---// BEGIN stuff here for all editors and admins--->
			<?php if( current_user_can('editor') || current_user_can('administrator') ) {  ?>
				<a href="sdem-tools-downloader.php?filename=clubredders-ept.xlsm">Start de Excel Contributie Tool</a> en download <a href="sdem-csv-contributie-tool.php">het CSV bronbestand.</a><br>
			<!---// END stuff here for all editors and admins--->
			<?php } ?>
			<p>Aanbevolen systeemeisen Excel Tools:</p>
			<ul type="square">
				<li><a href="https://www.microsoft.com/nl-nl/software-download/windows10" target="_blank" style="text-decoration: none">Windows 10 versie 1909</a> en <a href="https://products.office.com/nl-nl/try" target="_blank" style="text-decoration: none">Excel 2019</a></li>
				<!---// <li><a href="https://www.microsoft.com/nl-nl/windows/microsoft-edge" target="_blank" style="text-decoration: none">Microsoft Edge versie 80</a></li>--->
			</ul>
			<!---// END stuff here for all roles--->
			<?php } ?>
			<!---// BEGIN stuff here for all contributors, authors, editors or admins--->
			<?php if( current_user_can('contributor') ||  current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') ) {  ?>
			<p>LET OP: Met onderstaande velden worden de Excel tools geupdate.</p>
				<form action="" method="POST" enctype="multipart/form-data">
					<!--<div class="file-field input-field">
						<div class="btn">
							<span>clubredders-ebt.xlsb</span>
							<input type="file" name="ExcelBewakingsTool">
						</div>
						<div class="file-path-wrapper">
							<input class="file-path validate" type="text">
						</div>
					</div>-->
					<div class="file-field input-field">
						<div class="btn">
							<span>Competenties support Tool</span>
							<input type="file" name="ExcelDiplomacheckTool">
						</div>
						<div class="file-path-wrapper">
							<input class="file-path validate" type="text">
						</div>
					</div>
					<!---// BEGIN stuff here for all editors and admins--->
					<?php if( current_user_can('editor') || current_user_can('administrator') ) {  ?>
					<div class="file-field input-field">
						<div class="btn">
							<span>Boekhouding Tool</span>
							<input type="file" name="ExcelFinancieleTool">
						</div>
						<div class="file-path-wrapper">
							<input class="file-path validate" type="text">
						</div>
					</div>
					<!---// END stuff here for all editors and admins--->
					<?php } ?>
					<div class="file-field input-field">
						<div class="btn">
							<span>Jaarrooster Tool</span>
							<input type="file" name="ExcelInschrijvingsTool">
						</div>
						<div class="file-path-wrapper">
							<input class="file-path validate" type="text">
						</div>
					</div>
					<!---// BEGIN stuff here for all editors and admins--->
					<?php if( current_user_can('editor') || current_user_can('administrator') ) {  ?>
					<div class="file-field input-field">
						<div class="btn">
							<span>Contributie Tool</span>
							<input type="file" name="ExcelPAINTool">
						</div>
						<div class="file-path-wrapper">
							<input class="file-path validate" type="text">
						</div>
					</div>
					<!---// END stuff here for all editors and admins--->
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
<?php include '../../footer.php'; ?>