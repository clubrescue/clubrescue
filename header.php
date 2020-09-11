<?php
    /*** Club.Redders - PAGE TEMPLATE TYPE - MaterializeCSS - v0.8.6
    /*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
    require_once(__DIR__ . "/wp-authenticate.php");
    /*** REQUIRE USER AUTHENTICATION ***/
    login();
    /*** RETRIEVE LOGGED IN USER INFORMATION ***/
    $user = wp_get_current_user();
    $url = get_site_url();
?>
<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0" />
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
	<link rel="icon" type="image/x-icon"
		href="<?php echo $url; ?>/clubredders/favicon.ico">

	<!-- CSS  -->
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link href="<?php echo $url; ?>/clubredders/css/materialize.css"
		type="text/css" rel="stylesheet" media="screen,projection" />
	<link href="<?php echo $url; ?>/clubredders/css/style.css"
		type="text/css" rel="stylesheet" media="screen,projection" />
	<!--<link href="https://static2.sharepointonline.com/files/fabric/office-ui-fabric-core/11.0.0/css/fabric.min.css" rel="stylesheet"/>-->

	<!-- IOS WebApp fix  -->
	<script type="text/javascript">
		if (("standalone" in window.navigator) && window.navigator.standalone) {
			var noddy, remotes = false;
			document.addEventListener('click', function(event) {
				noddy = event.target;
				while (noddy.nodeName !== "A" && noddy.nodeName !== "HTML") {
					noddy = noddy.parentNode;
				}
				if ('href' in noddy && noddy.href.indexOf('http') !== -1 && (noddy.href.indexOf(document.location
						.host) !== -1 || remotes)) {
					event.preventDefault();
					document.location.href = noddy.href;
				}
			}, false);
		}
	</script>

</head>

<body>
	<header>
		<div class="container cr-header">
			<div class="navbar-fixed">
				<nav class="light-blue lighten-1" role="navigation">
					<div class="nav-wrapper container"><a id="logo-container"
							href="<?php echo $url; ?>/clubredders"
							class="brand-logo center">Club.Redders</a>
						<a href="#" data-activates="nav-mobile" class="button-collapse show-on-large"><i
								class="material-icons">menu</i></a>
						<ul class="right hide-on-med-and-down">
							<li><a
									href="<?php echo $url; ?>/clubredders">Welkom
									<?php echo $user->user_firstname;?></a>
							</li>
						</ul>
					</div>
				</nav>
			</div>
			
			<ul id="nav-mobile" class="side-nav">				
				<ul class="collapsible">
					<?php include __DIR__ . '/modules/mycr/nav.php'; ?>
					<?php include __DIR__ . '/modules/membermanagement/nav.php'; ?>
					<?php include __DIR__ . '/modules/diplomamanagement/nav.php'; ?>
					<?php include __DIR__ . '/modules/customscheduling/nav.php'; ?>
					<?php include __DIR__ . '/modules/mailinglists/nav.php'; ?>
					<?php include __DIR__ . '/modules/contribution/nav.php'; ?>
					<?php include __DIR__ . '/modules/accounting/nav.php'; ?>
					<?php include __DIR__ . '/modules/pdfmerger/nav.php'; ?>
					<?php include __DIR__ . '/modules/import/nav.php'; ?>
					<?php include __DIR__ . '/modules/sdem/nav.php'; ?>
					<?php include __DIR__ . '/modules/core/nav.php'; ?>
					<!---// BEGIN stuff here for all roles--->
					<?php if (current_user_can('subscriber') ||  current_user_can('contributor') ||  current_user_can('author') ||  current_user_can('editor') || current_user_can('administrator')) {  ?>
					<li><a href="https://clubrescue.github.io/crdocs" target="_blank"><i class="material-icons">help</i>Documentatie</a></li>
					<li><a href="<?php echo $url; ?>/wp-login.php?action=logout"><i class="material-icons">exit_to_app</i>Uitloggen</a></li>
					<?php }else { ?> <!---// END stuff here for all roles and BEGIN stuff here if user has no role--->
					<li><a href="https://login.microsoftonline.com/common/oauth2/authorize?response_type=code&scope=openid&domain_hint=trb.nu&client_id=115e93b9-98f9-4732-82bf-0cf79c0ed437&resource=https%3A%2F%2Fgraph.microsoft.com&redirect_uri=https%3A%2F%2Ftrb.nu%2Fwp-login.php&state=%7B0C913F39-1152-4FED-C546-CBAD15117A8D%7D&nonce=%7B0C913F39-1152-4FED-C546-CBAD15117A8D%7D&sso_reload=true"><i class="material-icons">exit_to_app</i>Inloggen</a></li>
					<?php } ?>
					<!---// END stuff here for users with no role--->
					
					<!---// Example of a submenu
					<li style="padding-left: 16px;">
						<div class="collapsible-header"><i class="material-icons">MATERIALICONNAME</i>(SUB)MENUNAME</div>
						<div class="collapsible-body">
							<ul>
								<li><a href="<?php //echo $url; ?>/clubredders/modules/MODULENAME/MODULEPAGE.php"><i class="material-icons">MATERIALICONNAME</i>LINKNAME</a></li>
							</ul>
						</div>
					</li>
					END example of a submenu--->
					
				</ul>
			</ul>			
		</div>
	</header>