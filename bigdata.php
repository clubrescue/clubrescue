<?php
		$dataTableQuery =  'SELECT * , DATEDIFF(CURDATE( ),`LidSinds`) as `DagenLid` FROM `cr_leden` where `Relatienr` = \''.$user->user_login.'\'';
		$database = new Database();
		// Build table
			if(@fopen($path,"r")==true){
			$dataTable .= '<tr>';
			$dataTable .= '<tr>';
			$dataTable .= '<tr>';
			$dataTable .= '</table>';