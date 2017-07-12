<?php
	require_once("includes/config.php");
	require_once("includes/database.php");
	Class	Stock	
	{
		
		
		public $HR;
		public $HRS;
		public $STKS;
		public $STKSWK;
		
		public $user			=	"root";
		public $pass			=	"";
		
		public $four_highs 		= array();
		public $four_lows 		= array();
		public $four_opens 		= array();
		public $four_closes		= array();
		public $four_typrice	= array();
		
		public $hrs_highs 		= array();
		public $hrs_lows 		= array();
		public $hrs_opens 		= array();
		public $hrs_closes		= array();
		public $hrs_typrice		= array();
		
		public $four_fries		= array();
		public $hrs_fries		= array();
		
		public $four_choco		= array();
		public $four_clevel		= array();
		
		public $hrs_choco		= array();
		public $hrs_clevel		= array();
		
		public $volume			=	FALSE;
		public $hrFstar			=	FALSE;
		public $hrBstar			=	FALSE;
		public $hrCstar			=	FALSE;
		
		public $question		=	FALSE;
		public $green			=	FALSE;
		public $red				=	FALSE;
		
		public $rstar			=	FALSE;
		public $gstar			=	FALSE;
		public $dayCstar		=	FALSE;
		
		public $wkBstar			=	FALSE;
		public $wkFstar			=	FALSE;
		public $wkCstar			=	FALSE;
		
		public $hrbuy;
		public $hrsell;
		
		
		
		public $day_closes 		= array();
		
		public $day_adjcloses 	= array();
		
		public $day_highs 		= array();
		
		public $day_lows 		= array();
		
		public $day_opens 		= array();
		
		public $day_typrice		= array();
		
		
		public $week_closes 	= array();
		
		public $week_adjcloses 	= array();
		
		public $week_highs 		= array();
		
		public $week_lows 		= array();
		
		public $week_opens 		= array();
		
		public $week_typrice	= array();
		
		
		public	$stock;
		public	$tablename;		
		public	$gentab		=	'gentab';		
		public	$ctable;		
		public	$btable;		
		public	$btable2;		
		public  $ftable;	
		public  $stars		=	int;	
		
		public $cdiv		=	float;
		public $fdiv		=	float;
		public $cmeasure	=	float;
		public $fmeasure	=	float;
		public $triB		=	float;
		public $triB2		=	float;
		public $trak		=	float;
		public $trak2		=	float;
		public $extB		=	float;
		public $extB2		=	float;
		public $flevel		=	float;
		public $clevel		=	float;
		public $fcom		=	float;
		public function __construct($stk)
		{
			$this->id			=	$stk;
			
			if($this->id)
			{
						$this->stock 		=	$this->id;
						$this->tablename 	=	'x' . $this->stock . 'x';
						$this->ctable 		=	$this->stock . 'xcco';
						$this->btable 		=	'x'. $this->stock . 'boll';
						$this->btable2 		=	'x'. $this->stock . 'boll2';
						$this->ftable 		=   $this->stock . 'xfries';
						$this->load_connections();

			}
			else 
			{
				print "ID NOT FOUND! <br />";
			}

		}
		
		#PRICE TABLES...

		public function load_connections()
		{
				$opt		=	[
									PDO::ATTR_ERRMODE				=>	PDO::ERRMODE_WARNING,
									PDO::ATTR_DEFAULT_FETCH_MODE	=>	PDO::FETCH_ASSOC,
									PDO::ATTR_EMULATE_PREPARES		=>	true,
									PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
									PDO::MYSQL_ATTR_LOCAL_INFILE => true,
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
		
		
		public function build_TblHR()
		{
			$HR = $this->HR;
				##CHANGE ALGORITHM FOR 4HR TABLE.
				
				if($this->id)
				{
					$this->stock		=	$this->id;
					
					$stock				=	$this->stock;
					$tablename			=	'x' . $stock . 'x';
					
					$oldtable			=	'x'. $stock .'x';
					$samtab				=	'samx'. $stock;
					$modtable			=	'modx'.$stock;
					$ttablename			=	'x' . $stock . 'tmp';
					$filename			=	$stock . '.csv';
					
					
					
					
					$compath			=	'C:/xampp/htdocs/jst/Hist/YAHOO/HOURLY/' . $filename;
				
					$HR->beginTransaction();
					try
					{
						
					
				
						$sql  = "CREATE TABLE  $ttablename  ( ";
						$sql .= "SYMBOL VARCHAR(30) NULL,";
						$sql .= "TDATE DATE ,";
						$sql .= "THOUR VARCHAR(50),";
						$sql .= "HLF VARCHAR(50),";
						$sql .= "2HLF VARCHAR(50),";
						$sql .= "TIME VARCHAR(5), ";
						$sql .= "TMIN VARCHAR(50),";
						$sql .= "OPEN DECIMAL(50,2)  NULL,";
						$sql .= "HIGH DECIMAL(50,2)  NULL,";
						$sql .= "LOW DECIMAL(50,2)  NULL,";
						$sql .= "CLOSE DECIMAL(50,2)  NULL,";
						$sql .= "index (symbol),";
						$sql .= "PRIMARY KEY (TDATE, THOUR, TMIN, OPEN, HIGH, LOW, CLOSE),";
						$sql .= "reg_date TIMESTAMP)";
			
			
				
						$rslt	=	$HR->prepare($sql);
						$rslt->execute();
						unset($rslt);

						$popTable  = "LOAD DATA INFILE 'C:/xampp/htdocs/jst/Hist/YAHOO/HOURLY/4.26/$filename' INTO TABLE  $ttablename FIELDS ";
						$popTable .= "TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 32 LINES";
						$popTable .= " ( @var1, CLOSE, HIGH, LOW, OPEN ) ";
						$popTable .= " SET TDATE =  from_unixtime(@var1,'%Y-%m-%d'), ";
						$popTable .= " THOUR = from_unixtime(@var1, '%H'), TMIN ";
						$popTable .= " = from_unixtime(@var1, '%i')";
				
						$rslt2		=	$HR->prepare($popTable);
						$rslt2->execute();
						
						
						if($rslt2 === true)
						{
							echo "2st query done. <br />";
						}
						unset($rslt2);
						
						$setsymbol	= "UPDATE  $ttablename SET SYMBOL = ?";
						$symup		=	$HR->prepare($setsymbol);
						$symup->execute([$stock]);
						unset($symup);
						
						$setsymbol	= "DELETE FROM  $ttablename WHERE TDATE < '2016-08-30'";
						$symup		=	$HR->prepare($setsymbol);
						$symup->execute();
						unset($symup);
						
						$sett		 =	 " UPDATE 4HR. $ttablename AS T3, (SELECT T.TDATE FROM $ttablename AS T";
						$sett		.=	 " INNER JOIN ( SELECT TDATE FROM $ttablename WHERE TMIN = 04 AND THOUR = 10";
						$sett		.=	 " ) AS T1 ON T.TDATE = T1.TDATE) AS T2 SET T3.TIME = 'ET' WHERE T3.TDATE = T2.TDATE";
						$updhlf		=	$HR->prepare($sett);
						$updhlf->execute();
						unset($updhlf);
						
						$sethlf2	= " UPDATE 4HR. $ttablename SET TIME = 'LT' WHERE TIME IS NULL ";
						$updhlf2	=	$HR->prepare($sethlf2);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE 4HR . $ttablename SET HLF = 1 WHERE TIME = 'ET' AND THOUR BETWEEN 09 AND 11";
						$updhlf2	=	$HR->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE 4HR . $ttablename SET HLF = 1 WHERE TIME = 'LT' AND THOUR BETWEEN 10 AND 12";
						$updhlf2	=	$HR->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);

						$sethlf		= "UPDATE 4HR . $ttablename SET HLF = 2 WHERE TIME = 'ET' AND THOUR >= 12 ";
						$updhlf2	=	$HR->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE 4HR . $ttablename SET HLF = 2 WHERE TIME = 'LT' AND THOUR >= 13 ";
						$updhlf2	=	$HR->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$altq		= "ALTER TABLE  $ttablename ORDER BY TDATE DESC, THOUR DESC, TMIN DESC ";
						$upalt		=	$HR->prepare($altq);
						$upalt->execute();
						unset($upalt);
				
						$sql2  = "CREATE TABLE $samtab ( ";
						$sql2 .= "id INT(255) NOT NULL AUTO_INCREMENT,"; 
						$sql2 .= "SYMBOL VARCHAR(30) NULL,";
						$sql2 .= "TDATE DATE ,";
						$sql2 .= "THOUR VARCHAR(50),";
						$sql2 .= "HLF VARCHAR(50),";
						$sql2 .= "TIME VARCHAR(5), ";
						$sql2 .= "TMIN VARCHAR(50),";
						$sql2 .= "OPEN DECIMAL(50,2)  NULL,";
						$sql2 .= "HIGH DECIMAL(50,2)  NULL,";
						$sql2 .= "LOW DECIMAL(50,2)  NULL,";
						$sql2 .= "CLOSE DECIMAL(50,2)  NULL,";
						$sql2 .= "index (symbol),";
						$sql2 .= "PRIMARY KEY (id),";
						$sql2 .= "reg_date TIMESTAMP)";
						$sql2up	=	$HR->prepare($sql2);
						$sql2up->execute();
						unset($sql2up);
				
						$trndat		 =	"INSERT INTO  $samtab ( SYMBOL, TDATE, THOUR, HLF, TIME, TMIN, OPEN, HIGH, LOW, CLOSE ) ";
						$trndat		.=	" SELECT SYMBOL, TDATE, THOUR, HLF, TIME, TMIN, OPEN, HIGH, LOW, CLOSE FROM  $ttablename";
						$trndat		.=	" ORDER BY TDATE DESC, THOUR DESC, TMIN DESC";
						$trnup		 =	$HR->prepare($trndat);
						$trnup->execute();
						unset($trnup);
						
						$drptab		=	"DROP TABLE $ttablename";
						$drup		=	$HR->prepare($drptab);
						$drup->execute();
						unset($drup);
						
						echo "<br/> ttable table for " . $stock . " has been dropped. <br/>";
						
						$rnmtab		=	"RENAME TABLE $samtab TO $ttablename";
						$rnmup		=	$HR->prepare($rnmtab);
						$rnmup->execute();
						unset($rnmup);
						
						echo "<br/>  Table for " . $stock . " has been renamed. <br/>";
						$HR->commit();
					}
					catch(PDOException $e)
					{
							print "<br/> ERROR LOADING TABLES: " .$e->getMessage(); 
					}
						
						$ext = False;
					
						$tablecheck	=	"show tables like :tablename";
						$tcres		=	$HR->prepare($tablecheck);
						$tcres->execute([':tablename'=>$tablename]);
						$count = $tcres->rowCount();
						
						
					
				
					
				
					
					
					
					if($tcres ==true && $count > 0)
					{
						echo "<br/>table check for $tablename was True. <br/>";
						$HR->beginTransaction();
						
						try
						{
							$sql  = "CREATE TABLE  $modtable  ( ";
							$sql .= "id INT(255) NOT NULL AUTO_INCREMENT,"; 
							$sql .= "SYMBOL VARCHAR(30) NULL,";
							$sql .= "TDATE DATE ,";
							$sql .= "THOUR VARCHAR(50),";
							$sql .= "OPEN DECIMAL(50,2)  NULL,";
							$sql .= "HIGH DECIMAL(50,2)  NULL,";
							$sql .= "LOW DECIMAL(50,2)  NULL,";
							$sql .= "CLOSE DECIMAL(50,2)  NULL,";
							$sql .= "TYPICAL_PRICE DECIMAL(50,2)   NULL,";
							$sql .= "DEGREES VARCHAR(50) NULL,";
							$sql .= "index (symbol),";
							$sql .= "PRIMARY KEY (id),";
							$sql .= "reg_date TIMESTAMP)";
						
							$tabq	=	$HR->prepare($sql);
							$tabq->execute();
							unset($tabq);
							echo "<br/> MOD table for " . $stock . " has been created. <br/>";
							
							$insrt	 =	"INSERT INTO  $modtable ( SYMBOL, TDATE, THOUR, OPEN ) SELECT t1.SYMBOL, t1.TDATE, ";
							$insrt	.=	" t1.thour, t1.open FROM  HR.$ttablename AS t1 LEFT JOIN  HR. $ttablename as t2 ON ";
							$insrt	.=	"( t1.TDATE = t2.TDATE AND t1.4HLF = t2.4HLF AND t1.ID < t2.ID) ";
							$insrt	.=	"WHERE t2.ID IS NULL ";
							
							$insq	=	$HR->prepare($insrt);
							$insq->execute();
							unset($insrt);
							
							$findID		=	"SELECT ID, TDATE FROM  $modtable";
							$fnd		=	$HR->prepare($findID);
							$fnd->execute();
							$dateArray		=	array();
							$hlfArray		=	array();
							$idArray		=	array();
						
							$ct = 0;
							
							while($row = $fnd->fetch(PDO::FETCH_BOTH))
							{
								$dateArray[$ct] = $row['TDATE'];
								$idArray[$ct]	= $row['ID'];
								$ct++;
								
							}
								
							
							$fnd->closeCursor();
							
							$maxsz	=	sizeof($idArray);
						##$
							for($i = 0; $i < $maxsz; $i++)
							{
								$id		=	$idArray[$i];
								$tdate	=	$dateArray[$i];
							
								$sethigh	 =	"UPDATE 4HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
								$sethigh	.=	" WHERE tdate = ? AND 4HLF = 1),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
								$sethigh	.=	" WHERE tdate = ? AND 4HLF = 1), CLOSE = (SELECT CLOSE FROM HR ."; 
								$sethigh	.=	" $ttablename WHERE tdate = ? AND 4HLF = 1 AND CLOSE IS NOT NULL ";
								$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
								$sethigh	.=	" AND thour < 12";
							
								$sthi	=	$HR->prepare($sethigh);
								$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
								$sthi->closeCursor();
								
								$sethigh	 =	"UPDATE 4HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
								$sethigh	.=	" WHERE tdate = ? AND 4HLF = 2),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
								$sethigh	.=	" WHERE tdate = ? AND 4HLF = 2), CLOSE = (SELECT CLOSE FROM HR ."; 
								$sethigh	.=	" $ttablename WHERE tdate = ? AND 4HLF = '2' AND CLOSE IS NOT NULL";
								$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1 ) WHERE tdate = ?";
								$sethigh	.=	" AND thour BETWEEN 12 AND 14";
								
								$sthi2	=	$HR->prepare($sethigh);
								$sthi2->execute([$tdate,$tdate,$tdate,$tdate]);
								$sthi2->closeCursor();
								
								
							}
							$sqluno		=	"UPDATE  4HR. $modtable SET TYPICAL_PRICE = (SELECT (HIGH+LOW+CLOSE)/3)";
							
							$sthi2		=	$HR->prepare($sqluno);
							$sthi2->execute();
							$sthi2->closeCursor();
							
							
							$oldtable	  =	'x'. $this->stock . 'x';
							
							
							
								$altquery 	=	" ALTER TABLE 4HR. $oldtable MODIFY id INT(5) NOT NULL ";
								$aquery		=	$HR->prepare($altquery);
								$aquery->execute();
								$aquery = NULL;
								
								$altquery2 		=	" ALTER TABLE 4HR.$oldtable DROP COLUMN id ";
								$aquery2		=	$HR->prepare($altquery2);
								$aquery2->execute();
								$aquery2 = NULL;
								
								$altquery3 	=	" ALTER TABLE 4HR.$modtable MODIFY id INT(5) NOT NULL ";
								$aquery3	=	$HR->prepare($altquery3);
								$aquery3->execute();
								$aquery3 = NULL;
								
								$altquery4 		=	" ALTER TABLE 4HR. $modtable DROP COLUMN id ";
								$aquery4		=	$HR->prepare($altquery4);
								$aquery4->execute();
								$aquery4 = NULL;
								
									$thirdtable		=	'ttt' .$stock;
									$sql  = "CREATE TABLE 4HR . $thirdtable  ( ";
									$sql .= "id INT(255) NOT NULL AUTO_INCREMENT,"; 
									$sql .= "SYMBOL VARCHAR(30) NULL,";
									$sql .= "TDATE DATE ,";
									$sql .= "THOUR VARCHAR(50),";
									$sql .= "OPEN DECIMAL(50,2)  NULL,";
									$sql .= "HIGH DECIMAL(50,2)  NULL,";
									$sql .= "LOW DECIMAL(50,2)  NULL,";
									$sql .= "CLOSE DECIMAL(50,2)  NULL,";
									$sql .= "TYPICAL_PRICE DECIMAL(50,2)   NULL,";
									$sql .= "DEGREES VARCHAR(50) NULL,";
									$sql .= "index (symbol),";
									$sql .= "PRIMARY KEY (id),";
									$sql .= "reg_date TIMESTAMP)";
									
									$third	=	$HR->prepare($sql);
									$third->execute();
									$third->closeCursor();
								
								$sql2	 =	" INSERT INTO 4HR. $thirdtable (SYMBOL, TDATE, ";
								$sql2	.=	" THOUR, OPEN, HIGH, LOW, CLOSE, TYPICAL_PRICE)";
								$sql2	.=	" SELECT SYMBOL, TDATE, THOUR, OPEN, HIGH, LOW, ";
								$sql2	.=	" CLOSE, TYPICAL_PRICE FROM 4HR. $modtable";
								$lastquery	 =	$HR->prepare($sql2);
								$lastquery->execute();
								$size 		 = $lastquery->rowCount();
								
								if($lastquery == true)
								{
									$lastque	 =	"INSERT INTO 4HR. $thirdtable ( SYMBOL, TDATE, THOUR, OPEN, HIGH, LOW, CLOSE, ";
									$lastque	.=	" TYPICAL_PRICE, DEGREES ) SELECT SYMBOL, TDATE, THOUR, OPEN, HIGH, LOW, ";
									$lastque	.=	" CLOSE, TYPICAL_PRICE, DEGREES FROM 4HR. $oldtable WHERE $oldtable. TDATE < (SELECT ";
									$lastque	.=	" TDATE FROM 4HR. $modtable ORDER BY TDATE ASC, THOUR ASC LIMIT 1) ORDER BY TDATE DESC,";
									$lastque	.=	" THOUR DESC";
									$lastquery	 =	$HR->prepare($lastque);
									$lastquery->execute();
									$size 		 = $lastquery->rowCount();
									
									$drop 		= " DROP TABLE  4HR . $oldtable ";
									$mquery		=	$HR->prepare($drop);
									$mquery->execute();
									$mquery = NULL;
									
									$drop 		= " DROP TABLE  4HR .$modtable ";
									$mquery		=	$HR->prepare($drop);
									$mquery->execute();
									$mquery = NULL;
									
									$rename			=	" RENAME TABLE $thirdtable TO $oldtable";
									$rmquery2		=	$HR->prepare($rename);
									$rmquery2->execute();
									$rmquery = NULL;
								}

								
								##$
	#################################################################################################
								
								$ccoquery		=	" SELECT ID, CLOSE FROM  4HR. $tablename";
								$cco1			=	$HR->prepare($ccoquery);
								$cco1->execute();
						
								$close	=	array();
								$n		=	0;
						
								while($row = $cco1->fetch(PDO::FETCH_BOTH))
								{
									$close[$n]['ID']	=	$row['ID'];
									$close[$n]['CLOSE']	=	$row['CLOSE'];
									++$n;
								}
								$cco1->closeCursor();
								$maxco	=	sizeof($close);
						
								for($i =0; $i < $maxco; $i++)
								{
							
									$peak	=	"PEAK";
									$valley	=	"VALLEY";
									
									
									$last	= 	$maxco - 1;
							
									if( $i == 0)
									{
										$n		=	$i + 1;
										$tdcci	=	$close[$i]['CLOSE'];
										$ycci	=	$close[$n]['CLOSE'];
										$tid	=	$close[$i]['ID'];
					
										$tid		=	" ' " . $tid . " ' ";
					
					
					
										if($tdcci > $ycci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :peak WHERE ID = $tid";
											$degup			=	$HR->prepare($utd);
											$degup->execute([':peak'=>$peak]);
											unset($degup);
										}
										else if( $tdcci < $ycci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :valley WHERE ID = $tid";
											$degup			=	$HR->prepare($utd);
											$degup->execute([':valley'=>$valley]);
											unset($degup);
										}
					
									}
									else if( $i > 0 && $i < $last)
									{
										$n		=	$i + 1;
										$m		=	$i - 1;
										$tdcci	=	$close[$i]['CLOSE'];
										$ycci	=	$close[$n]['CLOSE'];
										$tmcci	=	$close[$m]['CLOSE'];
										$tid		=	$close[$i]['ID'];
										$tid		=	" ' " . $tid . " ' ";
					
										if( $tdcci > $ycci && $tdcci > $tmcci)
										{
											$utd 			= " UPDATE  $tablename set DEGREES = :peak WHERE ID = $tid";
											$degup			=	$HR->prepare($utd);
											$degup->execute([':peak'=>$peak]);
											unset($degup);
										}
										else if ( $tdcci < $ycci && $tdcci < $tmcci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :valley WHERE ID = $tid";
											$degup			=	$HR->prepare($utd);
											$degup->execute([':valley'=>$valley]);
											unset($degup);
										}
					
									}
									else if($i == $last)
									{
					
										$m		=	$i - 1;
										$tdcci	=	$close[$i]['CLOSE'];
										$tmcci	=	$close[$m]['CLOSE'];
										$tid	=	$close[$i]['ID'];
										$tid	=	" ' " . $tid . " ' ";
										if($tdcci > $tmcci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :peak  WHERE ID = $tid";
											$degup			=	$HR->prepare($utd);
											$degup->execute([':peak'=>$peak]);
											unset($degup);
										}
										else if($tdcci < $tmcci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :valley  WHERE ID = $tid";
											$degup			=	$HR->prepare($utd);
											$degup->execute([':valley'=>$valley]);
											unset($degup);
										}
					
									}
				
								}	

								$arraysql	= "SELECT OPEN, LOW, HIGH, CLOSE, TYPICAL_PRICE FROM $tablename ";
								$values		= $HR->prepare($arraysql);
								$values->execute();
			
								while($row = $values->fetch(PDO::FETCH_BOTH))
								{
				
				
									$this->four_highs[]		= $row['HIGH'];
									$this->four_lows[]		= $row['LOW'];
									$this->four_opens[]		= $row['OPEN'];
									$this->four_closes[]	= $row['CLOSE'];
									$this->four_typrice[]	= $row['TYPICAL_PRICE'];	
			
								}

								$HR->commit();
						}
						catch(PDOException $e)
						{
							print "<br/> ERROR LOADING TABLES: " .$e->getMessage();
						}
						
							
							
					}
			
					else
					{
						echo "<br/>table check for $tablename was not True. <br/>";
						$HR->beginTransaction();

						try
						{
							$sql  = "CREATE TABLE $tablename ( ";
							$sql .= "id INT(255) NOT NULL AUTO_INCREMENT,"; 
							$sql .= "SYMBOL VARCHAR(30) NULL,";
							$sql .= "TDATE DATE,";
							$sql .= "THOUR VARCHAR(50),";
							$sql .= "OPEN DECIMAL(50,2)  NULL,";
							$sql .= "HIGH DECIMAL(50,2)  NULL,";
							$sql .= "LOW DECIMAL(50,2)  NULL,";
							$sql .= "CLOSE DECIMAL(50,2)  NULL,";
							$sql .= "TYPICAL_PRICE DECIMAL(50,2)   NULL,";
							$sql .= "DEGREES VARCHAR(50) NULL,";
							$sql .= "index (symbol),";
							$sql .= "PRIMARY KEY (id),";
							$sql .= "reg_date TIMESTAMP)";
							
							$que1		=	$HR->prepare($sql);
							$que1->execute();
							unset($que1);
							
							$setw	 =	"INSERT INTO  $tablename ( SYMBOL, TDATE, THOUR, OPEN ) SELECT t1.SYMBOL,";
							$setw	.=	" t1.TDATE, t1.thour, t1.open FROM  $ttablename AS t1 LEFT JOIN $ttablename ";
							$setw	.=	"as t2 ON ( t1.TDATE = t2.TDATE AND t1.HLF = t2.HLF AND t1.ID < t2.ID) ";
							$setw	.=	"WHERE t2.ID IS NULL ";
					
							$que2		=	$HR->prepare($setw);
							$que2->execute();
							unset($que2);
							
							$findID		=	"SELECT ID, TDATE FROM  $tablename";
					
							$que3		=	$HR->prepare($findID);
							$que3->execute();
							
							$dateArray		=	array();
							$idArray		=	array();
						
							while($row = $que3->fetch(PDO::FETCH_BOTH))
							{
								$idArray[]		=	$row['ID'];
								$dateArray[]	=	$row['TDATE'];
						
							}
							$que3->closeCursor();
							$maxsiz	=	sizeof($dateArray);
						
							for($i =0 ; $i < $maxsiz; $i++)
							{
								$id		=	$idArray[$i];
								$tdate	=	$dateArray[$i];
							
								
								$sthi		 =	"UPDATE 4HR. $tablename SET HIGH = (SELECT MAX(HIGH) FROM 4HR . $ttablename ";
								$sthi		.=	" WHERE tdate = '{$tdate}' AND HLF = 1),LOW = (SELECT MIN(LOW) FROM 4HR . $ttablename ";
								$sthi		.=	" WHERE tdate = '{$tdate}' AND HLF = 1), CLOSE = (SELECT CLOSE FROM 4HR ."; 
								$sthi		.=	" $ttablename WHERE tdate = '{$tdate}' AND HLF = '1' AND CLOSE IS NOT NULL";
								$sthi 		.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = '{$tdate}'";
								$sthi		.=	" AND thour < '12'";
							
								$que3		=	$HR->prepare($sthi);
								$que3->execute();
								unset($que3);
								
								$sti		 =	"UPDATE 4HR. $tablename SET HIGH = (SELECT MAX(HIGH) FROM 4HR . $ttablename ";
								$sti		.=	" WHERE tdate = '{$tdate}' AND HLF = 2),LOW = (SELECT MIN(LOW) FROM 4HR . $ttablename ";
								$sti		.=	" WHERE tdate = '{$tdate}' AND HLF = 2), CLOSE = (SELECT CLOSE FROM 4HR ."; 
								$sti		.=	" $ttablename WHERE tdate = '{$tdate}' AND HLF = '2' AND CLOSE IS NOT NULL";
								$sti		.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1 ) WHERE tdate = '{$tdate}'";
								$sti		.=	" AND thour BETWEEN 12 AND 14";
							
								$que4	=	$HR->prepare($sti);
								$que4->execute([$tdate, $tdate, $tdate, $tdate]);
								
								unset($que4);
							
							}
							
							$sqluno		=	"UPDATE  $tablename SET TYPICAL_PRICE = (SELECT (HIGH+LOW+CLOSE)/3)";
							$que5		=	$HR->prepare($sqluno);
							$que5->execute();
							unset($que5);
							$findID		=	"SELECT ID FROM  $ttablename WHERE TMIN = '29' OR TMIN = '00' OR TMIN = '34' ";
							$que6		=	$HR->prepare($findID);
							$que6->execute();
							$que6->closeCursor();
							
							
							$arraysql	= "SELECT OPEN, LOW, HIGH, CLOSE, TYPICAL_PRICE FROM $tablename ";
							$values		= $HR->prepare($arraysql);
							$values->execute();
			
							while($row = $values->fetch(PDO::FETCH_BOTH))
							{
				
				
								$this->four_highs[]		= $row['HIGH'];
								$this->four_lows[]		= $row['LOW'];
								$this->four_opens[]		= $row['OPEN'];
								$this->four_closes[]	= $row['CLOSE'];
								$this->four_typrice[]	= $row['TYPICAL_PRICE'];	
			
							}
						
							$HR->commit();
						}
						catch( PDOException $e)
						{
							print " <br/> ERROR LOADING TABLES: " . $e->getMessage();
						}
							
						
					}
						
						$tcres->closeCursor();
						$clarity		=	"DROP TABLE  $ttablename";
						$que7			=	$HR->prepare($clarity);
						$que7->execute();
						unset($que7);
						
						$ccoquery		=	" SELECT ID, CLOSE FROM  $tablename";
						$cco1			=	$HR->prepare($ccoquery);
						$cco1->execute();
						
						$close	=	array();
						$n		=	0;
						
						while($row = $cco1->fetch(PDO::FETCH_BOTH))
						{
							$close[$n]['ID']	=	$row['ID'];
							$close[$n]['CLOSE']	=	$row['CLOSE'];
							++$n;
						}
						$cco1->closeCursor();
						$maxco	=	sizeof($close);
						
						for($i =0; $i < $maxco; $i++)
						{
							
							$peak	=	"PEAK";
							$valley	=	"VALLEY";
							$last	= 	$maxco - 1;
							
							if( $i == 0)
							{
								$n		=	$i + 1;
								$tdcci	=	$close[$i]['CLOSE'];
								$ycci	=	$close[$n]['CLOSE'];
								$tid	=	$close[$i]['ID'];
					
								$tid		=	" ' " . $tid . " ' ";
					
					
					
								if($tdcci > $ycci)
								{
									$utd = " UPDATE  $tablename set DEGREES = :peak WHERE ID = $tid";
									$degup			=	$HR->prepare($utd);
									$degup->execute([':peak'=>$peak]);
									unset($degup);
								}
								else if( $tdcci < $ycci)
								{
									$utd = " UPDATE  $tablename set DEGREES = :valley WHERE ID = $tid";
									$degup			=	$HR->prepare($utd);
									$degup->execute([':valley'=>$valley]);
									unset($degup);
								}
					
							}
							else if( $i > 0 && $i < $last)
							{
								$n		=	$i + 1;
								$m		=	$i - 1;
								$tdcci	=	$close[$i]['CLOSE'];
								$ycci	=	$close[$n]['CLOSE'];
								$tmcci	=	$close[$m]['CLOSE'];
								$tid		=	$close[$i]['ID'];
								$tid		=	" ' " . $tid . " ' ";
					
								if( $tdcci > $ycci && $tdcci > $tmcci)
								{
									$utd 		= " UPDATE  $tablename set DEGREES = :peak WHERE ID = $tid";
									$degup		=	$HR->prepare($utd);
									$degup->execute([':peak'=>$peak]);
									unset($degup);
								}
								if ( $tdcci < $ycci && $tdcci < $tmcci)
								{
									$utd = " UPDATE  $tablename set DEGREES = :valley WHERE ID = $tid";
									$degup			=	$HR->prepare($utd);
									$degup->execute([':valley'=>$valley]);
									unset($degup);
								}
					
							}
							else if($i == $last)
							{
					
								$m		=	$i - 1;
								$tdcci	=	$close[$i]['CLOSE'];
								$tmcci	=	$close[$m]['CLOSE'];
								$tid	=	$close[$i]['ID'];
								$tid	=	" ' " . $tid . " ' ";
								if($tdcci > $tmcci)
								{
									$utd = " UPDATE  $tablename set DEGREES = :peak  WHERE ID = $tid";
									$degup			=	$HR->prepare($utd);
									$degup->execute([':peak'=>$peak]);
									unset($degup);
								}
								else if($tdcci < $tmcci)
								{
									$utd = " UPDATE  $tablename set DEGREES = :valley  WHERE ID = $tid";
									$degup			=	$HR->prepare($utd);
									$degup->execute([':valley'=>$valley]);
									unset($degup);
								}
					
							}
				
						}	
					
					
					
				}
			
		}
		
		public function build_TblHRS()
		{
			$HRS = $this->HRS;
			$HR	 = $this->HR;
				##CHANGE ALGORITHM FOR HR TABLE.
				
				if($this->id)
				{
					$this->stock		=	$this->id;
					
					$stock				=	$this->stock;
					$tablename			=	'x' . $stock . 'x';
					
					$oldtable			=	'x'. $stock .'x';
					$samtab				=	'samx'. $stock;
					$modtable			=	'modx'.$stock;
					$ttablename			=	'x' . $stock . 'tmp';
					$filename			=	$stock . '.csv';
					$compath			=	'C:/xampp/htdocs/jst/Hist/YAHOO/HOURLY/' . $filename;
			
					$HRS->beginTransaction();
					try
					{
						
					
				
						$sql  = "CREATE TABLE  $ttablename  ( ";
						$sql .= "SYMBOL VARCHAR(30) NULL,";
						$sql .= "TDATE DATE ,";
						$sql .= "THOUR VARCHAR(50),";
						$sql .= "HLF VARCHAR(50),";
						$sql .= "4HLF VARCHAR(50),";
						$sql .= "TIME VARCHAR(5), ";
						$sql .= "TMIN VARCHAR(50),";
						$sql .= "OPEN DECIMAL(50,2)  NULL,";
						$sql .= "HIGH DECIMAL(50,2)  NULL,";
						$sql .= "LOW DECIMAL(50,2)  NULL,";
						$sql .= "CLOSE DECIMAL(50,2)  NULL,";
						$sql .= "index (symbol),";
						$sql .= "PRIMARY KEY (TDATE, THOUR, TMIN, OPEN, HIGH, LOW, CLOSE),";
						$sql .= "reg_date TIMESTAMP)";
			
			
				
						$rslt	=	$HRS->prepare($sql);
						$rslt->execute();
						unset($rslt);

						$popTable  = "LOAD DATA INFILE 'C:/xampp/htdocs/jst/Hist/YAHOO/HOURLY/$filename' INTO TABLE  $ttablename FIELDS ";
						$popTable .= "TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 32 LINES";
						$popTable .= " ( @var1, CLOSE, HIGH, LOW, OPEN ) ";
						$popTable .= " SET TDATE =  from_unixtime(@var1,'%Y-%m-%d'), ";
						$popTable .= " THOUR = from_unixtime(@var1, '%H'), TMIN ";
						$popTable .= " = from_unixtime(@var1, '%i')";
				
						$rslt2		=	$HRS->prepare($popTable);
						$rslt2->execute();
						
						
						if($rslt2 === true)
						{
							echo "2st query done. <br />";
						}
						unset($rslt2);
						
						$setsymbol	= "UPDATE $ttablename SET SYMBOL = ?";
						$symup		=	$HRS->prepare($setsymbol);
						$symup->execute([$stock]);
						unset($symup);
						
						$setsymbol	= "DELETE FROM  $ttablename WHERE TDATE < '2016-08-30'";
						$symup		=	$HRS->prepare($setsymbol);
						$symup->execute();
						unset($symup);
						
						$sett		 =	 " UPDATE HR. $ttablename AS T3, (SELECT T.TDATE FROM $ttablename AS T";
						$sett		.=	 " INNER JOIN ( SELECT TDATE FROM $ttablename WHERE TMIN = 04 AND THOUR = 10";
						$sett		.=	 " ) AS T1 ON T.TDATE = T1.TDATE) AS T2 SET T3.TIME = 'ET' WHERE T3.TDATE = T2.TDATE";
						$updhlf		=	$HRS->prepare($sett);
						$updhlf->execute();
						unset($updhlf);
						
						$sethlf2	= " UPDATE HR. $ttablename SET TIME = 'LT' WHERE TIME IS NULL ";
						$updhlf2	=	$HRS->prepare($sethlf2);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 1 WHERE TIME = 'ET' AND THOUR = 09";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 2 WHERE TIME = 'ET' AND THOUR = 10 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 3 WHERE TIME = 'ET' AND THOUR = 11 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 4 WHERE TIME = 'ET' AND THOUR = 12 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 5 WHERE TIME = 'ET' AND THOUR = 13 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 6 WHERE TIME = 'ET' AND THOUR = 14 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 7 WHERE TIME = 'ET' AND THOUR = 15 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 1 WHERE TIME = 'LT' AND THOUR = 10 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 2 WHERE TIME = 'LT' AND THOUR = 11 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 3 WHERE TIME = 'LT' AND THOUR = 12 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 4 WHERE TIME = 'LT' AND THOUR = 13 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 5 WHERE TIME = 'LT' AND THOUR = 14 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 6 WHERE TIME = 'LT' AND THOUR = 15 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 7 WHERE TIME = 'LT' AND THOUR = 16 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET 4HLF = 1 WHERE TIME = 'ET' AND THOUR BETWEEN 09 AND 11";
						$updhlf2	=	$HR->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET 4HLF = 1 WHERE TIME = 'LT' AND THOUR BETWEEN 10 AND 12";
						$updhlf2	=	$HR->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);

						$sethlf		= "UPDATE HR . $ttablename SET 4HLF = 2 WHERE TIME = 'ET' AND THOUR >= 12 ";
						$updhlf2	=	$HR->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET 4HLF = 2 WHERE TIME = 'LT' AND THOUR >= 13 ";
						$updhlf2	=	$HR->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "DELETE FROM  HR. $ttablename WHERE TIME = 'ET' AND THOUR = 16 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "DELETE FROM  HR. $ttablename WHERE TIME = 'LT' AND THOUR = 17 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						
						$altq		= "ALTER TABLE  $ttablename ORDER BY TDATE DESC, THOUR DESC, TMIN DESC ";
						$upalt		=	$HRS->prepare($altq);
						$upalt->execute();
						unset($upalt);
				
						$sql2  = "CREATE TABLE $samtab ( ";
						$sql2 .= "id INT(255) NOT NULL AUTO_INCREMENT,"; 
						$sql2 .= "SYMBOL VARCHAR(30) NULL,";
						$sql2 .= "TDATE DATE ,";
						$sql2 .= "THOUR VARCHAR(50),";
						$sql2 .= "HLF VARCHAR(50),";
						$sql2 .= "4HLF VARCHAR(50),";
						$sql2 .= "TIME VARCHAR(5), ";
						$sql2 .= "TMIN VARCHAR(50),";
						$sql2 .= "OPEN DECIMAL(50,2)  NULL,";
						$sql2 .= "HIGH DECIMAL(50,2)  NULL,";
						$sql2 .= "LOW DECIMAL(50,2)  NULL,";
						$sql2 .= "CLOSE DECIMAL(50,2)  NULL,";
						$sql2 .= "index (symbol),";
						$sql2 .= "PRIMARY KEY (id),";
						$sql2 .= "reg_date TIMESTAMP)";
						$sql2up	=	$HRS->prepare($sql2);
						$sql2up->execute();
						unset($sql2up);
				
						$trndat		 =	"INSERT INTO  $samtab ( SYMBOL, TDATE, THOUR, HLF, 4HLF, TIME, TMIN, OPEN, HIGH, LOW, CLOSE ) ";
						$trndat		.=	" SELECT SYMBOL, TDATE, THOUR, HLF, 4HLF, TIME, TMIN, OPEN, HIGH, LOW, CLOSE FROM  $ttablename";
						$trndat		.=	" ORDER BY TDATE DESC, THOUR DESC, TMIN DESC";
						$trnup		 =	$HRS->prepare($trndat);
						$trnup->execute();
						unset($trnup);
						
						$drptab		=	"DROP TABLE $ttablename";
						$drup		=	$HRS->prepare($drptab);
						$drup->execute();
						unset($drup);
						
						echo "<br/> ttable table for " . $stock . " has been dropped. <br/>";
						
						$rnmtab		=	"RENAME TABLE $samtab TO $ttablename";
						$rnmup		=	$HRS->prepare($rnmtab);
						$rnmup->execute();
						unset($rnmup);
						
						echo "<br/>  Table for " . $stock . " has been renamed. <br/>";
						$HRS->commit();
					}
					catch(PDOException $e)
					{
							print "<br/> ERROR LOADING TABLES: " .$e->getMessage(); 
					}
						
						$ext = False;
					
						$tablecheck	=	"show tables like :tablename";
						$tcres		=	$HRS->prepare($tablecheck);
						$tcres->execute([':tablename'=>$tablename]);
						$count = $tcres->rowCount();
						
						
					
				
					
				
					
					
					
					if($tcres ==true && $count > 0)
					{
						echo "<br/>table check for $tablename was True. <br/>";
						$HRS->beginTransaction();
						
						try
						{
							$sql  = "CREATE TABLE  $modtable  ( ";
							$sql .= "id INT(255) NOT NULL AUTO_INCREMENT,"; 
							$sql .= "SYMBOL VARCHAR(30) NULL,";
							$sql .= "TDATE DATE ,";
							$sql .= "THOUR VARCHAR(50),";
							$sql .= "4HLF VARCHAR(50),";
							$sql .= "OPEN DECIMAL(50,2)  NULL,";
							$sql .= "HIGH DECIMAL(50,2)  NULL,";
							$sql .= "LOW DECIMAL(50,2)  NULL,";
							$sql .= "CLOSE DECIMAL(50,2)  NULL,";
							$sql .= "TYPICAL_PRICE DECIMAL(50,2)   NULL,";
							$sql .= "DEGREES VARCHAR(50) NULL,";
							$sql .= "index (symbol),";
							$sql .= "PRIMARY KEY (id),";
							$sql .= "reg_date TIMESTAMP)";
						
							$tabq	=	$HRS->prepare($sql);
							$tabq->execute();
							unset($tabq);
							echo "<br/> MOD table for " . $stock . " has been created. <br/>";
							
							$insrt	 =	"INSERT INTO  $modtable ( SYMBOL, TDATE, THOUR, OPEN ) SELECT t1.SYMBOL, t1.TDATE, ";
							$insrt	.=	" t1.thour, t1.open FROM  $ttablename AS t1 LEFT JOIN  $ttablename as t2 ON ";
							$insrt	.=	"( t1.TDATE = t2.TDATE AND t1.HLF = t2.HLF AND t1.ID < t2.ID) ";
							$insrt	.=	"WHERE t2.ID IS NULL ";
							
							$insq	=	$HRS->prepare($insrt);
							$insq->execute();
							unset($insrt);
							
							$findID		=	"SELECT ID, TDATE, HLF, TIME FROM  $ttablename GROUP BY TDATE DESC, THOUR DESC";
							$fnd		=	$HRS->prepare($findID);
							$fnd->execute();
							
							$dateArray		=	array();
							$hlfArray		=	array();
							$idArray		=	array();
							$timeArray		=	array();
					
						
							$ct = 0;
							
							while($row = $fnd->fetch(PDO::FETCH_BOTH))
							{
								$dateArray[$ct] = $row['TDATE'];
								$hlfArray[$ct] 	= $row['HLF'];
								$idArray[$ct]	= $row['ID'];
								$timeArray[$ct]	= $row['TIME'];
								
								$ct++;
								
							}
								
							
							$fnd->closeCursor();
							
							$maxsz	=	sizeof($idArray);
						
							for($i = 0; $i < $maxsz; $i++)
							{
								$id		=	$idArray[$i];
								$tdate	=	$dateArray[$i];
								$ttime	=	$timeArray[$i];
							
								if($ttime == 'ET')
								{
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 1),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 1), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 1 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 09";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 2),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 2), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 2 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 10";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 3),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 3), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 3 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 11";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 4),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 4), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 4 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 12";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 5),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 5), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 5 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 13";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 6),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 6), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 6 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 14";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 7),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 7), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 7 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 15";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
								}
								elseif($ttime == 'LT')
								{
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 1),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 1), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 1 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 10";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 2),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 2), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 2 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 11";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 3),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 3), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 3 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 12";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 4),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 4), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 4 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 13";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 5),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 5), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 5 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 14";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 6),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 6), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 6 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 15";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 7),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 7), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 7 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 16";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
								}
								
								
							}
							$sqluno		=	"UPDATE  $modtable SET TYPICAL_PRICE = (SELECT (HIGH+LOW+CLOSE)/3)";
							
							$sthi2		=	$HRS->prepare($sqluno);
							$sthi2->execute();
							$sthi2->closeCursor();
	
							
							$oldtable	  =	'x'. $this->stock . 'x';
							
							
							
								$altquery 	=	" ALTER TABLE $oldtable MODIFY id INT(5) NOT NULL ";
								$aquery		=	$HRS->prepare($altquery);
								$aquery->execute();
								$aquery = NULL;
								
								$altquery2 		=	" ALTER TABLE $oldtable DROP COLUMN id ";
								$aquery2		=	$HRS->prepare($altquery2);
								$aquery2->execute();
								$aquery2 = NULL;
								
								$altquery3 	=	" ALTER TABLE $modtable MODIFY id INT(5) NOT NULL ";
								$aquery3	=	$HRS->prepare($altquery3);
								$aquery3->execute();
								$aquery3 = NULL;
								
								$altquery4 		=	" ALTER TABLE $modtable DROP COLUMN id ";
								$aquery4		=	$HRS->prepare($altquery4);
								$aquery4->execute();
								$aquery4 = NULL;
								
									$thirdtable		=	'ttt' .$stock;
									$sql  = "CREATE TABLE HR . $thirdtable  ( ";
									$sql .= "id INT(255) NOT NULL AUTO_INCREMENT,"; 
									$sql .= "SYMBOL VARCHAR(30) NULL,";
									$sql .= "TDATE DATE ,";
									$sql .= "THOUR VARCHAR(50),";
									$sql .= "OPEN DECIMAL(50,2)  NULL,";
									$sql .= "HIGH DECIMAL(50,2)  NULL,";
									$sql .= "LOW DECIMAL(50,2)  NULL,";
									$sql .= "CLOSE DECIMAL(50,2)  NULL,";
									$sql .= "TYPICAL_PRICE DECIMAL(50,2)   NULL,";
									$sql .= "DEGREES VARCHAR(50) NULL,";
									$sql .= "index (symbol),";
									$sql .= "PRIMARY KEY (id),";
									$sql .= "reg_date TIMESTAMP)";
									
									$third	=	$HRS->prepare($sql);
									$third->execute();
									$third->closeCursor();
								
								$sql2	 =	" INSERT INTO $thirdtable (SYMBOL, TDATE, ";
								$sql2	.=	" THOUR, OPEN, HIGH, LOW, CLOSE, TYPICAL_PRICE)";
								$sql2	.=	" SELECT SYMBOL, TDATE, THOUR, OPEN, HIGH, LOW, ";
								$sql2	.=	" CLOSE, TYPICAL_PRICE FROM $modtable";
								$lastquery	 =	$HRS->prepare($sql2);
								$lastquery->execute();
								$size 		 = $lastquery->rowCount();
								
								if($lastquery == true)
								{
									$lastque	 =	"INSERT INTO HR. $thirdtable ( SYMBOL, TDATE, THOUR, OPEN, HIGH, LOW, CLOSE, ";
									$lastque	.=	" TYPICAL_PRICE, DEGREES ) SELECT SYMBOL, TDATE, THOUR, OPEN, HIGH, LOW, ";
									$lastque	.=	" CLOSE, TYPICAL_PRICE, DEGREES FROM HR. $oldtable WHERE $oldtable. TDATE < (SELECT ";
									$lastque	.=	" TDATE FROM HR. $modtable ORDER BY TDATE ASC, THOUR ASC LIMIT 1) ORDER BY TDATE DESC,";
									$lastque	.=	" THOUR DESC";
									$lastquery	 =	$HRS->prepare($lastque);
									$lastquery->execute();
									$size 		 = $lastquery->rowCount();
									
									$drop 		= " DROP TABLE  $oldtable ";
									$mquery		=	$HRS->prepare($drop);
									$mquery->execute();
									$mquery = NULL;
									
									$drop 		= " DROP TABLE  $modtable ";
									$mquery		=	$HRS->prepare($drop);
									$mquery->execute();
									$mquery = NULL;
									
									$rename			=	" RENAME TABLE $thirdtable TO $oldtable";
									$rmquery2		=	$HRS->prepare($rename);
									$rmquery2->execute();
									$rmquery = NULL;
								}

								
								
	#################################################################################################
								
								$ccoquery		=	" SELECT ID, CLOSE FROM  $tablename";
								$cco1			=	$HRS->prepare($ccoquery);
								$cco1->execute();
						
								$close	=	array();
								$n		=	0;
						
								while($row = $cco1->fetch(PDO::FETCH_BOTH))
								{
									$close[$n]['ID']	=	$row['ID'];
									$close[$n]['CLOSE']	=	$row['CLOSE'];
									++$n;
								}
								$cco1->closeCursor();
								$maxco	=	sizeof($close);
						
								for($i =0; $i < $maxco; $i++)
								{
							
									$peak	=	"PEAK";
									$valley	=	"VALLEY";
									
									
									$last	= 	$maxco - 1;
							
									if( $i == 0)
									{
										$n		=	$i + 1;
										$tdcci	=	$close[$i]['CLOSE'];
										$ycci	=	$close[$n]['CLOSE'];
										$tid	=	$close[$i]['ID'];
					
										$tid		=	" ' " . $tid . " ' ";
					
					
					
										if($tdcci > $ycci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :peak WHERE ID = $tid";
											$degup			=	$HRS->prepare($utd);
											$degup->execute([':peak'=>$peak]);
											unset($degup);
										}
										else if( $tdcci < $ycci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :valley WHERE ID = $tid";
											$degup			=	$HRS->prepare($utd);
											$degup->execute([':valley'=>$valley]);
											unset($degup);
										}
					
									}
									else if( $i > 0 && $i < $last)
									{
										$n		=	$i + 1;
										$m		=	$i - 1;
										$tdcci	=	$close[$i]['CLOSE'];
										$ycci	=	$close[$n]['CLOSE'];
										$tmcci	=	$close[$m]['CLOSE'];
										$tid		=	$close[$i]['ID'];
										$tid		=	" ' " . $tid . " ' ";
					
										if( $tdcci > $ycci && $tdcci > $tmcci)
										{
											$utd 			= " UPDATE  $tablename set DEGREES = :peak WHERE ID = $tid";
											$degup			=	$HRS->prepare($utd);
											$degup->execute([':peak'=>$peak]);
											unset($degup);
										}
										else if ( $tdcci < $ycci && $tdcci < $tmcci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :valley WHERE ID = $tid";
											$degup			=	$HRS->prepare($utd);
											$degup->execute([':valley'=>$valley]);
											unset($degup);
										}
					
									}
									else if($i == $last)
									{
					
										$m		=	$i - 1;
										$tdcci	=	$close[$i]['CLOSE'];
										$tmcci	=	$close[$m]['CLOSE'];
										$tid	=	$close[$i]['ID'];
										$tid	=	" ' " . $tid . " ' ";
										if($tdcci > $tmcci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :peak  WHERE ID = $tid";
											$degup			=	$HRS->prepare($utd);
											$degup->execute([':peak'=>$peak]);
											unset($degup);
										}
										else if($tdcci < $tmcci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :valley  WHERE ID = $tid";
											$degup			=	$HRS->prepare($utd);
											$degup->execute([':valley'=>$valley]);
											unset($degup);
										}
					
									}
				
								}	

								$arraysql	= "SELECT OPEN, LOW, HIGH, CLOSE, TYPICAL_PRICE FROM $tablename ";
								$values		= $HRS->prepare($arraysql);
								$values->execute();
			
								while($row = $values->fetch(PDO::FETCH_BOTH))
								{
				
				
									$this->hrs_highs[]		= $row['HIGH'];
									$this->hrs_lows[]		= $row['LOW'];
									$this->hrs_opens[]		= $row['OPEN'];
									$this->hrs_closes[]		= $row['CLOSE'];
									$this->hrs_typrice[]	= $row['TYPICAL_PRICE'];	
			
								}
								
								$HRS->commit();
						}
						catch(PDOException $e)
						{
							print "<br/> ERROR LOADING TABLES: " .$e->getMessage();
						}
						
						try
						{
							$sql  = "CREATE TABLE  $modtable  ( ";
							$sql .= "id INT(255) NOT NULL AUTO_INCREMENT,"; 
							$sql .= "SYMBOL VARCHAR(30) NULL,";
							$sql .= "TDATE DATE ,";
							$sql .= "THOUR VARCHAR(50),";
							$sql .= "OPEN DECIMAL(50,2)  NULL,";
							$sql .= "HIGH DECIMAL(50,2)  NULL,";
							$sql .= "LOW DECIMAL(50,2)  NULL,";
							$sql .= "CLOSE DECIMAL(50,2)  NULL,";
							$sql .= "TYPICAL_PRICE DECIMAL(50,2)   NULL,";
							$sql .= "DEGREES VARCHAR(50) NULL,";
							$sql .= "index (symbol),";
							$sql .= "PRIMARY KEY (id),";
							$sql .= "reg_date TIMESTAMP)";
						
							$tabq	=	$HR->prepare($sql);
							$tabq->execute();
							unset($tabq);
							echo "<br/> MOD table for " . $stock . " has been created. <br/>";
							
							$insrt	 =	"INSERT INTO  $modtable ( SYMBOL, TDATE, THOUR, OPEN ) SELECT t1.SYMBOL, t1.TDATE, ";
							$insrt	.=	" t1.thour, t1.open FROM  HR.$ttablename AS t1 LEFT JOIN  HR. $ttablename as t2 ON ";
							$insrt	.=	"( t1.TDATE = t2.TDATE AND t1.4HLF = t2.4HLF AND t1.ID < t2.ID) ";
							$insrt	.=	"WHERE t2.ID IS NULL ";
							
							$insq	=	$HR->prepare($insrt);
							$insq->execute();
							unset($insrt);
							
							$findID		=	"SELECT ID, TDATE FROM  $modtable";
							$fnd		=	$HR->prepare($findID);
							$fnd->execute();
							$dateArray		=	array();
							$hlfArray		=	array();
							$idArray		=	array();
						
							$ct = 0;
							
							while($row = $fnd->fetch(PDO::FETCH_BOTH))
							{
								$dateArray[$ct] = $row['TDATE'];
								$idArray[$ct]	= $row['ID'];
								$ct++;
								
							}
								
							
							$fnd->closeCursor();
							
							$maxsz	=	sizeof($idArray);
						
							for($i = 0; $i < $maxsz; $i++)
							{
								$id		=	$idArray[$i];
								$tdate	=	$dateArray[$i];
							
								$sethigh	 =	"UPDATE 4HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
								$sethigh	.=	" WHERE tdate = ? AND 4HLF = 1),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
								$sethigh	.=	" WHERE tdate = ? AND 4HLF = 1), CLOSE = (SELECT CLOSE FROM HR ."; 
								$sethigh	.=	" $ttablename WHERE tdate = ? AND 4HLF = 1 AND CLOSE IS NOT NULL ";
								$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
								$sethigh	.=	" AND thour < 12";
							
								$sthi	=	$HR->prepare($sethigh);
								$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
								$sthi->closeCursor();
								
								$sethigh	 =	"UPDATE 4HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
								$sethigh	.=	" WHERE tdate = ? AND 4HLF = 2),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
								$sethigh	.=	" WHERE tdate = ? AND 4HLF = 2), CLOSE = (SELECT CLOSE FROM HR ."; 
								$sethigh	.=	" $ttablename WHERE tdate = ? AND 4HLF = '2' AND CLOSE IS NOT NULL";
								$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1 ) WHERE tdate = ?";
								$sethigh	.=	" AND thour BETWEEN 12 AND 14";
								
								$sthi2	=	$HR->prepare($sethigh);
								$sthi2->execute([$tdate,$tdate,$tdate,$tdate]);
								$sthi2->closeCursor();
								
								
							}
							$sqluno		=	"UPDATE  4HR. $modtable SET TYPICAL_PRICE = (SELECT (HIGH+LOW+CLOSE)/3)";
							
							$sthi2		=	$HR->prepare($sqluno);
							$sthi2->execute();
							$sthi2->closeCursor();
							
							
							$oldtable	  =	'x'. $this->stock . 'x';
							
							
							
								$altquery 	=	" ALTER TABLE 4HR. $oldtable MODIFY id INT(5) NOT NULL ";
								$aquery		=	$HR->prepare($altquery);
								$aquery->execute();
								$aquery = NULL;
								
								$altquery2 		=	" ALTER TABLE 4HR.$oldtable DROP COLUMN id ";
								$aquery2		=	$HR->prepare($altquery2);
								$aquery2->execute();
								$aquery2 = NULL;
								
								$altquery3 	=	" ALTER TABLE 4HR.$modtable MODIFY id INT(5) NOT NULL ";
								$aquery3	=	$HR->prepare($altquery3);
								$aquery3->execute();
								$aquery3 = NULL;
								
								$altquery4 		=	" ALTER TABLE 4HR. $modtable DROP COLUMN id ";
								$aquery4		=	$HR->prepare($altquery4);
								$aquery4->execute();
								$aquery4 = NULL;
								
									$thirdtable		=	'ttt' .$stock;
									$sql  = "CREATE TABLE 4HR . $thirdtable  ( ";
									$sql .= "id INT(255) NOT NULL AUTO_INCREMENT,"; 
									$sql .= "SYMBOL VARCHAR(30) NULL,";
									$sql .= "TDATE DATE ,";
									$sql .= "THOUR VARCHAR(50),";
									$sql .= "OPEN DECIMAL(50,2)  NULL,";
									$sql .= "HIGH DECIMAL(50,2)  NULL,";
									$sql .= "LOW DECIMAL(50,2)  NULL,";
									$sql .= "CLOSE DECIMAL(50,2)  NULL,";
									$sql .= "TYPICAL_PRICE DECIMAL(50,2)   NULL,";
									$sql .= "DEGREES VARCHAR(50) NULL,";
									$sql .= "index (symbol),";
									$sql .= "PRIMARY KEY (id),";
									$sql .= "reg_date TIMESTAMP)";
									
									$third	=	$HR->prepare($sql);
									$third->execute();
									$third->closeCursor();
								
								$sql2	 =	" INSERT INTO 4HR. $thirdtable (SYMBOL, TDATE, ";
								$sql2	.=	" THOUR, OPEN, HIGH, LOW, CLOSE, TYPICAL_PRICE)";
								$sql2	.=	" SELECT SYMBOL, TDATE, THOUR, OPEN, HIGH, LOW, ";
								$sql2	.=	" CLOSE, TYPICAL_PRICE FROM 4HR. $modtable";
								$lastquery	 =	$HR->prepare($sql2);
								$lastquery->execute();
								$size 		 = $lastquery->rowCount();
								
								if($lastquery == true)
								{
									$lastque	 =	"INSERT INTO 4HR. $thirdtable ( SYMBOL, TDATE, THOUR, OPEN, HIGH, LOW, CLOSE, ";
									$lastque	.=	" TYPICAL_PRICE, DEGREES ) SELECT SYMBOL, TDATE, THOUR, OPEN, HIGH, LOW, ";
									$lastque	.=	" CLOSE, TYPICAL_PRICE, DEGREES FROM 4HR. $oldtable WHERE $oldtable. TDATE < (SELECT ";
									$lastque	.=	" TDATE FROM 4HR. $modtable ORDER BY TDATE ASC, THOUR ASC LIMIT 1) ORDER BY TDATE DESC,";
									$lastque	.=	" THOUR DESC";
									$lastquery	 =	$HR->prepare($lastque);
									$lastquery->execute();
									$size 		 = $lastquery->rowCount();
									
									$drop 		= " DROP TABLE  4HR . $oldtable ";
									$mquery		=	$HR->prepare($drop);
									$mquery->execute();
									$mquery = NULL;
									
									$drop 		= " DROP TABLE  4HR .$modtable ";
									$mquery		=	$HR->prepare($drop);
									$mquery->execute();
									$mquery = NULL;
									
									$rename			=	" RENAME TABLE $thirdtable TO $oldtable";
									$rmquery2		=	$HR->prepare($rename);
									$rmquery2->execute();
									$rmquery = NULL;
								}

								
								
	#################################################################################################
								
								$ccoquery		=	" SELECT ID, CLOSE FROM  4HR. $tablename";
								$cco1			=	$HR->prepare($ccoquery);
								$cco1->execute();
						
								$close	=	array();
								$n		=	0;
						
								while($row = $cco1->fetch(PDO::FETCH_BOTH))
								{
									$close[$n]['ID']	=	$row['ID'];
									$close[$n]['CLOSE']	=	$row['CLOSE'];
									++$n;
								}
								$cco1->closeCursor();
								$maxco	=	sizeof($close);
						
								for($i =0; $i < $maxco; $i++)
								{
							
									$peak	=	"PEAK";
									$valley	=	"VALLEY";
									
									
									$last	= 	$maxco - 1;
							
									if( $i == 0)
									{
										$n		=	$i + 1;
										$tdcci	=	$close[$i]['CLOSE'];
										$ycci	=	$close[$n]['CLOSE'];
										$tid	=	$close[$i]['ID'];
					
										$tid		=	" ' " . $tid . " ' ";
					
					
					
										if($tdcci > $ycci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :peak WHERE ID = $tid";
											$degup			=	$HR->prepare($utd);
											$degup->execute([':peak'=>$peak]);
											unset($degup);
										}
										else if( $tdcci < $ycci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :valley WHERE ID = $tid";
											$degup			=	$HR->prepare($utd);
											$degup->execute([':valley'=>$valley]);
											unset($degup);
										}
					
									}
									else if( $i > 0 && $i < $last)
									{
										$n		=	$i + 1;
										$m		=	$i - 1;
										$tdcci	=	$close[$i]['CLOSE'];
										$ycci	=	$close[$n]['CLOSE'];
										$tmcci	=	$close[$m]['CLOSE'];
										$tid		=	$close[$i]['ID'];
										$tid		=	" ' " . $tid . " ' ";
					
										if( $tdcci > $ycci && $tdcci > $tmcci)
										{
											$utd 			= " UPDATE  $tablename set DEGREES = :peak WHERE ID = $tid";
											$degup			=	$HR->prepare($utd);
											$degup->execute([':peak'=>$peak]);
											unset($degup);
										}
										else if ( $tdcci < $ycci && $tdcci < $tmcci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :valley WHERE ID = $tid";
											$degup			=	$HR->prepare($utd);
											$degup->execute([':valley'=>$valley]);
											unset($degup);
										}
					
									}
									else if($i == $last)
									{
					
										$m		=	$i - 1;
										$tdcci	=	$close[$i]['CLOSE'];
										$tmcci	=	$close[$m]['CLOSE'];
										$tid	=	$close[$i]['ID'];
										$tid	=	" ' " . $tid . " ' ";
										if($tdcci > $tmcci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :peak  WHERE ID = $tid";
											$degup			=	$HR->prepare($utd);
											$degup->execute([':peak'=>$peak]);
											unset($degup);
										}
										else if($tdcci < $tmcci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :valley  WHERE ID = $tid";
											$degup			=	$HR->prepare($utd);
											$degup->execute([':valley'=>$valley]);
											unset($degup);
										}
					
									}
				
								}	

								$arraysql	= "SELECT OPEN, LOW, HIGH, CLOSE, TYPICAL_PRICE FROM $tablename ";
								$values		= $HR->prepare($arraysql);
								$values->execute();
			
								while($row = $values->fetch(PDO::FETCH_BOTH))
								{
				
				
									$this->four_highs[]		= $row['HIGH'];
									$this->four_lows[]		= $row['LOW'];
									$this->four_opens[]		= $row['OPEN'];
									$this->four_closes[]	= $row['CLOSE'];
									$this->four_typrice[]	= $row['TYPICAL_PRICE'];	
			
								}
								
								$HR->commit();
						}
						catch(PDOException $e)
						{
							print "<br/> ERROR LOADING TABLES: " . $e->getMessage();
						}
						
							
							
					}
			
					else
					{
						echo "<br/>table check for $tablename was not True. <br/>";
						$HRS->beginTransaction();

						try
						{
							$sql  = "CREATE TABLE $tablename ( ";
							$sql .= "id INT(255) NOT NULL AUTO_INCREMENT,"; 
							$sql .= "SYMBOL VARCHAR(30) NULL,";
							$sql .= "TDATE DATE,";
							$sql .= "THOUR VARCHAR(50),";
							$sql .= "OPEN DECIMAL(50,2)  NULL,";
							$sql .= "HIGH DECIMAL(50,2)  NULL,";
							$sql .= "LOW DECIMAL(50,2)  NULL,";
							$sql .= "CLOSE DECIMAL(50,2)  NULL,";
							$sql .= "TYPICAL_PRICE DECIMAL(50,2)   NULL,";
							$sql .= "DEGREES VARCHAR(50) NULL,";
							$sql .= "index (symbol),";
							$sql .= "PRIMARY KEY (id),";
							$sql .= "reg_date TIMESTAMP)";
							
							$que1		=	$HRS->prepare($sql);
							$que1->execute();
							unset($que1);
							
							$setw	 =	"INSERT INTO  $tablename ( SYMBOL, TDATE, THOUR, OPEN ) SELECT t1.SYMBOL,";
							$setw	.=	" t1.TDATE, t1.thour, t1.open FROM  $ttablename AS t1 LEFT JOIN $ttablename ";
							$setw	.=	"as t2 ON ( t1.TDATE = t2.TDATE AND t1.HLF = t2.HLF AND t1.ID < t2.ID) ";
							$setw	.=	"WHERE t2.ID IS NULL ";
					
							$que2		=	$HRS->prepare($setw);
							$que2->execute();
							unset($que2);
							
							$findID		=	"SELECT ID, TDATE, HLF, TIME FROM $ttablename GROUP BY TDATE DESC, THOUR DESC";
					
							$que3		=	$HRS->prepare($findID);
							$que3->execute();
							
							$dateArray		=	array();
							$hlfArray		=	array();
							$idArray		=	array();
							$timeArray		=	array();
						
							while($row = $que3->fetch(PDO::FETCH_BOTH))
							{
								$idArray[]		=	$row['ID'];
								$dateArray[]	=	$row['TDATE'];
								$timeArray[]	=	$row['TIME'];
								$hlfArray[]		=	$row['HLF'];
						
							}
							$que3->closeCursor();
							$maxsiz	=	sizeof($dateArray);
						
							for($i =0 ; $i < $maxsiz; $i++)
							{
								$id		=	$idArray[$i];
								$tdate	=	$dateArray[$i];
								$ttime	=	$timeArray[$i];
								
								if($ttime == 'ET')
								{
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 1),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 1), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 1 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 09";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 2),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 2), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 2 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 10";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 3),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 3), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 3 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 11";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 4),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 4), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 4 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 12";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 5),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 5), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 5 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 13";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 6),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 6), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 6 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 14";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 7),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 7), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 7 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 15";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
								}
								elseif($ttime == 'LT')
								{
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 1),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 1), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 1 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 10";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 2),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 2), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 2 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 11";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 3),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 3), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 3 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 12";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 4),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 4), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 4 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 13";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 5),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 5), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 5 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 14";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 6),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 6), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 6 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 15";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 7),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 7), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 7 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 16";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
								}
								
							
								
							
							}
							
							$sqluno		=	"UPDATE  HR. $tablename SET TYPICAL_PRICE = (SELECT (HIGH+LOW+CLOSE)/3)";
							$que5		=	$HRS->prepare($sqluno);
							$que5->execute();
							unset($que5);
						
							
							
							$arraysql	= "SELECT OPEN, LOW, HIGH, CLOSE, TYPICAL_PRICE FROM HR. $tablename ";
							$values		= $HRS->prepare($arraysql);
							$values->execute();
			
							while($row = $values->fetch(PDO::FETCH_BOTH))
							{
				
				
								$this->hrs_highs[]		= $row['HIGH'];
								$this->hrs_lows[]		= $row['LOW'];
								$this->hrs_opens[]		= $row['OPEN'];
								$this->hrs_closes[]		= $row['CLOSE'];
								$this->hrs_typrice[]	= $row['TYPICAL_PRICE'];	
			
							}
						
							$HRS->commit();
						}
						catch( PDOException $e)
						{
							print " <br/> ERROR LOADING TABLES: " . $e->getMessage();
						}
							
						
					}
						
						
						$ccoquery		=	" SELECT ID, CLOSE FROM  HR. $tablename";
						$cco1			=	$HRS->prepare($ccoquery);
						$cco1->execute();
						
						$close	=	array();
						$n		=	0;
						
						while($row = $cco1->fetch(PDO::FETCH_BOTH))
						{
							$close[$n]['ID']	=	$row['ID'];
							$close[$n]['CLOSE']	=	$row['CLOSE'];
							++$n;
						}
						$cco1->closeCursor();
						$maxco	=	sizeof($close);
						
						for($i =0; $i < $maxco; $i++)
						{
							
							$peak	=	"PEAK";
							$valley	=	"VALLEY";
							$last	= 	$maxco - 1;
							
							if( $i == 0)
							{
								$n		=	$i + 1;
								$tdcci	=	$close[$i]['CLOSE'];
								$ycci	=	$close[$n]['CLOSE'];
								$tid	=	$close[$i]['ID'];
					
								$tid		=	" ' " . $tid . " ' ";
					
					
					
								if($tdcci > $ycci)
								{
									$utd = " UPDATE  $tablename set DEGREES = :peak WHERE ID = $tid";
									$degup			=	$HRS->prepare($utd);
									$degup->execute([':peak'=>$peak]);
									unset($degup);
								}
								else if( $tdcci < $ycci)
								{
									$utd = " UPDATE  $tablename set DEGREES = :valley WHERE ID = $tid";
									$degup			=	$HRS->prepare($utd);
									$degup->execute([':valley'=>$valley]);
									unset($degup);
								}
					
							}
							else if( $i > 0 && $i < $last)
							{
								$n		=	$i + 1;
								$m		=	$i - 1;
								$tdcci	=	$close[$i]['CLOSE'];
								$ycci	=	$close[$n]['CLOSE'];
								$tmcci	=	$close[$m]['CLOSE'];
								$tid		=	$close[$i]['ID'];
								$tid		=	" ' " . $tid . " ' ";
					
								if( $tdcci > $ycci && $tdcci > $tmcci)
								{
									$utd 		= " UPDATE  $tablename set DEGREES = :peak WHERE ID = $tid";
									$degup		=	$HRS->prepare($utd);
									$degup->execute([':peak'=>$peak]);
									unset($degup);
								}
								if ( $tdcci < $ycci && $tdcci < $tmcci)
								{
									$utd = " UPDATE  $tablename set DEGREES = :valley WHERE ID = $tid";
									$degup			=	$HRS->prepare($utd);
									$degup->execute([':valley'=>$valley]);
									unset($degup);
								}
					
							}
							else if($i == $last)
							{
					
								$m		=	$i - 1;
								$tdcci	=	$close[$i]['CLOSE'];
								$tmcci	=	$close[$m]['CLOSE'];
								$tid	=	$close[$i]['ID'];
								$tid	=	" ' " . $tid . " ' ";
								if($tdcci > $tmcci)
								{
									$utd = " UPDATE  $tablename set DEGREES = :peak  WHERE ID = $tid";
									$degup			=	$HRS->prepare($utd);
									$degup->execute([':peak'=>$peak]);
									unset($degup);
								}
								else if($tdcci < $tmcci)
								{
									$utd = " UPDATE  $tablename set DEGREES = :valley  WHERE ID = $tid";
									$degup			=	$HRS->prepare($utd);
									$degup->execute([':valley'=>$valley]);
									unset($degup);
								}
					
							}
				
						}	
					
					
						
						$tcres->closeCursor();
						$clarity		=	"DROP TABLE  $ttablename";
						$que7			=	$HRS->prepare($clarity);
						$que7->execute();
						unset($que7);
						
					
				}
			
		}
		

		public function build_google_TblHRS()
		{
			$HRS = $this->HRS;
			$HR	 = $this->HR;
				##CHANGE ALGORITHM FOR HR TABLE.
				
				if($this->id)
				{
					$this->stock		=	$this->id;
					
					$stock				=	$this->stock;
					$tablename			=	'x' . $stock . 'x';
					
					$oldtable			=	'x'. $stock .'x';
					$samtab				=	'samx'. $stock;
					$modtable			=	'modx'.$stock;
					$ttablename			=	'x' . $stock . 'tmp';
					$filename			=	$stock . '.csv';
					$compath			=	'C:/xampp/htdocs/jst/Hist/GOOGLE/HOURLY/' . $filename;
			
					$HRS->beginTransaction();
					try
					{
						
					
				
						$sql  = "CREATE TABLE  $ttablename  ( ";
						$sql .= "id INT(255) NOT NULL AUTO_INCREMENT,";
						$sql .= "SYMBOL VARCHAR(30) NULL,";
						$sql .= "UNIX VARCHAR(30) NULL,";
						$sql .= "TDATE DATE ,";
						$sql .= "THOUR VARCHAR(50),";
						$sql .= "HLF VARCHAR(50),";
						$sql .= "4HLF VARCHAR(50),";
						$sql .= "TIME VARCHAR(5), ";
						$sql .= "TMIN VARCHAR(50),";
						$sql .= "OPEN DECIMAL(50,2)  NULL,";
						$sql .= "HIGH DECIMAL(50,2)  NULL,";
						$sql .= "LOW DECIMAL(50,2)  NULL,";
						$sql .= "CLOSE DECIMAL(50,2)  NULL,";
						$sql .= "index (symbol),";
						$sql .= "PRIMARY KEY (id),";
						$sql .= "reg_date TIMESTAMP)";
			
			
				
						$rslt	=	$HRS->prepare($sql);
						$rslt->execute();
						unset($rslt);

						$popTable  = "LOAD DATA INFILE 'C:/xampp/htdocs/jst/Hist/GOOGLE/HOURLY/$filename' INTO TABLE  $ttablename FIELDS ";
						$popTable .= "TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 7 LINES";
						$popTable .= " ( UNIX, CLOSE, HIGH, LOW, OPEN ) ";

						$rslt2		=	$HRS->prepare($popTable);
						$rslt2->execute();
						
						$selection	=	"SELECT ID FROM $ttablename WHERE UNIX LIKE '%a%'";
						$rslt		=	$HRS->prepare($selection);
						$rslt->execute();
						
						$resultsa	=	$rslt->fetchAll();
						$max		=	sizeof($resultsa);
						
						$selection2	=	"SELECT MAX(ID) FROM $ttablename";
						$rsltid		=	$HRS->prepare($selection2);
						$rsltid->execute();
						
						$lastid	=	$rsltid->fetchColumn();
						
						$update1	=	"UPDATE $ttablename SET UNIX = SUBSTRING(UNIX,2) WHERE UNIX LIKE '%a%'";
						$rslt3		=	$HRS->prepare($update1);
						$rslt3->execute();
						
						for($i=0;$i < $max; ++$i)
						{
							$last	=	$max - 1;
							
							
							if($i < $last)
							{
								$x		=	$i + 1;
								$num	=	$resultsa[$i]['ID'];
								$nnum	=	$resultsa[$x]['ID'];
								
								$num	=	"'".	$num	."'";
								$nnum	=	"'".	$nnum	."'";
								
								$update2	=	"UPDATE $ttablename SET UNIX = (UNIX * 300) WHERE ID > $num AND ID < $nnum";
								$rslt4		=	$HRS->prepare($update2);
								$rslt4->execute();
								
								$update5	 = "SET @FACTOR = (SELECT UNIX FROM $ttablename WHERE ID = $num )";
								$rslt4		=	$HRS->prepare($update5);
								$rslt4->execute();
								
								$update6	=	"UPDATE $ttablename SET UNIX = (UNIX + @FACTOR) WHERE ID > $num AND ID < $nnum";
								$rslt4		=	$HRS->prepare($update6);
								$rslt4->execute();
								
							}
							elseif($i ==  $last)
							{
								$num	=	$resultsa[$i]['ID'];
								$nnum	=	$lastid;
								echo $nnum. "<hr />";
								$update2	=	"UPDATE $ttablename SET UNIX = (UNIX * 300) WHERE ID > $num AND ID <= $nnum";
								$rslt4		=	$HRS->prepare($update2);
								$rslt4->execute();
								
								$update5	 = "SET @FACTOR = (SELECT UNIX FROM $ttablename WHERE ID = $num )";
								$rslt4		=	$HRS->prepare($update5);
								$rslt4->execute();
								
								$update6	=	"UPDATE $ttablename SET UNIX = (UNIX + @FACTOR) WHERE ID > $num AND ID <= $nnum";
								$rslt4		=	$HRS->prepare($update6);
								$rslt4->execute();
							}
						}
						
					
						
						
						
						$update6	=	"UPDATE $ttablename SET UNIX = (UNIX - 300)";
						$rslt4		=	$HRS->prepare($update6);
						$rslt4->execute();
						
						$update3	 =	"UPDATE $ttablename SET TDATE = FROM_UNIXTIME(UNIX, '%Y-%m-%D'), THOUR = FROM_UNIXTIME(UNIX, '%H'),";
						$update3	.=	" TMIN = FROM_UNIXTIME(UNIX, '%i')";
						$rslt5		=	$HRS->prepare($update3);
						$rslt5->execute();
						
						
					
						if($rslt2 === true)
						{
							echo "2st query done. <br />";
						}
						unset($rslt2);
						
						$setsymbol	= "UPDATE $ttablename SET SYMBOL = ?";
						$symup		=	$HRS->prepare($setsymbol);
						$symup->execute([$stock]);
						unset($symup);
						
						$setsymbol	= "DELETE FROM  $ttablename WHERE TDATE < '2016-08-30'";
						$symup		=	$HRS->prepare($setsymbol);
						$symup->execute();
						unset($symup);
						
						$sett		 =	 " UPDATE HR. $ttablename AS T3, (SELECT T.TDATE FROM $ttablename AS T";
						$sett		.=	 " INNER JOIN ( SELECT TDATE FROM $ttablename WHERE TMIN = 00 AND THOUR = 10";
						$sett		.=	 " ) AS T1 ON T.TDATE = T1.TDATE) AS T2 SET T3.TIME = 'ET' WHERE T3.TDATE = T2.TDATE";
						$updhlf		=	$HRS->prepare($sett);
						$updhlf->execute();
						unset($updhlf);
						
						$sethlf2	= " UPDATE HR. $ttablename SET TIME = 'LT' WHERE TIME IS NULL ";
						$updhlf2	=	$HRS->prepare($sethlf2);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 1 WHERE TIME = 'ET' AND THOUR = 09";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 2 WHERE TIME = 'ET' AND THOUR = 10 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 3 WHERE TIME = 'ET' AND THOUR = 11 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 4 WHERE TIME = 'ET' AND THOUR = 12 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 5 WHERE TIME = 'ET' AND THOUR = 13 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 6 WHERE TIME = 'ET' AND THOUR = 14 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 7 WHERE TIME = 'ET' AND THOUR = 15 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 1 WHERE TIME = 'LT' AND THOUR = 10 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 2 WHERE TIME = 'LT' AND THOUR = 11 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 3 WHERE TIME = 'LT' AND THOUR = 12 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 4 WHERE TIME = 'LT' AND THOUR = 13 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 5 WHERE TIME = 'LT' AND THOUR = 14 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 6 WHERE TIME = 'LT' AND THOUR = 15 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET HLF = 7 WHERE TIME = 'LT' AND THOUR = 16 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET 4HLF = 1 WHERE TIME = 'ET' AND THOUR BETWEEN 09 AND 11";
						$updhlf2	=	$HR->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET 4HLF = 1 WHERE TIME = 'LT' AND THOUR BETWEEN 10 AND 12";
						$updhlf2	=	$HR->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);

						$sethlf		= "UPDATE HR . $ttablename SET 4HLF = 2 WHERE TIME = 'ET' AND THOUR >= 12 ";
						$updhlf2	=	$HR->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "UPDATE HR . $ttablename SET 4HLF = 2 WHERE TIME = 'LT' AND THOUR >= 13 ";
						$updhlf2	=	$HR->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "DELETE FROM  HR. $ttablename WHERE TIME = 'ET' AND THOUR = 16 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						$sethlf		= "DELETE FROM  HR. $ttablename WHERE TIME = 'LT' AND THOUR = 17 ";
						$updhlf2	=	$HRS->prepare($sethlf);
						$updhlf2->execute();
						unset($updhlf2);
						
						
						$altq		= "ALTER TABLE  $ttablename ORDER BY TDATE DESC, THOUR DESC, TMIN DESC ";
						$upalt		=	$HRS->prepare($altq);
						$upalt->execute();
						unset($upalt);
				
						$sql2  = "CREATE TABLE $samtab ( ";
						$sql2 .= "id INT(255) NOT NULL AUTO_INCREMENT,"; 
						$sql2 .= "SYMBOL VARCHAR(30) NULL,";
						$sql2 .= "TDATE DATE ,";
						$sql2 .= "THOUR VARCHAR(50),";
						$sql2 .= "HLF VARCHAR(50),";
						$sql2 .= "4HLF VARCHAR(50),";
						$sql2 .= "TIME VARCHAR(5), ";
						$sql2 .= "TMIN VARCHAR(50),";
						$sql2 .= "OPEN DECIMAL(50,2)  NULL,";
						$sql2 .= "HIGH DECIMAL(50,2)  NULL,";
						$sql2 .= "LOW DECIMAL(50,2)  NULL,";
						$sql2 .= "CLOSE DECIMAL(50,2)  NULL,";
						$sql2 .= "index (symbol),";
						$sql2 .= "PRIMARY KEY (id),";
						$sql2 .= "reg_date TIMESTAMP)";
						$sql2up	=	$HRS->prepare($sql2);
						$sql2up->execute();
						unset($sql2up);
				
						$trndat		 =	"INSERT INTO  $samtab ( SYMBOL, TDATE, THOUR, HLF, 4HLF, TIME, TMIN, OPEN, HIGH, LOW, CLOSE ) ";
						$trndat		.=	" SELECT SYMBOL, TDATE, THOUR, HLF, 4HLF, TIME, TMIN, OPEN, HIGH, LOW, CLOSE FROM  $ttablename";
						$trndat		.=	" ORDER BY TDATE DESC, THOUR DESC, TMIN DESC";
						$trnup		 =	$HRS->prepare($trndat);
						$trnup->execute();
						unset($trnup);
						
						$drptab		=	"DROP TABLE $ttablename";
						$drup		=	$HRS->prepare($drptab);
						$drup->execute();
						unset($drup);
						
						echo "<br/> ttable table for " . $stock . " has been dropped. <br/>";
						
						$rnmtab		=	"RENAME TABLE $samtab TO $ttablename";
						$rnmup		=	$HRS->prepare($rnmtab);
						$rnmup->execute();
						unset($rnmup);
						
						echo "<br/>  Table for " . $stock . " has been renamed. <br/>";
						$HRS->commit();
					
					}
					
					
					catch(PDOException $e)
					{
							print "<br/> ERROR LOADING TABLES: " .$e->getMessage(); 
					}
						
						$ext = False;
					
						$tablecheck	=	"show tables like :tablename";
						$tcres		=	$HRS->prepare($tablecheck);
						$tcres->execute([':tablename'=>$tablename]);
						$count = $tcres->rowCount();
						
						
					
				
					
				
					
					
					
					if($tcres ==true && $count > 0)
					{
						echo "<br/>table check for $tablename was True. <br/>";
						$HRS->beginTransaction();
						
						try
						{
							$sql  = "CREATE TABLE  $modtable  ( ";
							$sql .= "id INT(255) NOT NULL AUTO_INCREMENT,"; 
							$sql .= "SYMBOL VARCHAR(30) NULL,";
							$sql .= "TDATE DATE ,";
							$sql .= "THOUR VARCHAR(50),";
							$sql .= "4HLF VARCHAR(50),";
							$sql .= "OPEN DECIMAL(50,2)  NULL,";
							$sql .= "HIGH DECIMAL(50,2)  NULL,";
							$sql .= "LOW DECIMAL(50,2)  NULL,";
							$sql .= "CLOSE DECIMAL(50,2)  NULL,";
							$sql .= "TYPICAL_PRICE DECIMAL(50,2)   NULL,";
							$sql .= "DEGREES VARCHAR(50) NULL,";
							$sql .= "index (symbol),";
							$sql .= "PRIMARY KEY (id),";
							$sql .= "reg_date TIMESTAMP)";
						
							$tabq	=	$HRS->prepare($sql);
							$tabq->execute();
							unset($tabq);
							echo "<br/> MOD table for " . $stock . " has been created. <br/>";
							
							$insrt	 =	"INSERT INTO  $modtable ( SYMBOL, TDATE, THOUR, OPEN ) SELECT t1.SYMBOL, t1.TDATE, ";
							$insrt	.=	" t1.thour, t1.open FROM  $ttablename AS t1 LEFT JOIN  $ttablename as t2 ON ";
							$insrt	.=	"( t1.TDATE = t2.TDATE AND t1.HLF = t2.HLF AND t1.ID < t2.ID) ";
							$insrt	.=	"WHERE t2.ID IS NULL ";
							
							$insq	=	$HRS->prepare($insrt);
							$insq->execute();
							unset($insrt);
							
							$findID		=	"SELECT ID, TDATE, HLF, TIME FROM  $ttablename GROUP BY TDATE DESC, THOUR DESC";
							$fnd		=	$HRS->prepare($findID);
							$fnd->execute();
							
							$dateArray		=	array();
							$hlfArray		=	array();
							$idArray		=	array();
							$timeArray		=	array();
					
						
							$ct = 0;
							
							while($row = $fnd->fetch(PDO::FETCH_BOTH))
							{
								$dateArray[$ct] = $row['TDATE'];
								$hlfArray[$ct] 	= $row['HLF'];
								$idArray[$ct]	= $row['ID'];
								$timeArray[$ct]	= $row['TIME'];
								
								$ct++;
								
							}
								
							
							$fnd->closeCursor();
							
							$maxsz	=	sizeof($idArray);
						
							for($i = 0; $i < $maxsz; $i++)
							{
								$id		=	$idArray[$i];
								$tdate	=	$dateArray[$i];
								$ttime	=	$timeArray[$i];
							
								if($ttime == 'ET')
								{
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 1),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 1), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 1 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 09";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 2),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 2), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 2 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 10";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 3),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 3), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 3 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 11";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 4),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 4), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 4 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 12";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 5),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 5), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 5 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 13";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 6),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 6), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 6 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 14";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 7),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 7), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 7 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 15";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
								}
								elseif($ttime == 'LT')
								{
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 1),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 1), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 1 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 10";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 2),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 2), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 2 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 11";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 3),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 3), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 3 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 12";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 4),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 4), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 4 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 13";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 5),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 5), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 5 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 14";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 6),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 6), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 6 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 15";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 7),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 7), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 7 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 16";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
								}
								
								
							}
							$sqluno		=	"UPDATE  $modtable SET TYPICAL_PRICE = (SELECT (HIGH+LOW+CLOSE)/3)";
							
							$sthi2		=	$HRS->prepare($sqluno);
							$sthi2->execute();
							$sthi2->closeCursor();
	
							
							$oldtable	  =	'x'. $this->stock . 'x';
							
							
							
								$altquery 	=	" ALTER TABLE $oldtable MODIFY id INT(5) NOT NULL ";
								$aquery		=	$HRS->prepare($altquery);
								$aquery->execute();
								$aquery = NULL;
								
								$altquery2 		=	" ALTER TABLE $oldtable DROP COLUMN id ";
								$aquery2		=	$HRS->prepare($altquery2);
								$aquery2->execute();
								$aquery2 = NULL;
								
								$altquery3 	=	" ALTER TABLE $modtable MODIFY id INT(5) NOT NULL ";
								$aquery3	=	$HRS->prepare($altquery3);
								$aquery3->execute();
								$aquery3 = NULL;
								
								$altquery4 		=	" ALTER TABLE $modtable DROP COLUMN id ";
								$aquery4		=	$HRS->prepare($altquery4);
								$aquery4->execute();
								$aquery4 = NULL;
								
									$thirdtable		=	'ttt' .$stock;
									$sql  = "CREATE TABLE HR . $thirdtable  ( ";
									$sql .= "id INT(255) NOT NULL AUTO_INCREMENT,"; 
									$sql .= "SYMBOL VARCHAR(30) NULL,";
									$sql .= "TDATE DATE ,";
									$sql .= "THOUR VARCHAR(50),";
									$sql .= "OPEN DECIMAL(50,2)  NULL,";
									$sql .= "HIGH DECIMAL(50,2)  NULL,";
									$sql .= "LOW DECIMAL(50,2)  NULL,";
									$sql .= "CLOSE DECIMAL(50,2)  NULL,";
									$sql .= "TYPICAL_PRICE DECIMAL(50,2)   NULL,";
									$sql .= "DEGREES VARCHAR(50) NULL,";
									$sql .= "index (symbol),";
									$sql .= "PRIMARY KEY (id),";
									$sql .= "reg_date TIMESTAMP)";
									
									$third	=	$HRS->prepare($sql);
									$third->execute();
									$third->closeCursor();
								
								$sql2	 =	" INSERT INTO $thirdtable (SYMBOL, TDATE, ";
								$sql2	.=	" THOUR, OPEN, HIGH, LOW, CLOSE, TYPICAL_PRICE)";
								$sql2	.=	" SELECT SYMBOL, TDATE, THOUR, OPEN, HIGH, LOW, ";
								$sql2	.=	" CLOSE, TYPICAL_PRICE FROM $modtable";
								$lastquery	 =	$HRS->prepare($sql2);
								$lastquery->execute();
								$size 		 = $lastquery->rowCount();
								
								if($lastquery == true)
								{
									$lastque	 =	"INSERT INTO HR. $thirdtable ( SYMBOL, TDATE, THOUR, OPEN, HIGH, LOW, CLOSE, ";
									$lastque	.=	" TYPICAL_PRICE, DEGREES ) SELECT SYMBOL, TDATE, THOUR, OPEN, HIGH, LOW, ";
									$lastque	.=	" CLOSE, TYPICAL_PRICE, DEGREES FROM HR. $oldtable WHERE $oldtable. TDATE < (SELECT ";
									$lastque	.=	" TDATE FROM HR. $modtable ORDER BY TDATE ASC, THOUR ASC LIMIT 1) ORDER BY TDATE DESC,";
									$lastque	.=	" THOUR DESC";
									$lastquery	 =	$HRS->prepare($lastque);
									$lastquery->execute();
									$size 		 = $lastquery->rowCount();
									
									$drop 		= " DROP TABLE  $oldtable ";
									$mquery		=	$HRS->prepare($drop);
									$mquery->execute();
									$mquery = NULL;
									
									$drop 		= " DROP TABLE  $modtable ";
									$mquery		=	$HRS->prepare($drop);
									$mquery->execute();
									$mquery = NULL;
									
									$rename			=	" RENAME TABLE $thirdtable TO $oldtable";
									$rmquery2		=	$HRS->prepare($rename);
									$rmquery2->execute();
									$rmquery = NULL;
								}

								
								
	#################################################################################################
								
								$ccoquery		=	" SELECT ID, CLOSE FROM  $tablename";
								$cco1			=	$HRS->prepare($ccoquery);
								$cco1->execute();
						
								$close	=	array();
								$n		=	0;
						
								while($row = $cco1->fetch(PDO::FETCH_BOTH))
								{
									$close[$n]['ID']	=	$row['ID'];
									$close[$n]['CLOSE']	=	$row['CLOSE'];
									++$n;
								}
								$cco1->closeCursor();
								$maxco	=	sizeof($close);
						
								for($i =0; $i < $maxco; $i++)
								{
							
									$peak	=	"PEAK";
									$valley	=	"VALLEY";
									
									
									$last	= 	$maxco - 1;
							
									if( $i == 0)
									{
										$n		=	$i + 1;
										$tdcci	=	$close[$i]['CLOSE'];
										$ycci	=	$close[$n]['CLOSE'];
										$tid	=	$close[$i]['ID'];
					
										$tid		=	" ' " . $tid . " ' ";
					
					
					
										if($tdcci > $ycci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :peak WHERE ID = $tid";
											$degup			=	$HRS->prepare($utd);
											$degup->execute([':peak'=>$peak]);
											unset($degup);
										}
										else if( $tdcci < $ycci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :valley WHERE ID = $tid";
											$degup			=	$HRS->prepare($utd);
											$degup->execute([':valley'=>$valley]);
											unset($degup);
										}
					
									}
									else if( $i > 0 && $i < $last)
									{
										$n		=	$i + 1;
										$m		=	$i - 1;
										$tdcci	=	$close[$i]['CLOSE'];
										$ycci	=	$close[$n]['CLOSE'];
										$tmcci	=	$close[$m]['CLOSE'];
										$tid		=	$close[$i]['ID'];
										$tid		=	" ' " . $tid . " ' ";
					
										if( $tdcci > $ycci && $tdcci > $tmcci)
										{
											$utd 			= " UPDATE  $tablename set DEGREES = :peak WHERE ID = $tid";
											$degup			=	$HRS->prepare($utd);
											$degup->execute([':peak'=>$peak]);
											unset($degup);
										}
										else if ( $tdcci < $ycci && $tdcci < $tmcci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :valley WHERE ID = $tid";
											$degup			=	$HRS->prepare($utd);
											$degup->execute([':valley'=>$valley]);
											unset($degup);
										}
					
									}
									else if($i == $last)
									{
					
										$m		=	$i - 1;
										$tdcci	=	$close[$i]['CLOSE'];
										$tmcci	=	$close[$m]['CLOSE'];
										$tid	=	$close[$i]['ID'];
										$tid	=	" ' " . $tid . " ' ";
										if($tdcci > $tmcci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :peak  WHERE ID = $tid";
											$degup			=	$HRS->prepare($utd);
											$degup->execute([':peak'=>$peak]);
											unset($degup);
										}
										else if($tdcci < $tmcci)
										{
											$utd = " UPDATE  $tablename set DEGREES = :valley  WHERE ID = $tid";
											$degup			=	$HRS->prepare($utd);
											$degup->execute([':valley'=>$valley]);
											unset($degup);
										}
					
									}
				
								}	

								$arraysql	= "SELECT OPEN, LOW, HIGH, CLOSE, TYPICAL_PRICE FROM $tablename ";
								$values		= $HRS->prepare($arraysql);
								$values->execute();
			
								while($row = $values->fetch(PDO::FETCH_BOTH))
								{
				
				
									$this->hrs_highs[]		= $row['HIGH'];
									$this->hrs_lows[]		= $row['LOW'];
									$this->hrs_opens[]		= $row['OPEN'];
									$this->hrs_closes[]		= $row['CLOSE'];
									$this->hrs_typrice[]	= $row['TYPICAL_PRICE'];	
			
								}
								
								$HRS->commit();
						}
						catch(PDOException $e)
						{
							print "<br/> ERROR LOADING TABLES: " .$e->getMessage();
						}
						
							
					}
			
					else
					{
						echo "<br/>table check for $tablename was not True. <br/>";
						$HRS->beginTransaction();

						try
						{
							$sql  = "CREATE TABLE $tablename ( ";
							$sql .= "id INT(255) NOT NULL AUTO_INCREMENT,"; 
							$sql .= "SYMBOL VARCHAR(30) NULL,";
							$sql .= "TDATE DATE,";
							$sql .= "THOUR VARCHAR(50),";
							$sql .= "OPEN DECIMAL(50,2)  NULL,";
							$sql .= "HIGH DECIMAL(50,2)  NULL,";
							$sql .= "LOW DECIMAL(50,2)  NULL,";
							$sql .= "CLOSE DECIMAL(50,2)  NULL,";
							$sql .= "TYPICAL_PRICE DECIMAL(50,2)   NULL,";
							$sql .= "DEGREES VARCHAR(50) NULL,";
							$sql .= "index (symbol),";
							$sql .= "PRIMARY KEY (id),";
							$sql .= "reg_date TIMESTAMP)";
							
							$que1		=	$HRS->prepare($sql);
							$que1->execute();
							unset($que1);
							
							$setw	 =	"INSERT INTO  $tablename ( SYMBOL, TDATE, THOUR, OPEN ) SELECT t1.SYMBOL,";
							$setw	.=	" t1.TDATE, t1.thour, t1.open FROM  $ttablename AS t1 LEFT JOIN $ttablename ";
							$setw	.=	"as t2 ON ( t1.TDATE = t2.TDATE AND t1.HLF = t2.HLF AND t1.ID < t2.ID) ";
							$setw	.=	"WHERE t2.ID IS NULL ";
					
							$que2		=	$HRS->prepare($setw);
							$que2->execute();
							unset($que2);
							
							$findID		=	"SELECT ID, TDATE, HLF, TIME FROM $ttablename GROUP BY TDATE DESC, THOUR DESC";
					
							$que3		=	$HRS->prepare($findID);
							$que3->execute();
							
							$dateArray		=	array();
							$hlfArray		=	array();
							$idArray		=	array();
							$timeArray		=	array();
						
							while($row = $que3->fetch(PDO::FETCH_BOTH))
							{
								$idArray[]		=	$row['ID'];
								$dateArray[]	=	$row['TDATE'];
								$timeArray[]	=	$row['TIME'];
								$hlfArray[]		=	$row['HLF'];
						
							}
							$que3->closeCursor();
							$maxsiz	=	sizeof($dateArray);
						
							for($i =0 ; $i < $maxsiz; $i++)
							{
								$id		=	$idArray[$i];
								$tdate	=	$dateArray[$i];
								$ttime	=	$timeArray[$i];
								
								if($ttime == 'ET')
								{
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 1),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 1), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 1 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 09";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 2),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 2), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 2 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 10";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 3),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 3), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 3 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 11";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 4),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 4), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 4 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 12";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 5),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 5), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 5 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 13";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 6),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 6), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 6 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 14";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 7),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 7), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 7 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 15";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
								}
								elseif($ttime == 'LT')
								{
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 1),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 1), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 1 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 10";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 2),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 2), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 2 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 11";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 3),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 3), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 3 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 12";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 4),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 4), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 4 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 13";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 5),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 5), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 5 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 14";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 6),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 6), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 6 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 15";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
									
									$sethigh	 =	"UPDATE HR. $modtable SET HIGH = (SELECT MAX(HIGH) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 7),LOW = (SELECT MIN(LOW) FROM HR . $ttablename ";
									$sethigh	.=	" WHERE tdate = ? AND HLF = 7), CLOSE = (SELECT CLOSE FROM HR ."; 
									$sethigh	.=	" $ttablename WHERE tdate = ? AND HLF = 7 AND CLOSE IS NOT NULL ";
									$sethigh	.=	" ORDER BY tdate desc, thour desc, tmin desc limit 1) WHERE tdate = ?";
									$sethigh	.=	" AND thour = 16";
							
									$sthi	=	$HRS->prepare($sethigh);
									$sthi->execute([$tdate,$tdate,$tdate,$tdate]);
									$sthi->closeCursor();
								}
								
							
								
							
							}
							
							$sqluno		=	"UPDATE  HR. $tablename SET TYPICAL_PRICE = (SELECT (HIGH+LOW+CLOSE)/3)";
							$que5		=	$HRS->prepare($sqluno);
							$que5->execute();
							unset($que5);
						
							
							
							$arraysql	= "SELECT OPEN, LOW, HIGH, CLOSE, TYPICAL_PRICE FROM HR. $tablename ";
							$values		= $HRS->prepare($arraysql);
							$values->execute();
			
							while($row = $values->fetch(PDO::FETCH_BOTH))
							{
				
				
								$this->hrs_highs[]		= $row['HIGH'];
								$this->hrs_lows[]		= $row['LOW'];
								$this->hrs_opens[]		= $row['OPEN'];
								$this->hrs_closes[]		= $row['CLOSE'];
								$this->hrs_typrice[]	= $row['TYPICAL_PRICE'];	
			
							}
						
							$HRS->commit();
						}
						catch( PDOException $e)
						{
							print " <br/> ERROR LOADING TABLES: " . $e->getMessage();
						}
							
						
					}
						
						
						$ccoquery		=	" SELECT ID, CLOSE FROM  HR. $tablename";
						$cco1			=	$HRS->prepare($ccoquery);
						$cco1->execute();
						
						$close	=	array();
						$n		=	0;
						
						while($row = $cco1->fetch(PDO::FETCH_BOTH))
						{
							$close[$n]['ID']	=	$row['ID'];
							$close[$n]['CLOSE']	=	$row['CLOSE'];
							++$n;
						}
						$cco1->closeCursor();
						$maxco	=	sizeof($close);
						
						for($i =0; $i < $maxco; $i++)
						{
							
							$peak	=	"PEAK";
							$valley	=	"VALLEY";
							$last	= 	$maxco - 1;
							
							if( $i == 0)
							{
								$n		=	$i + 1;
								$tdcci	=	$close[$i]['CLOSE'];
								$ycci	=	$close[$n]['CLOSE'];
								$tid	=	$close[$i]['ID'];
					
								$tid		=	" ' " . $tid . " ' ";
					
					
					
								if($tdcci > $ycci)
								{
									$utd = " UPDATE  $tablename set DEGREES = :peak WHERE ID = $tid";
									$degup			=	$HRS->prepare($utd);
									$degup->execute([':peak'=>$peak]);
									unset($degup);
								}
								else if( $tdcci < $ycci)
								{
									$utd = " UPDATE  $tablename set DEGREES = :valley WHERE ID = $tid";
									$degup			=	$HRS->prepare($utd);
									$degup->execute([':valley'=>$valley]);
									unset($degup);
								}
					
							}
							else if( $i > 0 && $i < $last)
							{
								$n		=	$i + 1;
								$m		=	$i - 1;
								$tdcci	=	$close[$i]['CLOSE'];
								$ycci	=	$close[$n]['CLOSE'];
								$tmcci	=	$close[$m]['CLOSE'];
								$tid		=	$close[$i]['ID'];
								$tid		=	" ' " . $tid . " ' ";
					
								if( $tdcci > $ycci && $tdcci > $tmcci)
								{
									$utd 		= " UPDATE  $tablename set DEGREES = :peak WHERE ID = $tid";
									$degup		=	$HRS->prepare($utd);
									$degup->execute([':peak'=>$peak]);
									unset($degup);
								}
								if ( $tdcci < $ycci && $tdcci < $tmcci)
								{
									$utd = " UPDATE  $tablename set DEGREES = :valley WHERE ID = $tid";
									$degup			=	$HRS->prepare($utd);
									$degup->execute([':valley'=>$valley]);
									unset($degup);
								}
					
							}
							else if($i == $last)
							{
					
								$m		=	$i - 1;
								$tdcci	=	$close[$i]['CLOSE'];
								$tmcci	=	$close[$m]['CLOSE'];
								$tid	=	$close[$i]['ID'];
								$tid	=	" ' " . $tid . " ' ";
								if($tdcci > $tmcci)
								{
									$utd = " UPDATE  $tablename set DEGREES = :peak  WHERE ID = $tid";
									$degup			=	$HRS->prepare($utd);
									$degup->execute([':peak'=>$peak]);
									unset($degup);
								}
								else if($tdcci < $tmcci)
								{
									$utd = " UPDATE  $tablename set DEGREES = :valley  WHERE ID = $tid";
									$degup			=	$HRS->prepare($utd);
									$degup->execute([':valley'=>$valley]);
									unset($degup);
								}
					
							}
				
						}	
					
					
						/*
						$tcres->closeCursor();
						$clarity		=	"DROP TABLE  $ttablename";
						$que7			=	$HRS->prepare($clarity);
						$que7->execute();
						unset($que7);
						*/
					
				}
			
		}
	
	}
?>



