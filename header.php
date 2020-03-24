<?php
	/*** Club.Redders - PAGE TEMPLATE TYPE - MaterializeCSS - v0.8.6
	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/$user = wp_get_current_user();
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 'On');  //On or Off
?>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
  <meta http-equiv="cache-control" content="max-age=0" />
  <meta http-equiv="cache-control" content="no-cache" />
  <meta http-equiv="expires" content="0" />
  <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
  <meta http-equiv="pragma" content="no-cache" />
  
  <!-- Mobile web-app feature  -->
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-title" content="Club.Redders">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <title>Club.Redders</title>
  <link rel="icon" type="image/x-icon" href="favicon.ico">

  <!-- CSS  -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="/clubredders/css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
  <link href="/clubredders/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
  <!--<link href="https://static2.sharepointonline.com/files/fabric/office-ui-fabric-core/11.0.0/css/fabric.min.css" rel="stylesheet"/>-->

  <!-- IOS WebApp fix  -->  
	<script type="text/javascript">
    if(("standalone" in window.navigator) && window.navigator.standalone){
    var noddy, remotes = false;
    document.addEventListener('click', function(event) {
    noddy = event.target;
    while(noddy.nodeName !== "A" && noddy.nodeName !== "HTML") {
    noddy = noddy.parentNode;
    }
    if('href' in noddy && noddy.href.indexOf('http') !== -1 && (noddy.href.indexOf(document.location.host) !== -1 || remotes))
    {
    event.preventDefault();
    document.location.href = noddy.href;
    }
    },false);
    }
  </script>
  
</head>
<body>
<header>
<div class="container cr-header">
	<div class="navbar-fixed">
		<nav class="light-blue lighten-1" role="navigation">
			<div class="nav-wrapper container"><a id="logo-container" href="https://trb.nu/clubredders" class="brand-logo center">Club.Redders</a>
				<a href="#" data-activates="nav-mobile" class="button-collapse show-on-large"><i class="material-icons">menu</i></a>
				<ul class="right hide-on-med-and-down">
					<li><a href="https://trb.nu/clubredders">Welkom <?php echo $user->user_firstname . ""?></a></li>
				</ul>
			</div>
		</nav>
	</div>
	<ul id="nav-mobile" class="side-nav">
	<!---// BEGIN stuff here for all roles--->
		<?php if( current_user_can('subscriber') ||  current_user_can('contributor') ||  current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') ) {  ?>
		<li><a href="/clubredders/mijncr.php"><i class="material-icons">person</i>Mijn Club.Redders</a></li>
		<!---// END stuff here for all roles--->
		<?php } ?>
	<!---// BEGIN stuff here for all contributors, authors, editors or admins--->
		<?php if( current_user_can('contributor') ||  current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') ) {  ?>
		<li><a href="/clubredders/modules/ledenbeheer/"><i class="material-icons">account_box</i>Ledenadministratie</a></li>
		<li><a href="/clubredders/roosteren.php"><i class="material-icons">insert_invitation</i>Week rooster maken</a></li>
		<li><a href="/clubredders/rooster.php"><i class="material-icons">event_note</i>Week rooster inzien</a></li>
		<li><a href="/clubredders/kaart.php"><i class="material-icons">assignment_ind</i>Competentiekaarten</a></li>
		<li><a href="/clubredders/idm-pasfotos.php"><i class="material-icons">portrait</i>Pasfotos module</a></li>
		<li><a href="/clubredders/edm-maillijsten.php"><i class="material-icons">contact_mail</i>Maillijsten module</a></li>
		<li><a href="/clubredders/idm-dataimport.php"><i class="material-icons">file_upload</i>Data import module</a></li>
		<li><a href="https://trb.nu/clubforms/index.php" target="_blank"><i class="material-icons">question_answer</i>Club.Forms</a></li>
		<li><a href="https://trb.nu/clubkee" target="_blank"><i class="material-icons">security</i>Club.Keys</a></li>
		<li><a href="https://trb.nu/clubredders/merger/index.php"><i class="material-icons">picture_as_pdf</i>Club.PDF-merger</a></li>
		<!--- Excel tools module is zichtbaar voor kader én niet kader dat dit seizoen ASC of 809 is. --->
		<?php
		/*
		<!--- Excel tools module is zichtbaar voor kader én niet kader dat dit seizoen ASC of 809 is. --->
		<?php
		$where1 = 'BWK'.Date("Y").'%';
		$where2 = 'EXAMEN'.Date("Y").'%';
		$sqlActiviteiten = "SELECT count(distinct `Activiteit`) as count FROM  `cr_activiteiten` WHERE  `Relatienr` =  '$user->user_login' AND  `Locatie` IN ('OPL',  'ASC') AND (`Activiteit` LIKE '$where1' or `Activiteit` LIKE '$where2')";			
		$databaseActiviteiten = new Database();
		$databaseActiviteiten->query($sqlActiviteiten);	
		$activiteiten = $databaseActiviteiten->resultset();	
		
		if ( current_user_can('contributor') || current_user_can('editor') || current_user_can('administrator')) {	?>
		<?php }elseif ( current_user_can('subscriber') &&  $activiteiten[0]['count'] > 0  ) { ?>
			<li><a href="roosteren.php"><i class="material-icons">insert_invitation</i>Week rooster maken</a></li>
			<li><a href="rooster.php"><i class="material-icons">event_note</i>Week rooster inzien</a></li>
			<li><a href="kaart.php"><i class="material-icons">assignment_ind</i>Competentiekaarten</a></li>
		<?php } ?>
		*/	
		//} 
		?>
		<li><a href="/clubredders/support.php"><i class="material-icons">help<!---//_outline---></i>Support module</a></li>
		<!---// END stuff here for all contributors, authors, editors or admins--->
		<?php } 
			echo '<li><a href="idm-exceltools.php"><i class="material-icons">get_app</i>SDEM modules</a></li>';		
		?>
	<!---// BEGIN stuff here for all authors, editors or admins--->
		<?php if( current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator') ) {  ?>
		<!---// <li><a href="#"><i class="material-icons">code</i>Module naam...</a></li>--->
		<!---// END stuff here for all authors, editors or admins--->
		<?php } ?>
	<!---// BEGIN stuff here for all editors and admins--->
		<?php if( current_user_can('editor') || current_user_can('administrator') ) {  ?>
	<!---// <li><a href="#"><i class="material-icons">code</i>Module naam...</a></li>--->
		<!---// END stuff here for all editors and admins--->
		<?php } ?>
	<!---// BEGIN stuff here for all admins--->
		<?php if( current_user_can('administrator') || current_user_can('editor') ||$user->user_login === 'D271RRP') {  ?>
		<li><a href="/clubredders/contributie.php"><i class="material-icons">euro_symbol</i>Contributie</a></li>
		<li><a href="/clubredders/boekhouding.php"><i class="material-icons">import_contacts</i>Boekhouding</a></li>
		<li><a href="/clubredders/log.php"><i class="material-icons">code</i>Logging</a></li>
		<!---// <li><a href="#"><i class="material-icons">settings</i>Settings</a></li>--->
		<!---// END stuff here for all admins--->
		<?php } ?>
		<li><a href="https://trb.nu/wp-login.php?action=logout&redirect_to=https://trb.nu/clubredders"><i class="material-icons">exit_to_app</i>Uitloggen</a></li>
	</ul>
</div>
</header>