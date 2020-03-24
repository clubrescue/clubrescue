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
  <div class="section no-pad-bot" id="index-banner">
    <div class="container">
      <br><br>
      <h1 class="header center orange-text">Club.Redders</h1>
      <div class="row center">
        <h5 class="header col s12 light">De web-app om uw gegevens uit verplichte bonds ledenadministratie te bevrijden</h5>
      </div>
      <div class="row center">
        <a href="mijncr.php" id="download-button" class="btn-large waves-effect waves-light orange">Mijn Club.Redders</a>
      </div>
      <br><br>

    </div>
  </div>

  <div class="container">
    <div class="section">

      <!--   Icon Section   -->
      <div class="row">
        <div class="col s12 m4">
          <div class="icon-block">
            <h2 class="center light-blue-text"><i class="material-icons">group</i></h2>
            <h5 class="center">Intuïtief Material Design</h5>

            <p class="light">Onze gebruikersinterface is gebaseerd op Material Design, hiermee ontstaat een uniforme gebruikerservaring over alle modules. De gebruiker zal een krachtere gebruikerservaring ondervinden op zowel computer, tablet en telefoon.</p>
          </div>
        </div>

        <div class="col s12 m4">
          <div class="icon-block">
            <h2 class="center light-blue-text"><i class="material-icons">device_hub</i></h2>
            <h5 class="center">Centrale Data Hub</h5>

            <p class="light">Het hart van ons systeem is een centrale data hub welke alle data centraliseerd en aan diverse onderdelen beschikbaar stelt. Dit alles gebeurt op basis van het standaard data model uit uw eigen ledenadministratie.</p>
          </div>
        </div>

        <div class="col s12 m4">
          <div class="icon-block">
            <h2 class="center light-blue-text"><i class="material-icons">security</i></h2>
            <h5 class="center">Veilig en toegankelijk</h5>

            <p class="light">De vertrouwelijkheid en integriteit van uw data model staan in C.R centraal. Het enigste waar uw zelf voor hoeft te zorgen is een veilige omgeving om de gexporteerde data op te slaan voor uw deze in C.R importeerd. Omdat C.R een zelfstandig product is kunnen wij deze data niet met een directe koppeling uitlezen uit de meeste ledenadministratie systemen.</p>
          </div>
        </div>
      </div>

    </div>
    <br><br>

    <div class="section">

    </div>
  </div>
</main>
<?php include 'footer.php'; ?>