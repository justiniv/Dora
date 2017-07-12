<?php
	require_once("includes/config.php");
	
	Class Database 
	{
			#Might need to make these 3 variables public or static.
			#might have to move variables to config file as constants.
		
		
		public $last_query;
		
		public $d;
		public $month;
		public $check;
		public $evening;
		public $midnight;
		public $fourpm;
		public $morning;
		public $year;
		public $stkvoltab;
		public $dy2;
		public $yr2;
		public $dy3;
		public $yr3;
		public $day;
		public $mth2;
		public $mth3;
		public $stday;
		public $week;
		public $gentab;
		public $hrtab;
		
		
		public $user			=	"root";
		public $pass			=	"";
		
		
		public $STKS;
		public $STKSWK;
		public $HR;
		public $HRS;
		
		
		public $stocks 			= array();
		public $full_stocks 	= array();
		
		public $hr_fSel 		= array();
		public $day_fSel 		= array();
		public $wk_fSel 		= array();
		
		public $hr_cSel 		= array();
		public $day_cSel 		= array();
		public $wk_cSel 		= array();
		
		public $hr_bSel			= array();
		public $day_bSel		= array();
		public $wk_bSel			= array();
		
		//public $maxstk			= sizeof($stocks);
		
		function __construct()
		{
			$this->load_timeVariables();
			$this->load_connections();
			
			$this->load_stklist();
			$this->download_all_google_data_csv();
			
		}
		
		public function load_stklist()
		{
			 
			
			 
				$voltab	= $this->stkvoltab;
				
				$dbkey	=	$this->STKS;
				
				$check	=	$dbkey->prepare("SELECT 1 FROM $voltab");
				$check->execute();
				$rows = $check->rowCount();
				
				
				
				if($rows < 1)
				{
					echo	"ERROR!, NO MONTHLY TABLE FOUND";
					echo	" <br />";
				}
				else
				{
					try
					{
						
						
						$result =	$dbkey->prepare("SELECT DISTINCT SYMBOL FROM STKS . $voltab WHERE VOLUME > 999999 ");
						$result->execute();
						
						while($row = $result->fetch())
						{
							$this->full_stocks[] = $row['SYMBOL'];
							
							#echo $row['SYMBOL'] . "<br />";
						}
						
						$result =	$dbkey->prepare("SELECT DISTINCT SYMBOL FROM STKS . $voltab WHERE VOLUME > 1899999 ");
						$result->execute();
						
						while($row = $result->fetch())
						{
							$this->stocks[] = $row['SYMBOL'];
							
							#echo $row['SYMBOL'] . "<br />";
						}
				
					}
					catch(PDOException $e)
					{
						print "ERROR!, COULDN'T LOAD STOCKS FROM STOCKLIST : " . $e->getMessage()."<br />";
						die();
					}
			
					
				}
				
		}
	
		public function download_all_data_csv()
		{
			foreach($this->full_stocks as $i)
			{
				 $symbol	=	$i;
				 $stkfile	=	$symbol.'.csv';
				 $hrdir		=	'C:/xampp/htdocs/jst/Hist/YAHOO/HOURLY/';
				 $daydir	=	'C:/xampp/htdocs/jst/Hist/YAHOO/DAILY/';
				 $wkdir		=	'C:/xampp/htdocs/jst/Hist/YAHOO/WEEKLY/';
				 
				 $hrfile		=	$hrdir	.	$stkfile;
				 $dayfile		=	$daydir	.	$stkfile;
				 $wkfile		=	$wkdir	.	$stkfile;
				 if(!file_exists($hrfile))
				 {
					try
					{
						set_time_limit(0);
						$chandle		=	curl_init();
						$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
						curl_setopt($chandle, CURLOPT_URL, $url);
						curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
						curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
						curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
						$curlresult		=	CURL_EXEC($chandle);
						#print_r(curl_getinfo($chandle));
						$error = curl_error($chandle);
						CURL_CLOSE($chandle);
					
						
							$new_csv = fopen($hrfile, 'w');
							$bytes = file_put_contents( $hrfile , $curlresult);
							#$bytes = fwrite( $hrfile , $curlresult);
							if($bytes)
							{
								print "All Hourly Information was successfully downloaded for " . $symbol . ".";
								echo "<br/>";
							}
						
							fclose($new_csv);
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
					
					}
					catch(Exception $e)
					{
						print 'Failed fetching csv for: ' . $symbol . PHP_EOL;
					};
				 };
				 
				  if(!file_exists($dayfile))
				 {
					try
					{
					
						$chandle		=	curl_init();
						$url			=	'http://real-chart.finance.yahoo.com/table.csv?s=' . $symbol . '&a=' . $this->mth2 . '&b=' . $this->dy2 . '&c=' . $this->yr2 . '&d=' . $this->month . '&e='. $this->day . '&f=' . $this->year . '&g=d&ignore=.csv';
						curl_setopt($chandle, CURLOPT_URL, $url);
						curl_setopt($chandle, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 3);
						curl_setopt($chandle, CURLOPT_TIMEOUT, 5);
						$curlresult		=	CURL_EXEC($chandle);
						CURL_CLOSE($chandle);
					
					
						$new_csv = fopen($dayfile, 'w');
					 
						$bytes = file_put_contents( $dayfile , $curlresult);
						
						if($bytes)
						{
							print "All Daily Information was successfully downloaded for " . $symbol . ".";
							echo "<br/>";
						}
						
						fclose($new_csv);	
						
					
					}
					catch(Exception $e)
					{
						print 'Failed fetching csv for: ' . $symbol . PHP_EOL;
					};
			
					
					
				 };
				 
				  if(!file_exists($wkfile))
				 {
					try
					{
					
						$chandle		=	curl_init();
						$url			=	'http://real-chart.finance.yahoo.com/table.csv?s=' . $symbol . '&a=' . $this->mth3 . '&b=' . $this->dy3 . '&c=' . $this->yr3 . '&d=' . $this->month . '&e='. $this->day . '&f=' . $this->year . '&g=w&ignore=.csv';
						curl_setopt($chandle, CURLOPT_URL, $url);
						curl_setopt($chandle, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 3);
						curl_setopt($chandle, CURLOPT_TIMEOUT, 5);
						$curlresult		=	CURL_EXEC($chandle);
						CURL_CLOSE($chandle);
					
					
						$new_csv = fopen($wkfile, 'w');
					 
						$bytes = file_put_contents( $wkfile , $curlresult);
						
						
						if($bytes)
						{
							print "All Weekly Information was successfully downloaded for " . $symbol . ". <br/>";
						}
						
						fclose($new_csv);	
					
					}
					catch(Exception $e)
					{
						print 'Failed fetching csv for: ' . $symbol . PHP_EOL;
					};
			
					
					
				 };
				 
				 
			}
			
		}
		
		public function download_all_google_data_csv()
		{
			foreach($this->full_stocks as $i)
			{
				 $symbol	=	$i;
				 $stkfile	=	$symbol.'.csv';
				 $hrdir		=	'C:/xampp/htdocs/jst/Hist/GOOGLE/HOURLY/';
				 $daydir	=	'C:/xampp/htdocs/jst/Hist/GOOGLE/DAILY/';
				 $wkdir		=	'C:/xampp/htdocs/jst/Hist/GOOGLE/WEEKLY/';
				 
				 $hrfile		=	$hrdir	.	$stkfile;
				 
				 
				 if(!file_exists($hrfile))
				 {
					try
					{
						set_time_limit(0);
						$chandle		=	curl_init();
						$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
						curl_setopt($chandle, CURLOPT_URL, $url);
						curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
						curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
						curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
						$curlresult		=	CURL_EXEC($chandle);
						#print_r(curl_getinfo($chandle));
						$error = curl_error($chandle);
						CURL_CLOSE($chandle);
					
						
							$new_csv = fopen($hrfile, 'w');
							$bytes = file_put_contents( $hrfile , $curlresult);
							#$bytes = fwrite( $hrfile , $curlresult);
							if($bytes)
							{
								print "All Hourly Information was successfully downloaded for " . $symbol . ".";
								echo "<br/>";
							}
						
							fclose($new_csv);
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
							if(filesize($hrfile) < 2500)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'https://www.google.com/finance/getprices?q=' . $symbol .'&i=300&p=2d&f=d,o,h,l,c,v';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_csv = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_csv);
							}
					
					}
					catch(Exception $e)
					{
						print 'Failed fetching csv for: ' . $symbol . PHP_EOL;
					};
				 };
			}
			
		}
		
		public function download_all_data_json()
		{
			foreach($this->full_stocks as $i)
			{
				 $symbol	=	$i;
				 $stkfile	=	$symbol.'.json';
				 $hrdir		=	'C:/xampp/htdocs/jst/Hist/YAHOO/HOURLY/';
				 $daydir	=	'C:/xampp/htdocs/jst/Hist/YAHOO/DAILY/';
				 $wkdir		=	'C:/xampp/htdocs/jst/Hist/YAHOO/WEEKLY/';
				 
				 $hrfile		=	$hrdir	.	$stkfile;
				 $dayfile		=	$daydir	.	$stkfile;
				 $wkfile		=	$wkdir	.	$stkfile;
				 if(!file_exists($hrfile))
				 {
					try
					{
						set_time_limit(0);
						$chandle		=	curl_init();
						$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
						curl_setopt($chandle, CURLOPT_URL, $url);
						curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
						curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
						curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
						$curlresult		=	CURL_EXEC($chandle);
						#print_r(curl_getinfo($chandle));
						$error = curl_error($chandle);
						CURL_CLOSE($chandle);
					
						
							$new_json = fopen($hrfile, 'w');
							$bytes = file_put_contents( $hrfile , $curlresult);
							#$bytes = fwrite( $hrfile , $curlresult);
							if($bytes)
							{
								print "All Hourly Information was successfully downloaded for " . $symbol . ".";
								echo "<br/>";
							}
						
							fclose($new_json);
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
					if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
					if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
							if(filesize($hrfile) < 5000)
							{
								unlink($hrfile);
								print "File for " . $symbol . " has been deleted due to file size. <br/>"; 
								set_time_limit(0);
								$chandle		=	curl_init();
								$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/json';
								curl_setopt($chandle, CURLOPT_URL, $url);
								curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 2);
								curl_setopt($chandle, CURLOPT_TIMEOUT, 2);
								$curlresult		=	CURL_EXEC($chandle);
								
								$error = curl_error($chandle);
								CURL_CLOSE($chandle);
					
						
								$new_json = fopen($hrfile, 'w');
								$bytes = file_put_contents( $hrfile , $curlresult);
								
								if($bytes)
								{
									print " Hourly Information was downloaded again for " . $symbol . ".";
									echo "<br/>";
								}
								fclose($new_json);
							}
					
					}
					catch(Exception $e)
					{
						print 'Failed fetching json for: ' . $symbol . PHP_EOL;
					};
				 };
				 
				  if(!file_exists($dayfile))
				 {
					try
					{
					
						$chandle		=	curl_init();
						$url			=	'http://real-chart.finance.yahoo.com/table.json?s=' . $symbol . '&a=' . $this->mth2 . '&b=' . $this->dy2 . '&c=' . $this->yr2 . '&d=' . $this->month . '&e='. $this->day . '&f=' . $this->year . '&g=d&ignore=.json';
						curl_setopt($chandle, CURLOPT_URL, $url);
						curl_setopt($chandle, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 3);
						curl_setopt($chandle, CURLOPT_TIMEOUT, 5);
						$curlresult		=	CURL_EXEC($chandle);
						CURL_CLOSE($chandle);
					
					
						$new_json = fopen($dayfile, 'w');
					 
						$bytes = file_put_contents( $dayfile , $curlresult);
						
						if($bytes)
						{
							print "All Daily Information was successfully downloaded for " . $symbol . ".";
							echo "<br/>";
						}
						
						fclose($new_json);	
						
					
					}
					catch(Exception $e)
					{
						print 'Failed fetching json for: ' . $symbol . PHP_EOL;
					};
			
					
					
				 };
				 
				  if(!file_exists($wkfile))
				 {
					try
					{
					
						$chandle		=	curl_init();
						$url			=	'http://real-chart.finance.yahoo.com/table.json?s=' . $symbol . '&a=' . $this->mth3 . '&b=' . $this->dy3 . '&c=' . $this->yr3 . '&d=' . $this->month . '&e='. $this->day . '&f=' . $this->year . '&g=w&ignore=.json';
						curl_setopt($chandle, CURLOPT_URL, $url);
						curl_setopt($chandle, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 3);
						curl_setopt($chandle, CURLOPT_TIMEOUT, 5);
						$curlresult		=	CURL_EXEC($chandle);
						CURL_CLOSE($chandle);
					
					
						$new_json = fopen($wkfile, 'w');
					 
						$bytes = file_put_contents( $wkfile , $curlresult);
						
						
						if($bytes)
						{
							print "All Weekly Information was successfully downloaded for " . $symbol . ". <br/>";
						}
						
						fclose($new_json);	
					
					}
					catch(Exception $e)
					{
						print 'Failed fetching json for: ' . $symbol . PHP_EOL;
					};
			
					
					
				 };
				 
				 
			}
			
		}

		public function files_to_server()
		{
				$ftp_server		=	"ftp.quantinel.com";
				$ftp_username	=	"justinovich@quantinel.com";
				$ftp_userpass	=	"Simple1234";
				
				$ftp_conn	=	ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
				$login		=	ftp_login($ftp_conn, $ftp_username, $ftp_userpass);
				
				if($login)
				{
					echo " ftp connection established.";
				}
				else
				{
					echo "Couldn't establish an ftp connection.";
				}
				
				$dirname	=	$this->month . "." . $this->day . "." . $this->year ;
				$hrdir		=	'/home/quantine/quantinel.com/justinovich/HOURLY/' . $hrdir ;
				
				if (ftp_mkdir($ftp_conn, $hrdir))
				{
					echo "successfully created $hrdir\n";
				}
				else 
				{
					echo "There was a problem while creating $hrdir\n";
				}
				foreach($this->full_stocks as $i)
				{
				
					$symbol		=	$i;
					$stkfile	=	$symbol.'.csv';
				
					$hrfile		=	$hrdir	. '/' .	$stkfile;
					
					if(!$hrfile)
					{
						$ftp_file 	= fopen($hrfile,"r");
						
						set_time_limit(0);
						$chandle		=	curl_init();
						$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $symbol .'/chartdata;type=quote;range=20d/csv';
						curl_setopt($chandle, CURLOPT_URL, $url);
						curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($chandle, CURLOPT_FOLLOWLOCATION, true);
						curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 5);
						curl_setopt($chandle, CURLOPT_TIMEOUT, 5);
						$curlresult		=	CURL_EXEC($chandle);
						$error = curl_error($chandle);
						CURL_CLOSE($chandle);
					
						$upload = ftp_nb_put($ftp_conn, $ftp_file, $curlresult, FTP_BINARY);
						
						if($upload == FTP_FINISHED )
						{
							echo " Upload Complete for " . $symbol . ".";
						}
						elseif($upload == FTP_MOREDATA)
						{
							$upload = ftp_nb_continue($ftp_conn);
						}
						
						ftp_close($ftp_conn);
						fclose($ftp_file);
					}
				
				}
		}
		public function download_hr_data2()
		{
			foreach($this->stocks as $i)
			{
				 $stkfile	=	$i.'.csv';
				 $newDir	=	$this->month . "." . $this->day . "." . $this->year;
				 $newDir	=	'C:/xampp/htdocs/jst/Hist/YAHOO/HOURLY/' . $newDir;
				 mkdir($newDir);
				 $hrdir		=	$newDir . '/';
				 $hrfile	=	$hrdir	.	$stkfile;

				 if(!file_exists($hrfile))
				 {
					try
					{
						$chandle		=	curl_init();
						$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $i .'/chartdata;type=quote;range=45d/csv';
						curl_setopt($chandle, CURLOPT_URL, $url);
						curl_setopt($chandle, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 3);
						curl_setopt($chandle, CURLOPT_TIMEOUT, 5);
						$curlresult		=	CURL_EXEC($chandle);
						CURL_CLOSE($chandle);

						$new_csv = fopen($hrfile, 'w');
					 
						$bytes = file_put_contents( $hrfile , $curlresult);

						if($bytes)
						{
							print "All Hourly Information was successfully downloaded for " . $i . ".";
							echo "<br/>";
						}
						fclose($new_csv);
					}
					catch(Exception $e)
					{
						print 'Failed fetching csv for: ' . $i . PHP_EOL;
					};
				 };
			}
		}
		
		public function download_hr_data()
		{
			foreach($this->stocks as $i)
			{
				 $stkfile	=	$i.'.csv';
				
				
				 $hrdir		=	'C:/xampp/htdocs/jst/Hist/YAHOO/HOURLY/';
				 $hrfile	=	$hrdir	.	$stkfile;

				 if(!file_exists($hrfile))
				 {
					try
					{
						$chandle		=	curl_init();
						$url			=	'http://chartapi.finance.yahoo.com/instrument/1.0/' . $i .'/chartdata;type=quote;range=45d/csv';
						curl_setopt($chandle, CURLOPT_URL, $url);
						curl_setopt($chandle, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($chandle, CURLOPT_CONNECTTIMEOUT, 3);
						curl_setopt($chandle, CURLOPT_TIMEOUT, 5);
						$curlresult		=	CURL_EXEC($chandle);
						CURL_CLOSE($chandle);

						$new_csv = fopen($hrfile, 'w');
					 
						$bytes = file_put_contents( $hrfile , $curlresult);

						if($bytes)
						{
							print "All Hourly Information was successfully downloaded for " . $i . ".";
							echo "<br/>";
						}
						fclose($new_csv);
					}
					catch(Exception $e)
					{
						print 'Failed fetching csv for: ' . $i . PHP_EOL;
					};
				 };
			}
		}
		
		
	
		public function load_connections()
		{
				$opt		=	[
									PDO::ATTR_ERRMODE				=>	PDO::ERRMODE_WARNING,
									PDO::ATTR_DEFAULT_FETCH_MODE	=>	PDO::FETCH_ASSOC,
									PDO::ATTR_EMULATE_PREPARES		=>	false,
								];
		
				$dsn		=	'mysql:host=localhost;dbname=4HR;port=3306;charset=utf8';
				
				$hr			=	new PDO($dsn, $this->user, $this->pass, $opt);
				
				$dsn3		=	'mysql:host=localhost;dbname=HR;port=3306;charset=utf8';
				
				$hrs		=	new PDO($dsn3, $this->user, $this->pass, $opt);
				
				$dsn1		=	'mysql:host=localhost;dbname=STKS;port=3306;charset=utf8';
				
				$stks		=	new PDO($dsn1,$this->user, $this->pass,$opt);
				
				$dsn2		=	'mysql:host=localhost;dbname=STKSWK;port=3306;charset=utf8';
				
				$stkswk		=	new PDO($dsn2,$this->user, $this->pass, $opt);
				
				$this->HR		=	$hr;
				$this->HRS		=	$hrs;
				$this->STKS		=	$stks;
				$this->STKSWK	=	$stkswk;
			
		}
		
		public function load_timeVariables()
		{
			$this->check	=	strtotime('now');
			$this->fourpm	=	strtotime('4:15 PM');
			$this->evening	=	strtotime('TODAY 9 PM');
			$this->morning	=	strtotime('TODAY 7 AM');
			$this->midnight	=	strtotime('TODAY 11:59 PM');
			
			
			if($this->check > $this->fourpm )
			{
					$this->d 			= 	strtotime('tomorrow');
			
					ECHO "<br />" . date('gis',$this->d) . " tomorrow". "<br />";
					
					$this->month 		=	date('m', $this->d);
					$this->year			=	date('Y', $this->d);
					$this->week			=	date('W', $this->d);
					
					$this->day			=	date('d', $this->d);
					$this->yest			=	$this->day -1;
					$this->yest2		=	$this->day -2;
					$this->yest3		=	$this->day -3;
					$this->yest4		=	$this->day -4;
					$this->yest5		=	$this->day -5;
					
					$this->stday	 	=	strtotime('-450 DAY');
			
					$this->mth2			= date("m", $this->stday);

					$this->yr2			= date("Y", $this->stday);

					$this->dy2			= date("d", $this->stday);
					
					$this->stkvoltab	=	$this->month . 'Voltable';
					
					$this->gentab		=	"GENTAB" . $this->month . $this->day . $this->year ;
					$this->hrtab		=	"HRTAB" . $this->month . $this->day . $this->year ;
	
					echo $this->month ."<br />";
					
			}
			else
			{
				
				$this->d				=	strtotime('now');
			
				$this->month 			=	date('m', $this->d);
				$this->year				=	date('Y', $this->d);
				$this->week				=	date('W', $this->d);
		
				$this->day				=	date('d', $this->d);
				$this->yest				=	$this->day -1;
				$this->yest2			=	$this->day -2;
				$this->yest3			=	$this->day -3;
				$this->yest4			=	$this->day -4;
				$this->yest5			=	$this->day -5;
		
				$this->stday	 		=	strtotime('-450 DAY');
				 
				$this->mth2				=	date("m", $this->stday);

				$this->yr2				=	date("Y", $this->stday);

				$this->dy2				=	date("d", $this->stday);
		
				$this->stkvoltab		=	$this->month . 'Voltable';
				
				$this->gentab		=	"GENTAB" . $this->month . $this->day . $this->year ;
				$this->hrtab		=	"HRTAB" . $this->month . $this->day . $this->year ;
				
			}
			
			
		}
		
		public function build_Gen($var)
		{
			
			
			if($var == 'HR')
			{
			
				$gentab				=	$this->gentab;
				$hrtab				=	$this->hrtab;
			
				echo "<hr />" . $gentab . "<hr />";
				$HR						=	$this->HR;
				$HRS					=	$this->HRS;
			
				
				
				
					$creategentab  = " CREATE TABLE 4HR . $gentab (SYMBOL VARCHAR(5) NULL, ";
					$creategentab .= " TTIME VARCHAR(5) NULL, CDIV DECIMAL(50,1) NULL, ";
					$creategentab .= " FDIV DECIMAL(50,1) NULL, CMEASURE DECIMAL(15) NULL, "; 
					$creategentab .= " FMEASURE DECIMAL(50,3) NULL, TRIB DECIMAL(15) NULL,  ";
					$creategentab .= " TRIB2 DECIMAL(15) NULL, RECOMM VARCHAR(15) NULL,";
					$creategentab .= " TRAK DECIMAL(5,2) NULL, TRAK2 DECIMAL(5,2) NULL, ";
					$creategentab .= " HR VARCHAR(15) NULL, HRS DECIMAL(5,3) NULL, ";
					$creategentab .= " HRX VARCHAR(15) NULL,  ";
					$creategentab .= " EXTB DECIMAL(15) NULL, EXTB2 VARCHAR(15) NULL,";
					$creategentab .= " FLEVEL DECIMAL(5) NULL, CLEVEL DECIMAL(15) NULL,";
					$creategentab .= " FCOM DECIMAL(15,3) NULL, ";
					$creategentab .= " reg_date TIMESTAMP)";
				
					$create	=	$HR->prepare($creategentab);
					$create->execute();
					
					
					$creategentab  = " CREATE TABLE HR . $hrtab (SYMBOL VARCHAR(6) NULL, ";
					$creategentab .= " CLOSE DECIMAL(50,3) NULL,";
					$creategentab .= " HRCN DECIMAL(50,3) NULL, HRCD DECIMAL(50,3) NULL,";
					$creategentab .= " HRCD2 DECIMAL(50,1) NULL, HRFN DECIMAL(20,3) NULL, ";
					$creategentab .= " 4HRCN DECIMAL(5,3) NULL, 4HRCD DECIMAL(5,3) NULL, ";
					$creategentab .= " 4HRCD2 DECIMAL(5,3) NULL, 4HRFN DECIMAL(5,3) NULL, ";
					$creategentab .= " 4HRX VARCHAR(15) NULL, 4HRXN VARCHAR(15) NULL, ";
					$creategentab .= " RECOMM VARCHAR(15) NULL, MEMO VARCHAR(15) NULL, ";
					$creategentab .= " reg_date TIMESTAMP)";
				
					$create	=	$HR->prepare($creategentab);
					$create->execute();
					
					
					
					
				
				
			}
			elseif($var == 'STKS')
			{
			
				$gentab				=	$this->gentab;
			
				$STKS				=	$this->STKS;
				
				$check				=	"SHOW TABLES LIKE $gentab";
				$checkres			=	$STKS->prepare($check);
				$checkres->execute();
				$count 				=	$checkres->rowCount();
				
				if($count < 1)
				{
					$creategentab  	  = " CREATE TABLE $gentab (SYMBOL VARCHAR(30) NULL, TRADE_DATE DATE, ";
					$creategentab 	 .= " OPEN DECIMAL(50,3) NULL, HIGH DECIMAL(50,3) NULL, LOW DECIMAL(50,3) NULL, ";
					$creategentab 	 .= " CLOSE DECIMAL(50,3) NULL, PRICE_DEG VARCHAR(15) NULL, FRIES DECIMAL(50,3) NULL, ";
					$creategentab 	 .= " FRIES_DEG VARCHAR(15) NULL, RECOMM VARCHAR(15) NULL, CHOCO DECIMAL(50,3) NULL, ";
					$creategentab 	 .= " LEVELS VARCHAR(5) NULL, CHOCO_DEG VARCHAR(15) NULL, TRIG DECIMAL(50,3) NULL, ";
					$creategentab 	 .= " TTIME VARCHAR(5) NULL, EXTB VARCHAR(15) NULL, CMEASURE DECIMAL(50,3) NULL, ";
					$creategentab 	 .= " reg_date TIMESTAMP)";
					
					$create	=	$STKS->prepare($creategentab);
					$create->execute();
				}
				
			}
			elseif($var =='STKSWK')
			{
				$gentab				=	$this->gentab;
			
				$STKSWK				=	$this->STKSWK;
				
				$check				=	"SHOW TABLES LIKE $gentab";
				$checkres			=	$STKSWK->prepare($check);
				$checkres->execute();
				$count 				=	$checkres->rowCount();
				
				
				if($count < 1)
				{
					$creategentab  	  = " CREATE TABLE $gentab (SYMBOL VARCHAR(30) NULL, TRADE_DATE DATE, ";
					$creategentab 	 .= " OPEN DECIMAL(50,3) NULL, HIGH DECIMAL(50,3) NULL, LOW DECIMAL(50,3) NULL, ";
					$creategentab 	 .= " CLOSE DECIMAL(50,3) NULL, PRICE_DEG VARCHAR(15) NULL, FRIES DECIMAL(50,3) NULL, ";
					$creategentab 	 .= " FRIES_DEG VARCHAR(15) NULL, RECOMM VARCHAR(15) NULL, CHOCO DECIMAL(50,3) NULL, ";
					$creategentab 	 .= " LEVELS VARCHAR(5) NULL, CHOCO_DEG VARCHAR(15) NULL, TRIG DECIMAL(50,3) NULL, ";
					$creategentab 	 .= " reg_date TIMESTAMP)";
					
					$create	=	$STKSWK->prepare($creategentab);
					$create->execute();
					unset($create);
				}
				
				
				
			}
			
			
			
			
		}
		
		
	}

	?>