<?php
    if (!isset($user)) {
        //header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
        require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/wp-authenticate.php';
        /*** REQUIRE USER AUTHENTICATION ***/
        login();
        /*** RETRIEVE LOGGED IN USER INFORMATION ***/
        $user = wp_get_current_user();
    };
    
    $showCreateTabs = true;
    $message = 'Let op! Dit formulier kan automatisch ingevuld worden vanuit de mail van Machforms.';

    // Include database class
    require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/utility.class.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/database.class.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/msgraph/msgraph.class.php';
    
    // Declare Namespace for MS Graph Model
    use Microsoft\Graph\Model;

    $database = new Database();
    
    if (isset($_POST["Relatienr"])) {
        $dataArray = clean_post_array($_POST);
        $dataArray["VolledigAdres"] = $dataArray["Straat"].' '.$dataArray["Huisnr"].$dataArray["HuisnrToev"];
        $dataArray["VolledigeNaam"] = str_replace('  ', ' ', $dataArray["Roepnaam"] .' '. $dataArray["Tussenvoegsels"] .' '. $dataArray["Achternaam"]);
        
        $Relatienr = htmlspecialchars($_POST["Relatienr"]);
        
        $insertSql = build_sql_insert('cr_leden', $dataArray);

        $countSql = "SELECT COUNT(*) as count FROM `cr_leden` WHERE `Relatienr` = '$Relatienr'";
        $database->query($countSql);
        if ($database->single()["count"] == 0) {
            $database->query($insertSql);
            $database->execute();
            $logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \''.addslashes($insertSql).'\', \''.$user->user_login.'\')';
            $database->query($logQuery);
            $database->execute();
            $message = 'Lid is successvol aangemaakt in ClubRedders! ';
            
            // Add to Office365 here
            $newUserModel= new Model\User();
            $newUserModel->setUserPrincipalName($Relatienr.'@trb.nu');

            // Create a new PasswordProfile
            $newUserPasswordProfile = new Model\PasswordProfile;
            $newUserPasswordProfile->setForceChangePasswordNextSignIn(true);
            $newUserPasswordProfile->setPassword(generatePassword(12));
            
            $newUserModel->setPasswordProfile($newUserPasswordProfile);
            $newUserModel->setJobTitle($dataArray["Lidstatus"]);
            $newUserModel->setPreferredLanguage('nl-NL');
            $newUserModel->setAccountEnabled(true);
            $newUserModel->setMailNickname($Relatienr);

            $newUserModel->setDisplayName($dataArray["VolledigeNaam"]);
            $newUserModel->setGivenName($dataArray["Roepnaam"]);
            $newUserModel->setSurname(trim($dataArray["Tussenvoegsels"] .' '. $dataArray["Achternaam"]));
            $newUserModel->setUsageLocation("NL");
            $newUserModel->setStreetAddress($dataArray["VolledigAdres"]);
            $newUserModel->setCountry($dataArray["Land"]);
            $newUserModel->setPostalCode($dataArray["Postcode"]);
            $newUserModel->setCity($dataArray["Woonplaats"]);
            $newUserModel->setMobilePhone($dataArray["Mobiel"]);
            
            try {
                $msgraph = new MSGraphAPI();
                $output = $msgraph->createUser($newUserModel, $dataArray["Email"]);
                if ($output) {
                    $message .= 'Lid is ook succesvol aangemaakt in Office365! ';
                }
            } catch (Exception $e) {
                $response = json_decode($e->getResponse()->getBody());
                $bodyMessage = $response->error->message;
                $message .= "Lid kon niet succesvol worden aangemaakt in Office365. Neem contact op met de webmasters om dit handmatig te laten doen. Vermeld graag de volgende error: $bodyMessage ";
            }
    
            $message .= 'Klik <a href="/clubredders/modules/membermanagement/">hier</a> om terug te gaan naar de hoofdpagina voor het beheer van leden.';
        } else {
            $message='Sportlink nummer bestaat al. Klik opnieuw op de link uit de mail en probeer het nog een keer. Klik <a href="/clubredders/modules/membermanagement/">hier</a> om terug te gaan naar de hoofdpagina voor het beheer van leden.';
        }
        $showCreateTabs = false;
    } else {
        // Dropdown voor lidStatus maken
        $lidStatusSQL =  'SELECT DISTINCT `Lidstatus` FROM `cr_leden` ORDER BY `Lidstatus` ASC';
        $database->query($lidStatusSQL);
        $lidStatusResult = $database->resultset();
        
        $newLid = array();
        
        //Fetch, sanitize input and pre-populate the view.
        foreach ($_GET as $key=>$val) {
            if ($key !== 'Relatienr') {
                $escKey = htmlspecialchars($key);
                $escVal = htmlspecialchars($val);
                $newLid[$escKey] = $escVal;
            }
        }
    }
    
    include '../views/nieuwlid.view.php';
