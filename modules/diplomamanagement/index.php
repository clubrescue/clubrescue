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
      
    $welcome = 'Diplomabeheer';
    
    include './views/index.view.php';
	
	//requiresPrivilege function demo.
	if(requiresPrivilege('priv_test_Admin') == true ) {
		echo '<h1>Jij beschikt in deze module over admin rechten.</h1>';
	}elseif(requiresPrivilege('priv_test_Member') == true ) {
		echo '<h1>Jij beschikt in deze module over lid rechten.</h1>';	
	}elseif(requiresPrivilege('priv_test_WPC') == true ) {
		echo '<h1>Jij beschikt in deze module over kader rechten.</h1>';	
	}elseif(requiresPrivilege('priv_test_WPE') == true ) {
		echo '<h1>Jij beschikt in deze module over bestuur rechten.</h1>';	
	}else {
		echo '<h1>Jij beschikt niet over rechten voor deze module.</h1>';
	}