<?
	require ('../bootstrap.php');
	error_reporting(E_ALL);
	ini_set('max_execution_time', 0);
	
	print "WebXtractor Indexer\n\n";
	
	$datasourceSvc	=	Application_Service_Datasource::getInstance();
	
	foreach ($datasourceSvc->getDatasources() as $datasource) {
		print "indexing " . $datasource->url . " .. ";
		
		$indexerSvc 		= new Application_Service_Indexer($datasource);
		$indexerSvc->run();
		
		$datasource->setUpdated(time());
 		$datasourceSvc->saveDatasource($datasource);
 		
		print "ok!\n";
	}
	print "\nAll done!\n";
?>