<?php
	/*** Club.Redders - PAGE TEMPLATE TYPE - InternalDataMutations - v0.8.5
	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/
	login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();
	
	// Include database class
	require_once 'util/utility.class.php';	
	require_once 'util/database.class.php';	
	
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 'Off');  //On or Off
	
	if(isset($_FILES['EDP-Pasfoto'])){
		$errors= array();
		$file_name = basename($_FILES["EDP-Pasfoto"]["name"]);
		$file_name_backup = substr(basename($_FILES["EDP-Pasfoto"]["name"]), -11, 7).time().'.jpg';
		$file_size =$_FILES['EDP-Pasfoto']['size'];
		$file_tmp =$_FILES['EDP-Pasfoto']['tmp_name'];
		$file_type=$_FILES['EDP-Pasfoto']['type'];   
		$file_ext=strtolower(end(explode('.',$_FILES['EDP-Pasfoto']['name'])));
		$expensions= array("jpg");

		if(in_array($file_ext,$expensions)=== false){
			$errors[]="De gekozen pasfoto is geen jpg. Alleen jpg bestanden zijn toegestaan.";
		}
		if($file_size > 100000){
			$errors[]="Pasfotos mogen (momenteel) niet groter zijn dan 100 KB.";
		}				
		if(empty($errors)==true){
			echo rename("/srv/home/trbnu/domains/trb.nu/crbin/pasfotos/".$file_name, "/srv/home/trbnu/domains/trb.nu/crbin/backup/".$file_name_backup);
			move_uploaded_file($file_tmp,"/srv/home/trbnu/domains/trb.nu/crbin/pasfotos/".$file_name);
			echo nl2br("De gekozen pasfoto is geupload, de oude pasfoto is geback-upt.\n");
			$database = new Database();
			$logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \'De gekozen pasfoto '.$file_name.' is geupload.\', \''.$user->user_login.'\')';
			$database->query($logQuery);				
			$database->execute();				
			
		}else{
			print_r($errors);
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
			<p>LET OP: Met onderstaande velden kunnen pasfoto's<br>worden geupdate in de website, bestaande pasfotos<br>worden overschreven, relatienr. in hoofdletters, extensie in kleine letters.</p>
				<form action="" method="POST" enctype="multipart/form-data">
					<div class="file-field input-field">
						<div class="btn">
							<span>[RELATIENR].jpg</span>
							<input type="file" name="EDP-Pasfoto">
						</div>
						<div class="file-path-wrapper">
							<input class="file-path validate" type="text">
						</div>
					</div>
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