#!/usr/bin/php
<?php
	require_once('../classes/Initialize.php');
	$init = new Initialize();
	$init->setAutoload();

	function printline($msg) {
		echo "$msg\n";
	}

	printline("UNIT TEST: NewAlbum");
	$album = new Album(array(
    'Title' => 'Test Title',
    'Artist' => 'Test Artist',
    'Label' => 'Test Label',
    'GenreID' => 4,
    'AddDate' => time(),
    'Local' => true,
    'Compilation' => true
	));
	
	printline($album->hasAllRequiredFields() ? 'true' : 'false');
	
	// printline('');
	// printline('GenreID [1]: '.$album->GenreID);
	// printline('Label [Test Label]: '.$album->Label);
	// printline('Title [Rubber Soul]: '.$album->Title);
	// printline('Title [Rubber Soul]: '.$album->Title);
	// printline('Artist [The Flaming Lips]: '.$album->Artist);
	// printline('AddDate: '.$album->AddDate);
	// printline('Local [1]: '.$album->Local);
	// printline('Compilation [0]: '.$album->Compilation);
	// printline('');
	// 
	// $album->Local = false;
	// printline('Local[0]: '.$album->Local);
	// 
	// $album->Artist = "The Beatles  ";
	// printline('Artist [The Beatles]: '.$album->Artist);
	
	printline("Done");
?>
