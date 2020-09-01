<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/util/msgraph/vendor/autoload.php';
require_once __DIR__ . '/util/utility.class.php';

use Microsoft\Graph\Model;

$ini_array = parse_ini_file(CR_INI_PATH);
        
$tenantId = $ini_array["O365_TENANT_ID"];
$clientId = $ini_array["O365_CLIENT_ID"];
$clientSecret = $ini_array["O365_SECRET_ID"];
$redirectUri = $ini_array["O365_REDIRECT_URI"];

$provider = new TheNetworg\OAuth2\Client\Provider\Azure([
    'clientId' => $clientId,
    'clientSecret' => $clientSecret,
    'redirectUri' => $redirectUri
]);

$provider->urlAPI = "https://graph.microsoft.com/v1.0/";
$provider->resource = "https://graph.microsoft.com/";

if (!isset($_GET['code']) && !isset($_SESSION['token'])) {
    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit();

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    unset($_SESSION['token']);
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit();
} else {
    try {
        if (!isset($_SESSION['token'])) {
            $token = $provider->getAccessToken(
                'authorization_code',
                ['code' => $_GET['code']
                ]
            );

            $_SESSION['token'] = $token->getToken();
        }
        if (isset($_SESSION['O365_REDIRECT'])) {
            $url = "https://" . $_SERVER['SERVER_NAME'] . $_SESSION['O365_REDIRECT'];
            unset($_SESSION['O365_REDIRECT']);
            header("Location: $url");
        } else {
            $url = "https://" . $_SERVER['SERVER_NAME'];
            header("Location: $url/clubredders");
        }
        exit();
    } catch (Exception $e) {
        exit($e);
    }
}
