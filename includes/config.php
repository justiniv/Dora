<?php

	#Constants for Database
		error_reporting(E_ALL);
		//
		$check		=	strtotime('now');
		$fourpm		=	strtotime('TODAY 4:15 pm');
		$evening	=	strtotime('TODAY 9 pm');
		ini_set('memory_limit', '2048M');

		if($check > $evening)
		{
			$d 			= strtotime('tomorrow');
			
			$month 		=	date('m', $d);
			$year		=	date('Y', $d);
			$week		=	date('W', $d);
		
			$day		=	date('d', $d);
			$yest		=	$day -1;
			$yest2		=	date('d') -2;
			$yest3		=	date('d') -3;
			$yest4		=	date('d') -4;
			$yest5		=	date('d') -5;
			
			$stday	 	=	strtotime('-280 DAY');
			
			$mth2		= date("m", $stday);

			$yr2		= date("Y", $stday);

			$dy2		= date("d", $stday);
		
			$stkvoltab	=	$month . 'Voltable';
			
			$st2day	 		=	strtotime('-650 DAY');
				 
			$mth3			= date("m", $st2day);

			$yr3			= date("Y", $st2day);

			$dy3			= date("d", $st2day);
	
			echo $month;
			
			
			
		}
		else
		{
			$d	= strtotime('now');
			
			echo "TODAY";
			$month 		=	date('m', $d);
			$year		=	date('Y', $d);
			$week		=	date('W', $d);
		
			$day		=	date('d', $d);
			$yest		=	$day -1;
			$yest2		=	date('d') -2;
			$yest3		=	date('d') -3;
			$yest4		=	date('d') -4;
			$yest5		=	date('d') -5;
		
			$stday	 		=	strtotime('-280 DAY');
				 
			$mth2			= date("m", $stday);

			$yr2			= date("Y", $stday);

			$dy2			= date("d", $stday);
		
			$STKVOLTAB	=	$month . 'Voltable';
			
			$st2day	 		=	strtotime('-650 DAY');
				 
			$mth3			= date("m", $st2day);

			$yr3			= date("Y", $st2day);

			$dy3			= date("d", $st2day);
		}
		
		
			
			defined("DB_SERVER")	?	null	 	: define("DB_SERVER", "localhost");
			defined("DB_USER")		?	null	 	: define("DB_USER", "root");
			defined("DB_PASS")		?	null	 	: define("DB_PASS", "");
			defined("DB_HOURBASE")	?	null	 	: define("DB_HOURBASE", "4HR");
			defined("DB_DAYBASE")	?	null	 	: define("DB_DAYBASE", "STKS");
			defined("DB_WEEKBASE")	?	null	 	: define("DB_WEEKBASE", "STKSWK");
		
		
		
				$opt		=	[
									PDO::ATTR_ERRMODE				=>	PDO::ERRMODE_WARNING,
									PDO::ATTR_DEFAULT_FETCH_MODE	=>	PDO::FETCH_ASSOC,
									PDO::ATTR_EMULATE_PREPARES		=>	false,
								];
								
				 $user		=	"root";
				
				 $pass		=	"";
		
				$dsn		=	'mysql:host=localhost;dbname=4HR;port=3306;charset=utf8';
				
				
				$hr			=	new PDO($dsn,$user, $pass, $opt);
				
				$dsn1		=	'mysql:host=localhost;dbname=STKS;port=3306;charset=utf8';
				
				$stks		=	new PDO($dsn1,$user, $pass,$opt);
				
				$dsn2		=	'mysql:host=localhost;dbname=STKSWK;port=3306;charset=utf8';
				
				$stkswk		=	new PDO($dsn2,$user, $pass, $opt);
				
				
		
?>