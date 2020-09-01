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
    
    $showDropdown = true;
    $message = '';

    $database = new Database();
    $selectedBewaker = htmlspecialchars($_POST["Relatienr"]);
    
    $dropdownLedenSQL = ' SELECT `Relatienr`, `VolledigeNaam`, `Achternaam` FROM `cr_leden` ORDER BY `Achternaam` ASC';
    $database->query($dropdownLedenSQL);
    $dropdownLedenResult = $database->resultset();

    if ($_POST["Relatienr"]) {
        $Relatienr = htmlspecialchars($_POST["Relatienr"]);

        //Insert the user into the cr_leden_archived table
        $insertQuery = "INSERT INTO `cr_leden_archived` SELECT * FROM `cr_leden` WHERE `Relatienr` = '$Relatienr'";
        $database->query($insertQuery);
        $database->execute();
        $resultInsert = $database->rowCount();
        
        //Delete the user from the cr_leden table
        $deleteQuery = "DELETE FROM`cr_leden` WHERE `Relatienr` = '$Relatienr'";
        $database->query($deleteQuery);
        $database->execute();
        $resultDelete = $database->rowCount();

        if ($resultInsert + $resultDelete === 2) {
            $logQuery = 'INSERT INTO `cr_log` (timestamp, message, user) VALUES (\''.date('Y-m-d G:i:s').'\', \''.addslashes($deleteQuery).'\', \''.$user->user_login.'\')';
            $database->query($logQuery);
            $database->execute();

            $message = "Delete van lid $Relatienr uit ClubRedders is gelukt. ";
            $showDropdown = false;

            try {
                $msgraph = new MSGraphAPI();
                $O365Delete = $msgraph->removeUser($Relatienr.'@trb.nu');
    
                if ($O365Delete) {
                    $message .= 'Lid is ook succesvol verwijderd uit Office365. ';
                } else {
                    $message .= 'Lid kon niet succesvol worden verwijderd uit Office365. Neem contact op met de webmasters om dit handmatig te laten doen. ';
                }
            } catch (Exception $e) {
                $response = json_decode($e->getResponse()->getBody());
                $bodyMessage .= $response->error->message;
                $message .= "Lid kon niet succesvol worden verwijderd uit Office365. Neem contact op met de webmasters om dit handmatig te laten doen. Vermeld graag de volgende error: $bodyMessage ";
            }
        } else {
            $message = "Delete van lid $Relatienr uit ClubRedders is niet gelukt. Neem contact op met de webmasters. ";
            $showDropdown = false;
        }

        $message .= 'Klik <a href="/clubredders/modules/membermanagement/">hier</a> om terug te gaan naar de hoofdpagina voor het beheer van leden.';
    };
    
    include '../views/deletelid.view.php';
