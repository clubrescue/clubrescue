<?php
	/*** Club.Redders - PAGE TEMPLATE TYPE - MaterializeCSS - v0.8.6
	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/$user = wp_get_current_user();

?>
<?php include 'header.php'; ?>
<main>
  <div class="section no-pad-bot" id="index-banner">
    <div class="container">
      <br><br>
      <h1 class="header center orange-text">Support module</h1>
      <div class="row center">
        <h5 class="header col s12 light">De verenigings processen vind je onder de knop, de C.R handleidingen staan daaronder.</h5>
      </div>
      <div class="row center">
        <a href="https://trb.nu/wp-content/uploads/documenten/procesflow.pdf" id="download-button" class="btn-large waves-effect waves-light orange" target="_blank">Procesflow</a>
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
            <h2 class="center light-blue-text"><i class="material-icons">web</i></h2>
            <a href="https://trb.nu/clubredders/inrichtingsportlink.pdf"><h5 class="center">Inrichting Sportlink</h5></a>

            <p class="light">Om gebruik te kunnen maken van Club.Redders dient U Sportlink in te richten volgens de richtlijnen van Club.Redders. Dit document beschrijft welke velden wel/niet ingezet kunnen worden en op welke wijze U deze dient te vullen. Indien Sportlink niet overeenkomstig dit document is ingericht kan de werking van Club.Redders niet worden gegarandeerd.</p>
          </div>
        </div>

        <div class="col s12 m4">
          <div class="icon-block">
            <h2 class="center light-blue-text"><i class="material-icons">group</i></h2>
            <a href="https://trb.nu/clubredders/gebruikersenrollen.pdf"><h5 class="center">Gebruikers en Rollen</h5></a>

            <p class="light">Binnen een IT omgeving waarin Club.Redders draait bestaan verschillende gebruikers en rollen. Dit document beschrijft de inrichting en samenhang hiervan binnen Sportlink, WordPress, Club.Redders en Office 365.</p>
          </div>
        </div>

        <div class="col s12 m4">
          <div class="icon-block">
            <h2 class="center light-blue-text"><i class="material-icons">file_upload</i></h2>
            <a href="https://trb.nu/clubredders/exporterensportlinkdata.pdf"><h5 class="center">Exporteren Sportlink Data</h5></a>

            <p class="light">Nadat Sportlink is ingericht volgens de Club.Redders richtlijnen is het eveneens van belang dat de data op een bepaalde wijze wordt geexporteerd. Dit document beschrijft de wijze waarop U de data zodanig uit Sportlink haalt dat deze door Club.Redders kunnen worden ingelezen. Houd er rekening mee dat U zelf verantwoordelijk bent dat de gegevens tussen de export en import fase op een veilige wijze worden opgeslagen en daarna verwijderd.</p>
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