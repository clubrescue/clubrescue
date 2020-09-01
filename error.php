<?php
	//Club.Redders - PAGE TEMPLATE TYPE - MaterializeCSS - v0.8.6
	//header("Cache-Control: no-cache, must-revalidate");
	//header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");

	$response_code = http_response_code();
	
	//Informational response
	$response_100 = "Doorgaan";
	$response_101 = "Protocolwissel";
	$response_102 = "Processing";
	//Success
	$response_200 = "OK";
	$response_201 = "Aangemaakt";
	$response_202 = "Aanvaard";
	$response_203 = "Niet-gemachtigde informatie";
	$response_204 = "Geen inhoud";
	$response_205 = "Inhoud opnieuw instellen";
	$response_206 = "Gedeeltelijke inhoud";
	$response_207 = "Meerdere statussen";
	//Redirection
	$response_300 = "Meerkeuze";
	$response_301 = "Definitief verplaatst";
	$response_302 = "Tijdelijk verplaatst";
	$response_303 = "Zie andere";
	$response_304 = "Niet gewijzigd";
	$response_305 = "Gebruik Proxy (*: vele HTTP-clients (zoals Mozilla en Internet Explorer) gaan, wegens veiligheidsredenen, slecht met deze code om.)";
	$response_306 = "(Gereserveerd)";
	$response_307 = "Tijdelijke omleiding";
	$response_308 = "Definitieve omleiding";
	//Client errors
	$response_400 = "Foute aanvraag";
	$response_401 = "Niet geautoriseerd";
	$response_402 = "Betaalde toegang";
	$response_403 = "Verboden toegang";
	$response_404 = "Niet gevonden";
	$response_405 = "Methode niet toegestaan";
	$response_406 = "Niet aanvaardbaar";
	$response_407 = "Authenticatie op de proxyserver verplicht";
	$response_408 = "Aanvraagtijd verstreken";
	$response_409 = "Conflict";
	$response_410 = "Verdwenen";
	$response_411 = "Lengte benodigd";
	$response_412 = "Niet voldaan aan vooraf gestelde voorwaarde";
	$response_413 = "Aanvraag te groot";
	$response_414 = "Aanvraag-URL te lang";
	$response_415 = "Media-type niet ondersteund";
	$response_416 = "Aangevraagd gedeelte niet opvraagbaar";
	$response_417 = "Niet voldaan aan verwachting";
	$response_418 = "I'm a teapot (1-aprilgrap; zie HTCPCP) (gedefinieerd in RFC 2324[1])";
	$response_419 = "Pagina verlopen (Onofficiële http code van Laravel)";
	$response_422 = "Aanvraag kan niet verwerkt worden";
	$response_423 = "Afgesloten";
	$response_424 = "Gefaalde afhankelijkheid";
	$response_426 = "Upgrade nodig";
	$response_428 = "Voorwaarde nodig";
	$response_429 = "Te veel requests";
	$response_431 = "Headers van de aanvraag te lang";
	$response_450 = "Geblokkeerd door Windows Parental Controls (niet-officiële HTTP-statuscode)";
	$response_451 = "Toegang geweigerd om juridische redenen.[2] De code is een toespeling op de roman Fahrenheit 451.";
	$response_494 = "Request Header Too Large (Nginx), Deze header lijkt op header 431 maar wordt gebruikt door nginx";
	$response_495 = "Cert Error (Nginx), Wordt gebruikt door Nginx om een normale fout te melden en die van een certificaat error in de logboeken te onderscheiden.";
	$response_496 = "No Cert (Nginx), Wordt gebruikt door Nginx om een missend certificaat te melden en de foutcode te onderscheiden van een normale fout.";
	$response_497 = "HTTP to HTTPS (Nginx): Interne code van Nginx om aan te geven dat er een http aanvraag is op een https port";
	$response_498 = "Token expired/invalid (Esri): Een code van 498 geeft aan dat het token verlopen of ongeldig is.";
	$response_499 = "Token required (Esri): Wordt weggegeven door Esri dat er een token nodig is wanneer er geen is gegeven";
	//Server errors
	$response_500 = "Interne serverfout";
	$response_501 = "Niet geïmplementeerd";
	$response_502 = "Bad Gateway";
	$response_503 = "Dienst niet beschikbaar";
	$response_504 = "Gateway Timeout";
	$response_505 = "HTTP-versie wordt niet ondersteund";
	$response_509 = "Bandbreedte overschreden (niet-officiële HTTP-statuscode)";
	$response_510 = "Niet verlengd";
	$response_511 = "Netwerkauthenticatie vereist";
	$response_522 = "Connectie duurt te lang (Cloudflare)";
	$response_525 = "TLS-handshake mislukt (Cloudflare)[3]";
	
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
		href="<?php //echo $url; ?>/clubredders/favicon.ico">

	<!-- CSS  -->
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link href="<?php //echo $url; ?>/clubredders/css/materialize.css"
		type="text/css" rel="stylesheet" media="screen,projection" />
	<link href="<?php //echo $url; ?>/clubredders/css/style.css"
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
							href="<?php //echo $url; ?>/clubredders"
							class="brand-logo center">Club.Redders</a>
						<!--<a href="#" data-activates="nav-mobile" class="button-collapse show-on-large"><i
								class="material-icons">menu</i></a>-->
						<ul class="right hide-on-med-and-down">
							<li><a
									href="<?php //echo $url; ?>/clubredders">Welkom
									<?php //echo $user->user_firstname;?></a>
							</li>
						</ul>
					</div>
				</nav>
			</div>
		</div>
	</header>
	
<main>
  <div class="section no-pad-bot" id="index-banner">
    <div class="container">
      <br><br>
      <h1 class="header center orange-text">ERROR <?php echo $response_code; ?></h1>
      <div class="row center">
        <h5 class="header col s12 light"><?php echo ${"response_" . $response_code}; //${$response_code}; ?></h5>
      </div>
    </div>
  </div>
</main>
  <footer class="page-footer orange">
    <div class="container">
      <div class="row">
        <div class="col l6 s12">
          <h5 class="white-text">Over Club.Redders</h5>
          <p class="grey-text text-lighten-4">Club.Redders (C.R) maakt op basis van externe ledenadministratie systemen
            data beschikbaar aan (kader)leden. C.R streeft een modulaire opbouw na waarmee nieuwe functionaliteiten
            eenvoudig toegevoegd kunnen worden. Dit is de 2de generatie van C.R. De 3de generatie op basis van een<br>
            modulair raamwerk is momenteel in ontwikkeling.</p>


        </div>
        <div class="col l3 s12">
          <h5 class="white-text">Meer info</h5>
          <ul>
            <li><a class="white-text" target="_blank" href="https://clubrescue.github.io/about.html">Over ons</a></li>
            <li><a class="white-text" target="_blank" href="https://github.com/clubrescue">Project</a></li>
            <li><a class="white-text" target="_blank"
                href="https://github.com/clubrescue/clubrescue/projects/1">Roadmap</a></li>
            <li><a class="white-text" target="_blank" href="http://clubrescue.github.io/clubrescue">Documentatie</a>
            </li>
          </ul>
        </div>
        <div class="col l3 s12">
          <h5 class="white-text">Onze tools</h5>
          <ul>
            <li><a class="white-text" target="_blank" href="https://clubrescue.github.io">ITSuite4NP</a></li>
            <li><a class="white-text" target="_blank"
                href="https://products.office.com/nl-nl/office-online/documents-spreadsheets-presentations-office-online">Office
                365</a></li>
            <li><a class="white-text" target="_blank" href="https://www.machform.com">MachForm</a></li>
            <li><a class="white-text" target="_blank" href="https://wordpress.org">WordPress</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="footer-copyright">
      <div class="container">
        © 2017 <a class="orange-text text-lighten-3" target="_blank" href="https://github.com/BorghoutsR">Club.Redders -
          v0.8.6</a>, MIT/GPLv3.
      </div>
    </div>
  </footer>

  <!--  Scripts-->
  <script src="https://www.draw.io/js/viewer.min.js" type="text/javascript"></script>
  <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <script src="<?php echo $url; ?>/clubredders/js/materialize.js"></script>
  <script src="<?php echo $url; ?>/clubredders/js/init.js"></script>

  <script type="text/javascript">
    $(document).ready(function() {
      $('select').material_select();
    });
  </script>
  </body>

  </html>