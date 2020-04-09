<?php
	/*** Club.Redders - PAGE TEMPLATE TYPE - MaterializeCSS - v0.8.6
	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();

?>
<?php include 'header.php'; ?>
<main>

  <div class="container">
    <div class="section">

		<!--   Test include GitHub version check   -->
		<?php
			$SVT = file_get_contents("https://raw.githubusercontent.com/clubrescue/clubrescue/master/util/svt.ini");
	
			$CR_RUNNING_BE_VERSION				= "0.8.6"; // The version that is running on this server.
			$CR_RUNNING_DB_VERSION      		= "0.8.6"; // The version of the connected database.
			
			parse_str($SVT, $SVToutput);
			
			echo 'CR_RUNNING_BE_VERSION = '.$CR_RUNNING_BE_VERSION.'<br>'; // CR_RUNNING_BE_VERSION
			echo 'CR_RUNNING_DB_VERSION = '.$CR_RUNNING_DB_VERSION.'<br>'; // CR_RUNNING_DB_VERSION
			
			echo 'CR_CURRENT_BE_VERSION = '.$SVToutput['CR_CURRENT_BE_VERSION'].'<br>';  // CR_CURRENT_BE_VERSION
			echo 'CR_CURRENT_DB_VERSION = '.$SVToutput['CR_CURRENT_DB_VERSION'].'<br>'; // CR_CURRENT_DB_VERSION
			echo 'CR_REQUIRES_PREVIOUS_BE_VERSION = '.$SVToutput['CR_REQUIRES_PREVIOUS_BE_VERSION'].'<br>'; // CR_REQUIRES_PREVIOUS_BE_VERSION
			echo 'CR_REQUIRES_PREVIOUS_DB_VERSION = '.$SVToutput['CR_REQUIRES_PREVIOUS_DB_VERSION'].'<br>'; // CR_REQUIRES_PREVIOUS_DB_VERSION
		?>

    </div>
    <br><br>

    <div class="section">

    </div>
  </div>
</main>
<?php include 'footer.php'; ?>