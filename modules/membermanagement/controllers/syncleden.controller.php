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
    require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/msgraph/user.class.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/msgraph/msgraph.class.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/utility.class.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/clubredders/util/database.class.php';

    // Declare Namespace for MS Graph Model
    use Microsoft\Graph\Model;

    $user = new MSGraphUser($_SESSION['token']);
    $msgraph = new MSGraphAPI();
    $database = new Database();
  
    $welcome = 'Diplomabeheer';

    $message = 'Met deze knop worden alle missende leden in Office365 toegevoegd op basis van ClubRedders.';

    if (isset($_POST["sync"])) {
        $o365Users = $msgraph->getUsers();

        $sql = "select `Relatienr`, `Lidstatus`,`VolledigeNaam`,`Roepnaam`,`Tussenvoegsels`,`Achternaam`, `VolledigAdres`,`Land`,`Postcode`,`Woonplaats`,`Mobiel`, `Email` ";
        $sql .= "from `cr_leden` WHERE `Relatienr` NOT IN (";
        foreach ($o365Users as $o365User) {
            $sql .= "'".strtok($o365User->getUserPrincipalName(), '@')."',";
        }
        $sql = substr($sql, 0, -1).');';
    
        $database->query($sql);
    
        $toBeInsertedUsers = $database->resultset();
        
        $count = 0;

        foreach ($toBeInsertedUsers as $newUser) {
            // Add to Office365 here
            $newUserModel= new Model\User();
            $newUserModel->setUserPrincipalName($newUser["Relatienr"].'@trb.nu');
    
            // Create a new PasswordProfile
            $newUserPasswordProfile = new Model\PasswordProfile;
            $generatedPassword = generatePassword(12);
            $newUserPasswordProfile->setForceChangePasswordNextSignIn(true);
            $newUserPasswordProfile->setPassword(htmlspecialchars($generatedPassword));
    
            $newUserModel->setPasswordProfile($newUserPasswordProfile);
            $newUserModel->setJobTitle($newUser["Lidstatus"]);
            $newUserModel->setPreferredLanguage('nl-NL');
            $newUserModel->setAccountEnabled(true);
            $newUserModel->setMailNickname($newUser["Relatienr"]);
    
            $newUserModel->setDisplayName($newUser["VolledigeNaam"]);
            $newUserModel->setGivenName($newUser["Roepnaam"]);
            $newUserModel->setSurname(trim($newUser["Tussenvoegsels"] .' '. $newUser["Achternaam"]));
            $newUserModel->setUsageLocation("NL");
            $newUserModel->setStreetAddress($newUser["VolledigAdres"]);
            $newUserModel->setCountry($newUser["Land"]);
            $newUserModel->setPostalCode($newUser["Postcode"]);
            $newUserModel->setCity($newUser["Woonplaats"]);
            $newUserModel->setMobilePhone($newUser["Mobiel"]);

            try {
                $msgraph->createUser($newUserModel, $newUser["Email"]);
                $count++;
            } catch (Exception $e) {
                $response = json_decode($e->getResponse()->getBody());
                $message = $response->error->message;
                echo "Oh no! An error has occured. $message";
            }
        }
        $message = "Er zijn $count aantal leden toegevoegd aan Office365";
    }
  
    include '../views/syncleden.view.php';
