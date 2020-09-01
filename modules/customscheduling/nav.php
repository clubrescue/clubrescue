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
		<div class="collapsible-header"><i class="material-icons">event_available</i>Weekroosters</div>
		<div class="collapsible-body">
			<ul>
				<li><a href="<?php echo $url; ?>/clubredders/modules/customscheduling/create/roosteren.php"><i class="material-icons">insert_invitation</i>Week rooster maken</a></li>
				<li><a href="<?php echo $url; ?>/clubredders/modules/customscheduling/read/rooster.php"><i class="material-icons">event_note</i>Week rooster inzien</a></li>
			</ul>
		</div>
	</li>
	<?php } ?>
	<!---// END stuff here for all contributors, authors, editors or admins--->