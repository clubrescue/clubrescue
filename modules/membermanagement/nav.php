<?php
    /*** Club.Redders - PAGE TEMPLATE TYPE - MaterializeCSS - v0.8.6
    /*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
    require_once( __DIR__ . "/../../wp-authenticate.php");
    /*** REQUIRE USER AUTHENTICATION ***/
    login();
    /*** RETRIEVE LOGGED IN USER INFORMATION ***/
    $user = wp_get_current_user();
    $url = get_site_url();
?>

	<!---// BEGIN stuff here for all contributors, authors, editors or admins--->
	<?php if (current_user_can('contributor') ||  current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator')) {  ?>
	<li style="padding-left: 16px;">
		<div class="collapsible-header"><i class="material-icons">how_to_reg</i>Ledenadministratie</div>
		<div class="collapsible-body">
			<ul>
				<li><a href="<?php echo $url; ?>/clubredders/modules/membermanagement/photograph/pasfotos.php"><i class="material-icons">portrait</i>Pasfotos wijzigen</a></li> 
				<li><a href="<?php echo $url; ?>/clubredders/modules/membermanagement/controllers/nieuwlid.controller.php"><i class="material-icons">create</i>Nieuw lid opvoeren</a></li>
				<li><a href="<?php echo $url; ?>/clubredders/modules/membermanagement/controllers/updateleden.controller.php"><i class="material-icons">update</i>Updaten van een lid</a></li>
				<li><a href="<?php echo $url; ?>/clubredders/modules/membermanagement/controllers/deletelid.controller.php"><i class="material-icons">delete</i>Verwijderen van een lid</a></li>
				<li><a href="<?php echo $url; ?>/clubredders/modules/membermanagement/controllers/syncleden.controller.php"><i class="material-icons">sync</i>Syncroniseer O365</a></li>
			</ul>
		</div>
	</li>
	<?php } ?>
	<!---// END stuff here for all contributors, authors, editors or admins--->