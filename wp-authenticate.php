<?php
/*** UNCOMMENT THIS LINE TO DISPLAY ALL PHP ERRORS ***/// error_reporting(E_ALL);
/*** Please set the following in WP to prevent theme errors from the Sailliant design
wp-config.php in root
line 73
define('WP_DEBUG', false);
***///

init();

function authenticate()
{
    $username = htmlspecialchars($_POST["username"]);
    $password = htmlspecialchars($_POST["password"]);

    $user = get_user_by('login', $username);

    /*** COMPARE FORM PASSWORD WITH WORDPRESS PASSWORD ***/
    if (!wp_check_password($password, $user->data->user_pass, $user->ID)) {
        return false;
    };

    wp_set_current_user($user->ID, $username);

    /*** SET PERMANENT COOKIE IF NEEDED ***/
    if ($_POST["remember"] == "1") {
        wp_set_auth_cookie($user->ID, true);
    } else {
        wp_set_auth_cookie($user->ID);
    }

    /*** REDIRECT USER TO PREVIOUS PAGE ***/
    if (isset($_SESSION["return_to"])) {
        $url = $_SESSION["return_to"];
        unset($_SESSION["return_to"]);
        header("location: $url");
    } else {
        $url = get_site_url();
        header("location: $url/clubredders/");
    };
}

function login()
{
    if (!is_user_logged_in()) {

      /*** REMEMBER THE PAGE TO RETURN TO ONCE LOGGED IN ***/
        $_SESSION["return_to"] = $_SERVER['REQUEST_URI'];

        /*** REDIRECT TO LOGIN PAGE ***/
        $url = get_site_url();
        header("location: $url/wp-login.php?redirect_to=$url/clubredders");
    };
}

function init()
{
    /*** INITIATING PHP SESSION ***/
    if (!session_id()) {
        session_start();
    }

    /*** LOADING WORDPRESS LIBRARIES ***/
    define('WP_USE_THEMES', false);
    $base = strpos($_SERVER['DOCUMENT_ROOT'], 'xampp') === false ? $_SERVER['DOCUMENT_ROOT'] : $_SERVER['DOCUMENT_ROOT'].'/trb.nu';
    require_once $base . '/wp-load.php';
}
