<?php

	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/
	login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();
	
	
	// Include database class
	include 'util/utility.class.php';	
    include 'util/database.class.php';	

    if(current_user_can('administrator') || $user->user_login === 'D271RRP') {	
    
		//$tableQuery =  'SELECT b.`BankrekNr` as `VerenigingsRekeningnummer` , b.`BIC` as `VerenigingsBIC` , b.`VerenigingsNaam`, a.`BankrekNr`, a.`BIC`, a.`VolledigeNaam`, b.`IncassantID`, b.`BasisContributie`, b.`Valuta`, \'\' as `Omschrijving`, a.`Machtigingskenmerk`, a.`MachtigingOndertekend`,\'\' as `Sequence`,\'\' as `Datum`,\'\' as `End to End` FROM `cr_leden` as a left outer join `cr_options` as b on 1=1 where `Lidstatus` IN (\'Lid\', \'JeugdLid\')';

        //$database = new Database();
        //$database->query($tableQuery);
        //$tableResult = $database->resultset();
		
//		$tableQuery =  'SELECT b.`BankrekNr` as `VerenigingsRekeningnummer` , b.`BIC` as `VerenigingsBIC` , b.`VerenigingsNaam`, a.`BankrekNr`, a.`BIC`, a.`VolledigeNaam`, b.`IncassantID`, b.`BasisContributie`, b.`Valuta`, \'\' as `Omschrijving`, a.`Machtigingskenmerk`, a.`MachtigingOndertekend`,\'\' as `Sequence`,\'\' as `Datum`,\'\' as `End to End` FROM `cr_leden` as a left outer join `cr_options` as b on 1=1 where `Lidstatus` IN (\'Lid\', \'JeugdLid\')';

//        $database = new Database();
//        $database->query($tableQuery);
//        $tableResult = $database->resultset();
		
		$VerenigingstableQuery =  'SELECT `VerenigingsNaam` as `name`, `BankrekNr` as `IABN` , `BIC` as `BIC`, `Batch` as `batch`, `IncassantID` as `creditor_id`, `Valuta` as `currency`, `Validatie` as `validate`, `Versie.PAIN.008.001` as `version` FROM `cr_options` Where Actief=1';

        $Verenigingsdatabase = new Database();
        $Verenigingsdatabase->query($VerenigingstableQuery);
        $VerenigingstableResult = $Verenigingsdatabase->resultset();
		
		
//		$LidtableQuery =  'SELECT b.`BankrekNr` as `VerenigingsRekeningnummer` , b.`BIC` as `VerenigingsBIC` , b.`VerenigingsNaam`, a.`BankrekNr`, a.`BIC`, a.`VolledigeNaam`, b.`IncassantID`, b.`BasisContributie`, b.`Valuta`, \'\' as `Omschrijving`, a.`Machtigingskenmerk`, a.`MachtigingOndertekend`,\'\' as `Sequence`,\'\' as `Datum`,\'\' as `End to End` FROM `cr_leden` as a left outer join `cr_options` as b on 1=1 where `Lidstatus` IN (\'Lid\', \'JeugdLid\')';

//        $Liddatabase = new Database();
//        $Liddatabase->query($LidtableQuery);
//        $LidtableResult = $Liddatabase->resultset();

    }
?>
<?php
error_reporting(E_ALL);
require_once("SEPASDD.php");


$config = array_column($VerenigingstableResult,'0');


echo($VerenigingstableResult,'0');
print_r($config);
			
//$config = array("name" => "Texelse Reddingsbrigade",
//                "IBAN" => "NL71RABO0113448716",
//                //"BIC" => "RABONL2U", <- Optional, banks may disallow BIC in future
//                "batch" => True,
//                "creditor_id" => "NL08ZZZ406365290000",
//                "currency" => "EUR",
//                //"validate" => False, <- Optional, will disable internal validation of BIC and IBAN.
//				"version" => "3"
//                );
                
//$payment = array("name" => "Test von Testenstein",
//                 "IBAN" => "NL40BANK1234567890",
//                 //"BIC" => "BANKNL2A", <- Optional, banks may disallow BIC in future
//                 "amount" => "1000",
//                 "type" => "FRST",
//                 "collection_date" => date("Y-m-d"),
//                 "mandate_id" => "1234",
//                 "mandate_date" => date("2014-02-01"),
//                 "description" => "Test transaction"
//                );      

//try{
//    $SEPASDD = new SEPASDD($config);
//    $SEPASDD->addPayment($payment);
//    $xml = $SEPASDD->save();
    
//    if($SEPASDD->validate($xml)){
//		print_r($xml);
//	}else{
//		print_r($SEPASDD->validate($xml));
//	}
//	print_r($SEPASDD->getDirectDebitInfo());
//}catch(Exception $e){
//    echo $e->getMessage();
//    exit;
//}

?>
