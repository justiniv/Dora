<?php
		require_once("includes/functions.php");
		require_once("includes/config.php");
		require_once("includes/database.php");

?>

<?php
	$dbase			=	new Database;
	$allStocks		=	$dbase->full_stocks;
	$smStocks		=	$dbase->stocks;
	
	$HR				= 	$dbase->HR;
	$gentab			= 	$dbase->gentab;
	$size 			=	sizeof($allStocks);
	$Bsize 			=	sizeof($smStocks);
	
	$questions		=	array();
	
	$greens			=	array();
	
	$reds			=	array();
	
	
	
	foreach($smStocks as $symbol)
	{
		echo "<br />" .$symbol ."<br />";
		$symbol	=	new Stock($symbol);
		
		
		
		if(property_exists($symbol, "stock"))
		{
			
			$filename	= $symbol->stock . '.csv';
			$filename	=	'C:/xampp/htdocs/jst/Hist/GOOGLE/HOURLY/' . $filename;
			
			
			if( filesize($filename) > 3000)
			{
				echo "Working on ".$symbol->stock ."." ;
				echo "<hr/>";
				
				$symbol->build_google_TblHRS();
			}
			
			
		}
		
		
		
	}
	
?>

	<BODY>
	</BODY>



</HTML>
