#!/usr/bin/php
<?php
	require_once('../classes/Initialize.php');
	$init = new Initialize();
	$init->setAutoload();

	function printline($msg) {
		echo "$msg\n";
	}

	printline("UNIT TEST: ShowMetadata");
	
	// CONSTRUCTOR
	$showMetadata = new ShowMetadata(array(
			'UID' => 1,
			'Name' => 'Test Name',
			'Description' => 'Test description'
	));
	
	// Has required fields?
	printline($showMetadata->hasAllRequiredFields() ? 'Has all required fields' : 'Is missing some required fields');
	
	// GET
	printline('');
	printline('UID [1]: '.$showMetadata->UID);
	printline('Name [Test Name]: '.$showMetadata->Name);
	printline('Description [Test description]: '.$showMetadata->Description);
	printline('');
	
	// SET
	$showMetadata->UID = 2;
	$showMetadata->Name = 'New Name';
	$showMetadata->Description = 'New description';
	
	// GET
	printline('');
	printline('UID [2]: '.$showMetadata->UID);
	printline('Name [New Name]: '.$showMetadata->Name);
	printline('Description [New description]: '.$showMetadata->Description);
	printline('');
	
	printline("Done");
?>
