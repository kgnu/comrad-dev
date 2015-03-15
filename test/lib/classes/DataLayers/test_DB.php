<?php
	require_once('../classes/Initialize.php');
	$init = new Initialize();
	$init->setAutoload();

	function printline($msg) {
		echo "$msg<br>\n";
	}

	printline("UNIT TEST: DB");
	
	
	// FIND
	printline("SECTION: FIND");
	
	$c = DB::getInstance('MySql');
	
	// Find all albums with ID < 1000
	// Only display 10 of the results, sorted by Title
	$results = $c->find(new Album(), $count, array(
		'limit' => 10,
		'sortcolumn' => 'Title',
		'rangecolumn' => 'AlbumID',
		'ascending' => true,
		'rangemax' => 1000,
		'rangeinclusivemax' => false
	));
	printline("Found ".count($results)." of $count results");
	foreach($results as $album) {
		printline("Album #$album->AlbumID: $album->Title");
	}
	printline();


	$c = DB::getInstance('MySql');
	
	// Find all shows with (Now < StartDateTime <= 2 Weeks from now)
	// Display all of the results, sorted by StartDateTime
	$now = time();
	$twoWeeksFromNow = $now + (2 * 7 * 24 * 60 * 60);
	
	$results = $c->find(new Show(), $count, array(
		'limit' => NULL,
		'sortcolumn' => 'StartDateTime',
		'rangecolumn' => 'StartDateTime',
		'rangemin' => $now,
		'rangemax' => $twoWeeksFromNow,
		'rangeinclusivemin' => false
	));
	printline("Found ".count($results)." of $count results");
	foreach($results as $show) {
		printline("Show #$show->UID: ".date('D, d M Y H:i:s', $show->StartDateTime));
	}
	printline();
	
	
	
	// INSERT
	// $showMetadata = new ShowMetadata(array(
	// 	"Name" => "Test Name",
	// 	"Description" => "Test description"
	// ));
	// 
	// $id = $c->insert($showMetadata);
	// 
	// printline('Insertion ID: '.$id);
	
	printline("Done");
?>
