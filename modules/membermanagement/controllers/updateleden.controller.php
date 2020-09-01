<?php
    
    if (!isset($user)) {
        //header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
        require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/wp-authenticate.php';
        /*** REQUIRE USER AUTHENTICATION ***/
        login();
        /*** RETRIEVE LOGGED IN USER INFORMATION ***/
        $user = wp_get_current_user();
    };

    // Include database class
    require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/utility.class.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/database.class.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/msgraph/msgraph.class.php';
    
    // Declare Namespace for MS Graph Model
    use Microsoft\Graph\Model;

    $message = '';
    $database = new Database();
    
    $dropdownLedenSQL = ' SELECT `Relatienr`, `VolledigeNaam`, `Achternaam` FROM `cr_leden` ORDER BY `Achternaam` ASC';
    $database->query($dropdownLedenSQL);
    $dropdownLedenResult = $database->resultset();
    $selectedBewaker = isset($_POST["bewaker"]) ? htmlspecialchars($_POST["bewaker"]) : null;

    
    if (isset($_POST["bewaker"])) {
        $selectedBewaker = htmlspecialchars($_POST["bewaker"]);
        $ledenGegevensSQL = 'SELECT `cr_leden`.* , SUM(`TransactieBedrag`) as `TransactieBedrag` FROM `cr_leden` left outer join `cr_transacties` on `cr_leden`.`Relatienr`= `cr_transacties`.`Relatienr` where `cr_leden`.`Relatienr` = \''.$selectedBewaker.'\'';
        $database->query($ledenGegevensSQL);
        $ledenGegevensResult = $database->single();

        $lidStatusSQL =  'SELECT DISTINCT `Lidstatus` FROM `cr_leden` ORDER BY `Lidstatus` ASC';
        $database->query($lidStatusSQL);
        $lidStatusResult = $database->resultset();
    }
    
    if (isset($_POST["Relatienr"])) {
        $selectedBewaker = htmlspecialchars($_POST["Relatienr"]);
        $dataArray = clean_post_array($_POST);
            
        $dataArray["VolledigAdres"] = $dataArray["Straat"].' '.$dataArray["Huisnr"].$dataArray["HuisnrToev"];
        $dataArray["VolledigeNaam"] = str_replace('  ', ' ', $dataArray["Roepnaam"] .' '. $dataArray["Tussenvoegsels"] .' '. $dataArray["Achternaam"]);
        
        $updateSQL = build_sql_update('cr_leden', $dataArray, '`Relatienr` = \''.$selectedBewaker.'\'');
        $database->query($updateSQL);
        $updateResult = $database->execute();
        
        if ($updateResult) {
            $message = "Het lid met sportlinkid $selectedBewaker is succesvol geupdate! ";

            $updateUserModel = new Model\User();
            $updateUserModel->setUserPrincipalName($selectedBewaker.'@trb.nu');

            $updateUserModel->setDisplayName($dataArray["VolledigeNaam"]);
            $updateUserModel->setGivenName($dataArray["Roepnaam"]);
            $updateUserModel->setSurname(trim($dataArray["Tussenvoegsels"] .' '. $dataArray["Achternaam"]));
            $updateUserModel->setUsageLocation("NL");
            $updateUserModel->setStreetAddress($dataArray["VolledigAdres"]);
            $updateUserModel->setCountry($dataArray["Land"]);
            $updateUserModel->setPostalCode($dataArray["Postcode"]);
            $updateUserModel->setCity($dataArray["Woonplaats"]);
            $updateUserModel->setMobilePhone($dataArray["Mobiel"]);

            try {
                $msgraph = new MSGraphAPI();
                $output = $msgraph->updateUser($updateUserModel);
                
                if ($output) {
                    $message .= 'Lid is ook succesvol geüpdatet in Office365! ';
                } else {
                    $message .= "Lid kon niet succesvol worden geüpdatet in Office365. Neem contact op met de webmasters om dit handmatig te laten doen. ";
                }
            } catch (Exception $e) {
                $response = json_decode($e->getResponse()->getBody());
                $bodyMessage = $response->error->message;
                $message .= "Lid kon niet succesvol worden geüpdatet in Office365. Neem contact op met de webmasters om dit handmatig te laten doen. Vermeld graag de volgende error: $bodyMessage ";
            }

            $logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \''.addslashes($updateSQL).'\', \''.$user->user_login.'\')';
            $database->query($logQuery);
            $database->execute();

            $ledenGegevensSQL = 'SELECT `cr_leden`.* , SUM(`TransactieBedrag`) as `TransactieBedrag` FROM `cr_leden` left outer join `cr_transacties` on `cr_leden`.`Relatienr`= `cr_transacties`.`Relatienr` where `cr_leden`.`Relatienr` = \''.$selectedBewaker.'\'';
            $database->query($ledenGegevensSQL);
            $ledenGegevensResult = $database->single();

            $lidStatusSQL =  'SELECT DISTINCT `Lidstatus` FROM `cr_leden` ORDER BY `Lidstatus` ASC';
            $database->query($lidStatusSQL);
            $lidStatusResult = $database->resultset();
            
            $dropdownLedenSQL = ' SELECT `Relatienr`, `VolledigeNaam`, `Achternaam` FROM `cr_leden` ORDER BY `Achternaam` ASC';
            $database->query($dropdownLedenSQL);
            $dropdownLedenResult = $database->resultset();
            
            $message .= 'Klik <a href="/clubredders/modules/membermanagement/">hier</a> om terug te gaan naar de hoofdpagina voor het beheer van leden.';
        }
    }
    
    include '../views/updateleden.view.php';
