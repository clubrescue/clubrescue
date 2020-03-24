<?php
	/*** Club.Redders - PAGE TEMPLATE TYPE - MaterializeCSS - v0.8.6
	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();

	/*** Insturctions to include the My envoironment into your website to keep C.R hidden from end-users.
	The my envoironment and the photo module can be included in your website.
	To include the my envoironment into your website you can use the following options;
		a. Create a function that can be called.
		b. Create a shortcode to the function from step a. to include a call.
	To create the function use the id name from the tabs bookmark section.
	Then include the $user variable, include the corresponding php page from the include section bellow.
	End the function with the echo statement from the bookmark section, replacing echo with return.
	***/
	
	include 'mijncr-attributes.php';
	include 'mijncr-activiteiten.php';
	include 'mijncr-verenigingsdiplomas.php';
	include 'mijncr-bondsdiplomas.php';
	include 'mijncr-bondsfuncties.php';
	include 'mijncr-acties.php';		include 'mijncr-declaraties.php';

?>
<?php include 'header.php'; ?>
<main>
  <div class="container"><br>

	<div class="row">
      <div class="col s12">
        <ul class="tabs">
          <li class="tab"><a class="active" href="#userattributes">Persoonlijke gegevens</a></li>
          <li class="tab"><a href="#activiteiten">Activiteiten</a></li>
          <li class="tab"><a href="#verenigingsdiplomas">Verenigingsdiploma's</a></li>
		  <li class="tab"><a href="#bondsdiplomas">Bondsdiploma's</a></li>
		  <li class="tab"><a href="#bondsfuncties">Bondsfuncties</a></li>
		  <li class="tab"><a href="#acties">Mijn acties</a></li>		  		  <li class="tab"><a href="#declaraties">Mijn declaraties</a></li>
        </ul>
      </div>
      <div id="userattributes" class="col s12"><?php echo $lidTable ?></div>
      <div id="activiteiten" class="col s12"><?php echo $activiteitenTable ?></div>
      <div id="verenigingsdiplomas" class="col s12"><?php echo $verenigingsDiplomasTable ?></div>
      <div id="bondsdiplomas" class="col s12"><?php echo $bondsDiplomasTable ?></div>
	  <div id="bondsfuncties" class="col s12"><?php echo $bondsFunctiesTable ?></div>
	  <div id="acties" class="col s12">
	    <ul class="collapsible" data-collapsible="accordion">
	      <li>
	        <div class="collapsible-header"><i class="material-icons">person</i>Gegevens wijzigen</div>
	        <div class="collapsible-body"><span><?php echo $acties_wijzien ?></span></div>
	      </li>
	      <li>
	        <div class="collapsible-header"><i class="material-icons">beach_access</i>Strandbewaking</div>
	        <div class="collapsible-body"><span><?php echo $acties_strandbewaking ?></span></div>
	      </li>
	      <li>
	        <div class="collapsible-header active"><i class="material-icons">fingerprint</i>Kader acties</div>
	        <div class="collapsible-body"><span><?php echo $acties_kader ?></span></div>
	      </li>
	    </ul>
	  </div>	  	  <div id="declaraties" class="col s12">	    <ul class="collapsible" data-collapsible="accordion">	      <li>	        <div class="collapsible-header"><i class="material-icons">local_mall</i>Inkopen</div>	        <div class="collapsible-body"><span><?php echo $InkopenTable ?></span></div>	      </li>	      <li>	        <div class="collapsible-header"><i class="material-icons">directions_car</i>Reiskosten</div>	        <div class="collapsible-body"><span><?php echo $ReiskostenTable ?></span></div>	      </li>	      <li>	        <div class="collapsible-header"><i class="material-icons">directions_boat</i>Overtochten</div>	        <div class="collapsible-body"><span><?php echo $OvertochtenTable ?></span></div>	      </li>	    </ul>	  </div>
	</div>

  </div>
</main>
<?php include 'footer.php'; ?>