<?php

require_once 'msgraph.class.php';
try {
    $msgraph=new MSGraphAPI();
    
    // $newUserModel=new Model\User();
    // $newUserModel->setUserPrincipalName('T2@trb.nu');

    // // Create a new PasswordProfile
    // $newUserPasswordProfile = new Model\PasswordProfile;
    // $newUserPasswordProfile->setForceChangePasswordNextSignIn(true);
    // $newUserPasswordProfile->setPassword('123523fded12!!');
    // $newUserModel->setPasswordProfile($newUserPasswordProfile);

    // $newUserModel->setJobTitle('Lid');
    // $newUserModel->setPreferredLanguage('nl-NL');
    // $newUserModel->setAccountEnabled(true);
    // $newUserModel->setMailNickname("T123");

    // $newUserModel->setDisplayName("Voornaam Achternaam");
    // $newUserModel->setGivenName("Voornaam");
    // $newUserModel->setSurname("Achternaam");
    // $newUserModel->setUsageLocation("NL");
    // $newUserModel->setStreetAddress("Straatnaam 47");
    // $newUserModel->setCountry("Nederland");
    // $newUserModel->setPostalCode("1234 AB");
    // $newUserModel->setCity("AMSTERDAM");
    // $newUserModel->setMobilePhone("06 12 45 56 78");

    // $updateUserModel = new Model\User();
    // $updateUserModel->setUserPrincipalName('T2@trb.nu');

    // $updateUserModel->setDisplayName("Voornaam Achternaam");
    // $updateUserModel->setGivenName("Voornaam");
    // $updateUserModel->setSurname("Achternaam");
    // $updateUserModel->setUsageLocation("NL");
    // $updateUserModel->setStreetAddress("Straatnaam 47");
    // $updateUserModel->setCountry("Nederland");
    // $updateUserModel->setPostalCode("1234 AB");
    // $updateUserModel->setCity("AMSTERDAM");
    // $updateUserModel->setMobilePhone("06 21 45 56 78");

    // $output = $msgraph->createUser($newUserModel); // -> will return new user model
    // $output = $msgraph->updateUser($updateUserModel); // -> will return true/false
    // $output = $msgraph->removeUser('T2@trb.nu'); // -> will return true/false
    // $output = $msgraph->getSingleUser("T2@trb.nu");

    // $output = $msgraph->getGroups();

    // $output = $msgraph->getGroupMembersById('226089f9-f42a-4cf0-96d6-acbe71f62005');
    // $output = $msgraph->getGroups();

    // echo '<pre>';
    // var_dump($output);
} catch (Exception $e) {
    $response = json_decode($e->getResponse()->getBody());
    $message = $response->error->message;
    echo "Oh no! An error has occured. $message";
}
