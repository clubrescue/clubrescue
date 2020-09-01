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
	include '../../util/msgraph/msgraph.class.php';
	
	// Declare Namespace for MS Graph Model
    use Microsoft\Graph\Graph;
	use Microsoft\Graph\Model;
	use GuzzleHttp\Client;
	use GuzzleHttp\Psr7;
	
?>
<?php include __DIR__ . '/../../header.php'; ?>
<main>
<div class="container">
		<div class="section">
			<!---// BEGIN stuff here for all contributors, authors, editors or admins--->
			<?php if(current_user_can('editor') || current_user_can('administrator') ) {  ?>
				<?php

				$tableQuery = 'SELECT `option_id`, `option_name`, `option_value`, `autoload` FROM `u70472p67165_wp282`.`wpwx_options` WHERE `option_name` = \'crwp_settings\' '; //`option_value`, 
				
				$database = new Database("WP");
				$database->query($tableQuery);	
				$tableResult = $database->resultset();

    //$option_value = get_option( 'aadsso_settings' );	//Loading data over the WP database connection results in an empty array. Deprecated by the line below;
	$option_value = unserialize($tableResult[0]["option_value"]); //Loading data over the CR database using the database name in the SELECT FROM works. Use PHP unserialize to render the option_value as an array.
		echo 'Club.Rescue-WP' . '<br>';
		echo 'Current WordPress configuration for Club.Rescue-WP.' . '<br>';
		echo 'General' . '<br>';
		echo' <table id=aadsso_settings>';
	//		echo '<tr>';
	//			echo '<td>option_id</td>';
	//			echo '<td>'.$tableResult[0]["option_id"]. '</td>';
	//			//echo '<td>option_id is a field from the WP_Options table in addition to the option_value field.</td>';
	//		echo '</tr>';
	//		echo '<tr>';
	//			echo '<td>option_name</td>';
	//			echo '<td>'.$tableResult[0]["option_name"]. '</td>';
	//			//echo '<td>option_name is a field from the WP_Options table in addition to the option_value field.</td>';
	//		echo '</tr>';
			echo '<tr>';
				echo '<td>My Club.Rescue pages</td>';
				echo '<td>'.$option_value["crwp_pages"]. '</td>';
				//echo '<td>This specifies the WordPress pages that will contain the shortcodes provided by this plugin. Only pages listed here will provide working shortcodes cause only these pages will trigger the O365 authentication for Club.Rescue. Provide pages by using there page slug and seperate multiple slug\'s with , for example my-cr or my-cr, my-extra-personal-page.</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Whitelabel (OTAP)</td>';
				echo '<td>'.$option_value["crwp_otap"]. '</td>';
				//echo '<td>This specifies the Club.Rescue installation folder. You can change that folder\'s name to whitelabel the tool. A whitelabel is only usefull for users that will access Club.Rescue directly. As a alternative you can use the otap attribute in the shortcode to trigger a second (testing) C.R installation as a source.</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Default source</td>';
				//echo '<td>'.$option_value["crwp_source"]. '</td>';
					if ($option_value["crwp_source"] === "mycr-attributes") {
						echo '<td>label attributes</td>';
					}elseif ($option_value["crwp_source"] === "mycr-activities"){
						echo '<td>label activities</td>';
					}elseif ($option_value["crwp_source"] === "mycr-internalcertifications"){
						echo '<td>label internalcertifications</td>';
					}elseif ($option_value["crwp_source"] === "mycr-externalcertifications"){
						echo '<td>label externalcertifications</td>';
					}elseif ($option_value["crwp_source"] === "mycr-externalfunctions"){
						echo '<td>label externalfunctions</td>';
					}elseif ($option_value["crwp_source"] === "mycr-documents"){
						echo '<td>label documents</td>';
					}elseif ($option_value["crwp_source"] === "mycr-actions"){
						echo '<td>label actions</td>';
					}elseif ($option_value["crwp_source"] === "mycr-expenses"){
						echo '<td>label expenses</td>';
					}else {
						echo '<td>ERROR: No default has been selected!</td>';
					}
				//echo '<td>This specifies the My Club.Rescue submodule to use by default when not using the source attribute in the shortcode.</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Default variable</td>';
				//echo '<td>'.$option_value["crwp_variable"]. '</td>';
					if ($option_value["crwp_variable"] === "lidTable") {
						echo '<td>label lidTable</td>';
					}elseif ($option_value["crwp_variable"] === "activiteitenTable"){
						echo '<td>label activiteitenTable</td>';
					}elseif ($option_value["crwp_variable"] === "verenigingsDiplomasTable"){
						echo '<td>label verenigingsDiplomasTable</td>';
					}elseif ($option_value["crwp_variable"] === "bondsDiplomasTable"){
						echo '<td>label bondsDiplomasTable</td>';
					}elseif ($option_value["crwp_variable"] === "bondsFunctiesTable"){
						echo '<td>label bondsFunctiesTable</td>';
					}elseif ($option_value["crwp_variable"] === "documentlist_Upload OR documentlist_Dynamicname"){
						echo '<td>label documentlist_Upload OR documentlist_Dynamicname</td>';
					}elseif ($option_value["crwp_variable"] === "acties_wijzien OR acties_strandbewaking OR acties_kader"){
						echo '<td>label acties_wijzien OR acties_strandbewaking OR acties_kader</td>';
					}elseif ($option_value["crwp_variable"] === "InkopenTable OR ReiskostenTable OR OvertochtenTable"){
						echo '<td>label InkopenTable OR ReiskostenTable OR OvertochtenTable</td>';
					}else {
						echo '<td>ERROR: No default has been selected!</td>';
					}
				//echo '<td>This specifies the default variable to load from the My Club.Rescue submodule used in the source attribute.</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Error message</td>';
				echo '<td>'.$option_value["crwp_errormessage"]. '</td>';
				//echo '<td>This specifies the error message which will be displayed if the My Club.Rescue pages cannot be loaded/displayed.</td>';
			echo '</tr>';
		echo '</table>';
		echo 'Advanced' . '<br>';
		echo' <table id=aadsso_settings_advanced>';
			echo '<tr>';
				echo '<td>Club.Rescue link</td>';
				//echo '<td>'.$option_value["crwp_links"]. '</td>';
					if ($option_value["crwp_links"] === "true" ) {
						echo '<td>true</td>';
					}else {
						echo '<td>false</td>';
					}
				//echo '<td>This specifies if a link to your local Club.Rescue installation will be displayed in the plugins dashboard.</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Branch</td>';
				//echo '<td>'.$option_value["crwp_branch"]. '</td>';
					if ($option_value["crwp_branch"] === "master") {
						echo '<td>Master (default)</td>';
					}elseif ($option_value["crwp_branch"] === "dev"){
						echo '<td>Development (only for test environments)</td>';
					}
				//echo '<td>This specifies the branch that will be used to check and deploy for updates.</td>';
			echo '</tr>';
	//		echo '<tr>';
	//			echo '<td>autoload</td>';
	//			echo '<td>'.$tableResult[0]["autoload"]. '</td>';
	//			//echo '<td>autoload is a field from the WP_Options table in addition to the option_value field.</td>';
	//		echo '</tr>';
		echo '</table>';

				?>
			<!-- END stuff here for all contributors, authors, editors or admins -->
			<?php } ?>
		</div>
	</div>
</main>
<?php include '../../footer.php'; ?>