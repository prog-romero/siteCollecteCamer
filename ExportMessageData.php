<?php

namespace Phppot;
require_once __DIR__ . './lib/DataSource.php';
$db = new DataSource();
$result =$db->select("SELECT text,vote_final FROM message");

if(!empty($result)){
	$delimiter = ",";
	$filename = "dataMessages.csv";
	$f = fopen('php://memory','w');
	
	$fields = array('Text','Vote_Final');
	fputcsv($f,$fields,$delimiter);
	foreach($result as $re){
		$lineData = array($re['text'], $re['vote_final']);
		fputcsv($f,$lineData,$delimiter);
	}
	
	fseek($f,0);
	
	//header('Content-Type:text/csv');
	header('Content-Disposition:attachment; filename = "' .$filename . '";');
	
	fpassthru($f);
}
exit;

?>