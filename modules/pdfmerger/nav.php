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
	<li><a href="<?php echo $url; ?>/clubredders/modules/pdfmerger/index.php"><i class="material-icons">picture_as_pdf</i>PDF-merger</a></li>
	<?php } ?>
	<!---// END stuff here for all contributors, authors, editors or admins--->