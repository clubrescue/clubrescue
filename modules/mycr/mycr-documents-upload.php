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

		//check if form is submitted
		if (isset($_POST['submit']))
		{
			$filename = $_FILES['file1']['name'];
			//additional CR database values from the upload form.
			$label = $_POST['label'];

			//upload file
			if($filename != '')
			{
				$ext = pathinfo($filename, PATHINFO_EXTENSION);
				$allowed = ['pdf'/***, 'docx'***/]; //Disable docx because only PDF can be renderd corectly within browsers.
			
				//check if file type is valid
				if (in_array($ext, $allowed))
				{
					// get last record id
					$sqlr = 'SELECT MAX(id) AS `maxid` FROM `cr_myfiles`';
					$database = new Database();
					$database->query($sqlr);	
					$result = $database->single();
					
					if ($result > 0)
					{
						$filename = ($result['maxid']+1) . '-' . $filename;
					}
					else
						$filename = '1' . '-' . $filename;

					//set target directory
					$crbinRoot = '/home/u70472p67165/domains/trb.nu';
					$path = $crbinRoot.'/crbin/uploads/';
						
					$created = @date('Y-m-d H:i:s');
					move_uploaded_file($_FILES['file1']['tmp_name'],($path . $filename));
					
					// insert file details into database
					$sqlw = 'INSERT INTO `cr_myfiles`(filename, created, label) VALUES(\''.$filename.'\', \''.$created.'\', \''.$label.'\')';
					$database = new Database();
					//$logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \'Excel Inschrijvings Tool geupload.\', \''.$user->user_login.'\')';
					//$database->query($logQuery);
					$database->query($sqlw);	
					$database->execute();
					if($_POST['source'] == "CR") {
						header("Location: mycr.php?st=success");
					}elseif($_POST['source'] == "WP") {
						header("Location: https://trb.nu/mijn-trb-nu?st=success");
					}
				}
				else
				{
					if($_POST['lsource'] == "CR") {
						header("Location: mycr.php?st=success");
					}elseif($_POST['source'] == "WP") {
						header("Location: https://trb.nu/mijn-trb-nu?st=success");
					}
				}
			}
			else
				if($_POST['source'] == "CR") {
						header("Location: mycr.php?st=success");
					}elseif($_POST['source'] == "WP") {
						header("Location: https://trb.nu/mijn-trb-nu?st=success");
					}
		}
	}
?>