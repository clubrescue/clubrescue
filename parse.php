<?php
	/*** Club.Redders - PAGE TEMPLATE TYPE - MaterializeCSS - v0.8.6
	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();
	
include('util/parsedown.php');
include 'header.php';
$html = file_get_contents('./support/externalmanual.md');
$Parsedown = new Parsedown();
?>
<main>
  <div class="section no-pad-bot" id="index-banner">
    <div class="container">
<?php echo $Parsedown->text($html); ?>
	</div>
  </div>
</main>
<?php include 'footer.php'; ?>