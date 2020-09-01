<?php
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
	
?>
<?php include __DIR__ . '/../../header.php'; ?>
<main>
<div class="container">
		<div class="section">
			<!---// BEGIN stuff here for all contributors, authors, editors or admins--->
			<?php if(current_user_can('editor') || current_user_can('administrator') ) {  ?>
				<?php
			
				//AANPASSEN: CR WAARDEN GAAN OVER DE MF WAARDE BIJ FOUTIVE USERNAMES.
				$tableQueryLedenUsers = 'SELECT cr.`Relatienr`, cr.`VolledigeNaam`, cr.`Lidstatus`, cr.`Verenigingsfunctie`, apu.`user_id`, apu.`status`, apu.`user_fullname`, apu.`user_email`, apu.`tsv_enable`, apu.`user_password`, apu.`priv_new_forms`, apu.`priv_new_themes`, apu.`priv_administer` FROM `u70472p67165_clubredders`.`cr_leden` AS cr RIGHT JOIN `u70472p67165_clubforms`.`ap_users` AS apu ON cr.`Relatienr` = LEFT(apu.`user_email`, 7)';
				$tableQueryUsers = 			'SELECT `user_id`, `status`, `user_fullname`, `user_email`, `tsv_enable`, `user_password`, `priv_new_forms`, `priv_new_themes`, `priv_administer` FROM `ap_users` ';
				$tableQueryPermissions =	'SELECT `user_id` as `priv_user_id`, `form_id` as `priv_form_id`, `edit_form`, `edit_report`, `edit_entries`, `view_entries` FROM `ap_permissions` ';
				$tableQueryForms =			'SELECT `form_id`, `form_active`, `form_name`, `form_tags`, `form_description` FROM `ap_forms` ';
											//Active users are 0 or 1, suspended users are 2.
											//Active forms are 1, time-suspended forms are 0. Unkown forms are 2 and deleted forms are 9.
				$tableQueryMachFormAuth = 'SELECT apu.`user_id`,  app.`user_id` AS `priv_user_id`, apu.`status`, cr.`Relatienr`, cr.`VolledigeNaam`, cr.`Lidstatus`, cr.`Verenigingsfunctie`, apu.`user_fullname`, apu.`user_email`, apu.`tsv_enable`, NULL AS `user_password`, apu.`priv_new_forms`, apu.`priv_new_themes`, apu.`priv_administer`, apf.`form_id`, app.`form_id` AS `priv_form_id`, apf.`form_active`, apf.`form_name`, apf.`form_tags`, apf.`form_description`, app.`edit_form`, app.`edit_report`, app.`edit_entries`, app.`view_entries` FROM `u70472p67165_clubforms`.`ap_permissions` AS app INNER JOIN `u70472p67165_clubforms`.`ap_users` AS apu ON app.`user_id` = apu.`user_id` INNER JOIN `u70472p67165_clubforms`.`ap_forms` AS apf ON app.`form_id` = apf.`form_id` LEFT JOIN `u70472p67165_clubredders`.`cr_leden` AS cr ON LEFT(apu.`user_email`, 7) = cr.`Relatienr` ORDER BY `user_id` ';
				
				$CRdatabase = new Database();
				$MFdatabase = new Database("MF");
				
				$CRdatabase->query($tableQueryLedenUsers);	
				$tableResultLedenUsers = $CRdatabase->resultset();

				$MFdatabase->query($tableQueryUsers);	
				$tableResultUsers = $MFdatabase->resultset();
				
				$MFdatabase->query($tableQueryPermissions);	
				$tableResultPermissions = $MFdatabase->resultset();
				
				$MFdatabase->query($tableQueryForms);	
				$tableResultForms = $MFdatabase->resultset();
				
				//Full auth table
				$CRdatabase->query($tableQueryMachFormAuth);	
				$tableResultMachFormAuth = $CRdatabase->resultset();
				
				echo '<table id=MachFormAuth>';
				echo '<tr>';
					echo '<td>apu_user_id</td>';
					echo '<td>app_priv_user_id</td>';
					echo '<td>apu_status</td>';
					echo '<td>cr_Relatienr</td>';
					echo '<td>cr_VolledigeNaam</td>';
					echo '<td>cr_Lidstatus</td>';
					echo '<td>cr_Verenigingsfunctie</td>';
					echo '<td>apu_user_fullname</td>';
					echo '<td>apu_user_email</td>';
					echo '<td>apu_tsv_enable</td>';
					echo '<td>apu_user_password</td>';
					echo '<td>apu_priv_new_forms</td>';
					echo '<td>apu_priv_new_themes</td>';
					echo '<td>apu_priv_administer</td>';
					echo '<td>apf_form_id</td>';
					echo '<td>app_priv_form_id</td>';
					echo '<td>apf_form_active</td>';
					echo '<td>apf_form_name</td>';
					echo '<td>apf_form_tags</td>';
					echo '<td>apf_form_description</td>';
					echo '<td>app_edit_form</td>';
					echo '<td>app_edit_report</td>';
					echo '<td>app_edit_entries</td>';
					echo '<td>app_view_entries</td>';
				echo '</tr>';
				foreach ($tableResultMachFormAuth as $key => $value) {
					echo '<tr>';
						echo '<td>' . $value["user_id"] . '</td>';
						echo '<td>' . $value["priv_user_id"] . '</td>';
						echo '<td>' . $value["status"] . '</td>';
						echo '<td>' . $value["Relatienr"] . '</td>';
						echo '<td>' . $value["VolledigeNaam"] . '</td>';
						echo '<td>' . $value["Lidstatus"] . '</td>';
						echo '<td>' . $value["Verenigingsfunctie"] . '</td>';
						echo '<td>' . $value["user_fullname"] . '</td>';
						echo '<td>' . $value["user_email"] . '</td>';
						echo '<td>' . $value["tsv_enable"] . '</td>';
						echo '<td>' . $value["user_password"] . '</td>';
						echo '<td>' . $value["priv_new_forms"] . '</td>';
						echo '<td>' . $value["priv_new_themes"] . '</td>';
						echo '<td>' . $value["priv_administer"] . '</td>';
						echo '<td>' . $value["form_id"] . '</td>';
						echo '<td>' . $value["priv_form_id"] . '</td>';
						echo '<td>' . $value["form_active"] . '</td>';
						echo '<td>' . $value["form_name"] . '</td>';
						echo '<td>' . $value["form_tags"] . '</td>';
						echo '<td>' . $value["form_description"] . '</td>';
						echo '<td>' . $value["edit_form"] . '</td>';
						echo '<td>' . $value["edit_report"] . '</td>';
						echo '<td>' . $value["edit_entries"] . '</td>';
						echo '<td>' . $value["view_entries"] . '</td>';
					echo '</tr>';
				}
				echo '</table>';
				/*
				echo '<table id=cr_mf_ap_users>';
				echo '<tr>';
					echo '<td>cr_Relatienr</td>';
					echo '<td>cr_VolledigeNaam</td>';
					echo '<td>cr_Lidstatus</td>';
					echo '<td>cr_Verenigingsfunctie</td>';
					echo '<td>mf_user_id</td>';
					echo '<td>mf_status</td>';
					echo '<td>mf_user_fullname</td>';
					echo '<td>mf_user_email</td>';
					echo '<td>mf_tsv_enable</td>';
					echo '<td>mf_mf_user_password</td>';
					echo '<td>mf_priv_new_forms</td>';
					echo '<td>mf_priv_new_themes</td>';
					echo '<td>mf_priv_administer</td>';
				echo '</tr>';
				foreach ($tableResultLedenUsers as $key => $value) {
					echo '<tr>';
						echo '<td>'.$value["Relatienr"] . '</td>';
						echo '<td>'.$value["VolledigeNaam"] . '</td>';
						echo '<td>'.$value["Lidstatus"] . '</td>';
						echo '<td>'.$value["Verenigingsfunctie"] . '</td>';
						echo '<td>'.$value["user_id"] . '</td>';
						echo '<td>'.$value["status"] . '</td>';
						echo '<td>'.$value["user_fullname"] . '</td>';
						echo '<td>'.$value["user_email"] . '</td>';
						echo '<td>'.$value["tsv_enable"] . '</td>';
						echo '<td>user_password</td>';
						echo '<td>'.$value["priv_new_forms"] . '</td>';
						echo '<td>'.$value["priv_new_themes"] . '</td>';
						echo '<td>'.$value["priv_administer"] . '</td>';
					echo '</tr>';
				}
				echo '</table>';
				
				echo" <table id=mf_ap_permissions>\r\n";
				echo '<tr>';
					echo '<td>priv_user_id</td>';
					echo '<td>priv_form_id</td>';
					echo '<td>edit_form</td>';
					echo '<td>edit_report</td>';
					echo '<td>edit_entries</td>';
					echo '<td>view_entries</td>';
				echo '</tr>';
				foreach ($tableResultPermissions as $key => $value) {
					echo '<tr>';
						echo '<td>'.$value["priv_user_id"] . '</td>';
						echo '<td>'.$value["priv_form_id"] . '</td>';
						echo '<td>'.$value["edit_form"] . '</td>';
						echo '<td>'.$value["edit_report"] . '</td>';
						echo '<td>'.$value["edit_entries"] . '</td>';
						echo '<td>'.$value["view_entries"] . '</td>';
					echo '</tr>';
				}
				echo '</table>';
				
				echo" <table id=mf_ap_forms>\r\n";
				echo '<tr>';
					echo '<td>form_id</td>';
					echo '<td>form_active</td>';
					echo '<td>form_name</td>';
					echo '<td>form_tags</td>';
					echo '<td>form_description</td>';
				echo '</tr>';
				foreach ($tableResultForms as $key => $value) {
					echo '<tr>';						
						echo '<td>'.$value["form_id"] . '</td>';
						echo '<td>'.$value["form_active"] . '</td>';
						echo '<td>'.$value["form_name"] . '</td>';
						echo '<td>'.$value["form_tags"] . '</td>';
						echo '<td>'.$value["form_description"] . '</td>';
					echo '</tr>';
				}
				echo '</table>';
				*/
				?>
			<!-- END stuff here for all contributors, authors, editors or admins -->
			<?php } ?>
		</div>
	</div>
</main>
<?php include '../../footer.php'; ?>