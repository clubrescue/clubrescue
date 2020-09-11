<?php
    session_start();

    if (!isset($_SESSION['token'])) {
        $_SESSION['O365_REDIRECT'] = $_SERVER['REQUEST_URI'];
        include $_SERVER['DOCUMENT_ROOT'] . '/clubredders/auth.php';
    }
    
    require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/msgraph/user.class.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/utility.class.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/database.class.php';
    
    $user = new MSGraphUser($_SESSION['token']);
	//retrieve current user name
	$userNameRequest = $user->getUser();
	$userNameJSON = json_encode($userNameRequest);
	$userNameValue = json_decode($userNameJSON, true);
	$userName = strtok($userNameValue['userPrincipalName'], '@');

	/*** Insturctions to include the My envoironment into your website to keep C.R hidden from end-users.
	The my envoironment and the photo module can be included in your website.
	To include the my envoironment into your website you can use the following options;
		a. Create a function that can be called.
		b. Create a shortcode to the function from step a. to include a call.
	To create the function use the id name from the tabs bookmark section.
	Then include the $user variable, include the corresponding php page from the include section bellow.
	End the function with the echo statement from the bookmark section, replacing echo with return.
	***/
	
	//$welcome = 'MyCR';
    //include './views/index.view.php';

	//if(isset($user)){
		
	/** requiresPrivilege function demo
		if(requiresPrivilege('priv_test_Admin') == true ) {
			echo '<h1>Jij beschikt in deze module over admin rechten.</h1>';
		}elseif(requiresPrivilege('priv_test_Member') == true ) {
			echo '<h1>Jij beschikt in deze module over lid rechten.</h1>';	
		}elseif(requiresPrivilege('priv_test_WPC') == true ) {
			echo '<h1>Jij beschikt in deze module over kader rechten.</h1>';	
		}elseif(requiresPrivilege('priv_test_WPE') == true ) {
			echo '<h1>Jij beschikt in deze module over bestuur rechten.</h1>';	
		}else {
			echo '<h1>Jij beschikt niet over rechten voor deze module.</h1>';
		}
	*/

		// Leden   = subscriber  = priv_test_Member
		// Kader   = contributor = priv_test_WPC
		// Bestuur = editor      = priv_test_WPE
		// Admin   = admin       = priv_test_Admin

		include 'mycr-attributes.php';
		include 'mycr-activities.php';
		include 'mycr-internalcertifications.php';
		include 'mycr-externalcertifications.php';
		include 'mycr-externalfunctions.php';
		include 'mycr-documents.php';
		include 'mycr-actions.php';		
		include 'mycr-expenses.php';
	
	//}
?>
<?php include __DIR__ . '/../../header.php'; ?>

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
		  <!-- Mijn documenten code -->
		  <?php echo $documenttab ?>
		  <!-- Mijn documenten code -->
		  <li class="tab"><a href="#acties">Mijn acties</a></li>
		  <li class="tab"><a href="#declaraties">Mijn declaraties</a></li>
        </ul>
      </div>
      <div id="userattributes" class="col s12"><?php echo $lidTable ?></div>
      <div id="activiteiten" class="col s12"><?php echo $activiteitenTable ?></div>
      <div id="verenigingsdiplomas" class="col s12"><?php echo $verenigingsDiplomasTable ?></div>
      <div id="bondsdiplomas" class="col s12"><?php echo $bondsDiplomasTable ?></div>
	  <div id="bondsfuncties" class="col s12"><?php echo $bondsFunctiesTable ?></div>
	  <!-- Mijn documenten code -->
	  <?php echo $documentlist ?>
	  <!-- Mijn documenten code -->
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
	        <div class="collapsible-header"><i class="material-icons">fingerprint</i>Kader acties</div>
	        <div class="collapsible-body"><span><?php echo $acties_kader ?></span></div>
	      </li>
	    </ul>
	  </div>
	  <div id="declaraties" class="col s12">
		<?php echo $DeclaratiesHeader ?>
		<ul class="collapsible" data-collapsible="accordion">
	      <li>
			<div class="collapsible-header"><i class="material-icons">local_mall</i>Inkopen</div>
	        <div class="collapsible-body"><span><?php echo $InkopenTable ?></span></div>
		  </li>
		  <li>
			<div class="collapsible-header"><i class="material-icons">directions_car</i>Reiskosten</div>
			<div class="collapsible-body"><span><?php echo $ReiskostenTable ?></span></div>
		  </li>
		  <li>
			<div class="collapsible-header"><i class="material-icons">directions_boat</i>Overtochten</div>
			<div class="collapsible-body"><span><?php echo $OvertochtenTable ?></span></div>
		  </li>
		</ul>
		<?php echo $DeclaratiesFooter ?>
	  </div>
	</div>

  </div>
</main>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/clubredders/footer.php'; ?>