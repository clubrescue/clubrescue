<?php
	//include __DIR__ . '/../../header.php';
    $filename = './declarations/merged.pdf';
    $fileinfo = pathinfo($filename);
    $sendname = $_GET['varname'] . ".pdf";
    header('Content-Type: application/pdf');
    header("Content-Disposition: attachment; filename=\"$sendname\"");
    readfile($filename);
	//include '../../footer.php';