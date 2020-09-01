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

    //$welcome = 'MyCR Expenses';
    //include './views/index.view.php';
	
	if(isset($user)){

		// fetch files
		$sql = 'SELECT * FROM `cr_myfiles`';
		$sqlLabels = 'SELECT DISTINCT `label` FROM `cr_myfiles`';

		$database = new Database();
		$database->query($sql);
		$result = $database->resultset();
			
		$databaseLabels = new Database();
		$databaseLabels->query($sqlLabels);		
		$labels = $databaseLabels->resultset();
			
		// Mijn CR codebase.

			$documenttab = '<li class="tab"><a href="#documenten">Mijn documenten</a></li>';

			$documentlist = '<div id="documenten" class="col s12"><ul class="collapsible" data-collapsible="accordion">';
			foreach ($labels as $label => $category) {
				$documentlist .= '<li><div class="collapsible-header"><i class="material-icons">add_circle</i>'.$category['label'].'</div><div class="collapsible-body"><span><div id="'.$category['label'].'">';
				foreach ($result as $key => $value) {
					if($value['label'] == $category['label']) {
						$documentlist .= '<a href="mycr-documents-download.php?filename='.$value['filename'].'" target="_blank">'.substr(substr($value['filename'], strpos($value['filename'], "-") + 1), 0, -4).'</a><br>';
					}
				}
				$documentlist .= '</div></span></div></li>';
			}
			
			// Mogelijkheid voor kader leden om documenten toe te voegen
			if( requiresPrivilege('priv_test_WPC') == true || requiresPrivilege('priv_test_WPE') == true || requiresPrivilege('priv_test_Admin') == true ) {
				$documentlist .= '<li><div class="collapsible-header"><i class="material-icons">add_circle</i>Upload documenten</div><div class="collapsible-body"><span><div id="'.$category['label'].'">';

				$documentlist .= '<form action="mycr-documents-upload.php" method="POST" enctype="multipart/form-data">';
				$documentlist .= '<div class="file-field input-field">';
				$documentlist .= '<div class="btn">';
				$documentlist .= '<span>Selecteer het bestand om te publiceren</span>';
				$documentlist .= '<input type="file" name="file1">';
				$documentlist .= '</div>';
				$documentlist .= '<div class="file-path-wrapper">';
				$documentlist .= '<input class="file-path validate" type="text">';
				$documentlist .= '</div>';
				$documentlist .= '</div>';
					$documentlist .= '<div class="input-field">';
					$documentlist .= '<input placeholder="Categorie label" id="label" name="label" type="text" class="validate">';
					$documentlist .= '<label for="label">Categorie</label>';
					$documentlist .= '</div>';
					
					$documentlist .= '<div class="input-field">';
					$documentlist .= '<input value="CR" id="source" name="source" type="hidden" class="validate">';
					$documentlist .= '<label for="label">Categorie</label>';
					$documentlist .= '</div>';
				$documentlist .= '<button class="btn waves-effect waves-light" type="submit" name="submit" value="Uploaden">uploaden';
				$documentlist .= '<i class="material-icons right">file_upload</i>';
				$documentlist .= '</button>';
										if(isset($_GET['st'])) {
											$documentlist .= '<div class="alert alert-danger text-center">';
											if ($_GET['st'] == 'success') {
												echo "Het bestand is succesvol geupload!";
											} else {
												echo 'De gekozen bestandsextensie is niet toegestaan (enkel .pdf)!';
											}
											$documentlist .= '</div>';
										}
				$documentlist .= '</form>';	
				
				$documentlist .= '</div></span></div></li>';
			}
			$documentlist .= '</ul></div>';
			
			
		// Mijn WP-Plugin codebase.

			foreach ($labels as $label => $category) {
				${'documentlist' . '_' . $category['label']} = '<div id="'.$category['label'].'">';
				foreach ($result as $key => $value) {
					if($value['label'] == $category['label']) {
						${'documentlist' . '_' . $category['label']} .= '<a href="/clubredders/modules/mycr/mycr-documents-download.php?filename='.$value['filename'].'" target="_blank">'.substr(substr($value['filename'], strpos($value['filename'], "-") + 1), 0, -4).'</a><br>';
					}
				}
				${'documentlist' . '_' . $category['label']} .= '</div>';
			}
			
			// Mogelijkheid voor kader leden om documenten toe te voegen
			if( requiresPrivilege('priv_test_WPC') == true || requiresPrivilege('priv_test_WPE') == true || requiresPrivilege('priv_test_Admin') == true ) {
				$documentlist_Upload = '<div id="Upload documenten">';

				$documentlist_Upload .= '<form action="/clubredders/modules/mycr/mycr-documents-upload.php" method="post" class="wpcf7-form" enctype="multipart/form-data">';
				$documentlist_Upload .= '<div style="display: none;">';
				$documentlist_Upload .= '</div>';
				$documentlist_Upload .= '<p><label> Selecteer het bestand om te publiceren<br>';
				$documentlist_Upload .= '<span class="wpcf7-form-control-wrap file1">';
				$documentlist_Upload .= '<input type="file" name="file1" size="40" class="wpcf7-form-control wpcf7-file" accept=".pdf" aria-invalid="false"></span> </label></p>';
				$documentlist_Upload .= '<p><label> Categorie label<br>';
				$documentlist_Upload .= '<span class="wpcf7-form-control-wrap label">';
				$documentlist_Upload .= '<input type="text" id="label" name="label" value="" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span> </label></p>';
				$documentlist_Upload .= '<p style="padding-bottom: 0px;">';
				
				//$documentlist_Upload .= '<p><label> Categorie label<br>';
				//$documentlist_Upload .= '<span class="wpcf7-form-control-wrap label">';
				$documentlist_Upload .= '<input type="hidden" id="source" name="source" value="WP" size="40" class="wpcf7-form-control wpcf7-text" aria-invalid="false"></span> </label></p>';
				//$documentlist_Upload .= '<p style="padding-bottom: 0px;">';
				
				$documentlist_Upload .= '<input type="submit" name="submit" value="Verzenden" class="wpcf7-form-control wpcf7-submit"><span class="ajax-loader"></span></p>';
				$documentlist_Upload .= '<div class="wpcf7-response-output wpcf7-display-none" aria-hidden="true"></div>';
										if(isset($_GET['st'])) {
											$documentlist_Upload .= '<div class="alert alert-danger text-center">';
											if ($_GET['st'] == 'success') {
												echo "Het bestand is succesvol geupload!";
											} else {
												echo 'De gekozen bestandsextensie is niet toegestaan (enkel .pdf)!';
											}
											$documentlist_Upload .= '</div>';
										}
				$documentlist_Upload .= '</form>';	
				$documentlist_Upload .= '</div>';
			}
			// End execute code only if...
	}
?>