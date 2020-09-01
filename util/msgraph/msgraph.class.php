<?php

require __DIR__ . '/vendor/autoload.php';

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

class MSGraphAPI
{
    private $accessToken;

    public function __construct()
    {
        $ini_array = parse_ini_file(CR_INI_PATH);
        
        $tenantId = $ini_array["O365_TENANT_ID"];
        $clientId = $ini_array["O365_CLIENT_ID"];
        $clientSecret = $ini_array["O365_SECRET_ID"];
    
        $guzzle = new Client();
        $url = "https://login.microsoftonline.com/$tenantId/oauth2/token?api-version=1.0";
        $token = json_decode($guzzle->post($url, [
            'form_params' => [
                'client_id' => $clientId,
                'client_secret' => htmlspecialchars($clientSecret),
                'resource' => 'https://graph.microsoft.com/',
                'grant_type' => 'client_credentials'
            ],
        ])->getBody()->getContents());
        
        $this->accessToken = $token->access_token;
    }
    
    public function getUsers()
    {
        $graph = new Graph();
        $graph->setAccessToken($this->accessToken);

        $users = $graph->createRequest("GET", '/users?$top=500')
                      ->setReturnType(Model\User::class)
                      ->execute();

        return $users;
    }

    public function getSingleUser($userPrincipalName)
    {
        $graph = new Graph();
        $graph->setAccessToken($this->accessToken);

        $user = $graph->createRequest("GET", "/users/$userPrincipalName")
                      ->setReturnType(Model\User::class)
                      ->execute();

        return $user;
    }

    public function getSingleUserGroupNames($userPrincipalName)
    {
        $graph = new Graph();
        $graph->setAccessToken($this->accessToken);

        $groups = $graph->createRequest("GET", "/users/$userPrincipalName/memberOf".'?$select=displayName')
                      ->setReturnType(Model\Group::class)
                      ->execute();

        $returnArray = [];

        foreach ($groups as $group) {
            $returnArray[] = $group->getDisplayName();
        }
        return $returnArray;
    }

    public function getSingleUserGroups($userPrincipalName)
    {
        $graph = new Graph();
        $graph->setAccessToken($this->accessToken);

        $groups = $graph->createRequest("GET", "/users/$userPrincipalName/memberOf".'?$select=displayName')
                      ->setReturnType(Model\Group::class)
                      ->execute();

        return $groups;
    }

    public function getSingleUserLicenses($userPrincipalName)
    {
        $graph = new Graph();
        $graph->setAccessToken($this->accessToken);

        $licenses = $graph->createRequest("GET", "/users/$userPrincipalName/licenseDetails")
                      ->setReturnType(Model\LicenseDetails::class)
                      ->execute();

        return $licenses;
    }

    public function createUser($userModel, $emailForPassword)
    {
        $graph = new Graph();
        $graph->setAccessToken($this->accessToken);
        
        $newUser = $graph->createRequest("POST", "/users")
                        ->attachBody($userModel)
                        ->setReturnType(Model\User::class)
                        ->execute();

        $assignLicense = $this->_assignDefaultLicense($userModel);

        if (!$assignLicense) {
            throw new Exception('Assigning licenses to the new user failed.');
        }

        $sendEmail = $this->_sendConfirmationMail($userModel, $emailForPassword);

        if (!$sendEmail) {
            throw new Exception('Sending out confirmation email to the new user failed.');
        }
        
        return $newUser;
    }

    public function updateUser($userModel)
    {
        $graph = new Graph();
        $graph->setAccessToken($this->accessToken);
        $userPrincipalName = $userModel->getUserPrincipalName();
        
        $response = $graph->createRequest("PATCH", "/users/$userPrincipalName")
                        ->attachBody($userModel)
                        ->execute();
        return $response->getStatus() === 204;
    }

    public function removeUser($userPrincipalName)
    {
        $graph = new Graph();
        $graph->setAccessToken($this->accessToken);
        
        $response = $graph->createRequest("DELETE", "/users/$userPrincipalName")
                        ->execute();

        return $response->getStatus() === 204;
    }

    public function getGroupById($groupObjectId)
    {
        $graph = new Graph();
        $graph->setAccessToken($this->accessToken);

        $group = $graph->createRequest("GET", "/groups/$groupObjectId")
                      ->setReturnType(Model\Group::class)
                      ->execute();

        return $group;
    }

    public function getAllGroups()
    {
        $graph = new Graph();
        $graph->setAccessToken($this->accessToken);

        $groups = $graph->createRequest("GET", "/groups")
                      ->setReturnType(Model\Group::class)
                      ->execute();

        return $groups;
    }

    public function getGroupMembersById($groupId)
    {
        $graph = new Graph();
        $graph->setAccessToken($this->accessToken);

        $members = $graph->createRequest("GET", "/groups/$groupId/members")
                      ->setReturnType(Model\Group::class)
                      ->execute();

        return $members;
    }

    private function _assignDefaultLicense($userModel)
    {
        $license = new Model\LicenseAssignmentState();
        $license->setSkuId('18181a46-0d4e-45cd-891e-60aabd171b4e'); // STANDARDPACK -> Microsoft E1 Licence
        $license->setDisabledPlans(["54fc630f-5a40-48ee-8965-af0503c1386e","94065c59-bc8e-4e8b-89e5-5138d471eaff","b8afc642-032e-4de5-8c0a-507a7bba7e5d","33c4f319-9bdd-48d6-9c4d-410b750a4a5a","c63d4d19-e8cb-460e-b37c-4d6c34603745","5e62787c-c316-451f-b873-1d05acd4d12c","159f4cd6-e380-449f-a816-af1a9ef76344","743dd19e-1ce3-4c62-a3ad-49ba8f63a2f6","8c7d2df8-86f0-4902-b2ed-a0458298f3b3","0f9b09cb-62d1-4ff4-9129-43f4996f83f4","92f7a6f3-b89b-4bbd-8c30-809e6da5ad1c","57ff2da0-773e-42df-b2af-ffb7a2317929","e95bec33-7c88-4a70-8e19-b10bd9d0c014","b737dad2-2f6c-4c65-90e3-ca563267e8b9","a23b959c-7ce8-4e57-9140-b90eb88a9e97","882e1d05-acd1-4ccb-8708-6ee03664b117","7547a3fe-08ee-4ccb-b430-5077c5041653", "0feaeb32-d00e-4d66-bd5a-43b5b83db82c","c7699d2e-19aa-44de-8edf-1736da088ca1","9aaf7827-d63c-4b61-89c3-182f06f82e5c"]);
   
        $graph = new Graph();
        $graph->setAccessToken($this->accessToken);
        
        $response = $graph->createRequest("POST", "/users//".$userModel->getUserPrincipalName()."/assignLicense")
                        ->attachBody(array(
                            "addLicenses" => [$license],
                            "removeLicenses" => null
                        ))
                        ->execute();
        return $response->getStatus() === 200;
    }

    private function _sendConfirmationMail($userModel, $emailAddress)
    {
        $message = new Model\Message();

        $password = htmlspecialchars($userModel->getPasswordProfile()->getPassword());

        $message->setSubject('Nieuw acount voor de Texelse Reddingsbrigade');

        $parsedBody = file_get_contents(__DIR__.'/mailtemplate.html');

        // Add username
        $parsedBody = str_replace("{{username}}", $userModel->getUserPrincipalName(), $parsedBody);
        // Add password
        $parsedBody = str_replace("{{password}}", $password, $parsedBody);

        $messageBody = (object)[];
        $messageBody->contentType = 'Html';
        $messageBody->content = $parsedBody;
        $message->setBody($messageBody);
    
        $messageRecipients = (object)[];
        $messageRecipients->emailAddress = (object)[];
        $messageRecipients->emailAddress->address = $emailAddress;
        $message->setToRecipients([$messageRecipients]);

        // Add BCC for Webmasters
        $messageBCC = (object)[];
        $messageBCC->emailAddress = (object)[];
        $messageBCC->emailAddress->address = 'webmaster@trb.nu';
        $message->setBccRecipients([$messageBCC]);

        return $this->sendMail($message);
    }

    public function sendMail($messageModel)
    {
        $graph = new Graph();
        $graph->setAccessToken($this->accessToken);

        $body = (object)[];
        $body->Message = $messageModel;
        
        $response = $graph->createRequest("POST", "/users/noreply@trb.nu/sendMail")
                        ->attachBody($body)
                        ->execute();
                        
        return $response->getStatus() === 202;
    }
}
