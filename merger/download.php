<?php
    $filename = './Declaraties/merged.pdf';
    $fileinfo = pathinfo($filename);
    $sendname = $_GET['varname'] . ".pdf";
    header('Content-Type: application/pdf');
    header("Content-Disposition: attachment; filename=\"$sendname\"");
    readfile($filename);
