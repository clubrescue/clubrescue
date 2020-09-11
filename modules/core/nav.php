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

	<!---// BEGIN stuff here for all editors or admins--->
	<?php if (current_user_can('administrator') || current_user_can('editor') ||$user->user_login === 'D271RRP') {  ?>
	<li style="padding-left: 16px;">
		<div class="collapsible-header"><i class="material-icons">admin_panel_settings</i>Beheer</div>
		<div class="collapsible-body">
			<ul>
				<li><a href="<?php echo $url; ?>/clubredders/modules/core/crwp-settings.php"><i class="material-icons">design_services</i>C.R-WP settings</a></li>
				<li><a href="<?php echo $url; ?>/clubredders/modules/core/wp-sso-settings.php"><i class="material-icons">login</i>WordPress SSO settings</a></li>
				<li><a href="<?php echo $url; ?>/clubredders/modules/core/mf-autorisaties.php"><i class="material-icons">supervisor_account</i>MachForm autorisaties</a></li>
				<li><a href="<?php echo $url; ?>/clubredders/modules/core/aadusersattributes.php"><i class="material-icons">edit_attributes</i>AAD attributen SOLL</a></li>
				<!---// BEGIN stuff here for all admins--->
				<?php if (current_user_can('administrator') || $user->user_login === 'D271RRP') {  ?>
				<li><a href="<?php echo $url; ?>/clubredders/modules/core/log.php"><i class="material-icons">code</i>Logging</a></li>
				<li><a href="<?php echo $url; ?>/clubredders/modules/core/crversion.php"><i class="material-icons">integration_instructions</i>Versie</a></li>
				<?php } ?>
				<!---// END stuff here for all admins--->
			</ul>
		</div>
	</li>
	<?php } ?>
	<!---// END stuff here for all editors or admins--->