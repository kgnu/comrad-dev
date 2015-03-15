<?php

	//created sean williams 4/4/11
	//to be run every minute
	//checks the list of upcoming shows created by getUpcomingShowsFromDb.php
	//and starts StreamRipper to record the audio stream
	
	//this script uses StreamRipper - http://manpages.ubuntu.com/manpages/dapper/man1/streamripper.1.html
	
	
	set_error_handler('errorHandler');
	function errorHandler($errno, $errstr, $errfile, $errline) {
		echo '<br /><b>Error</b>: ' . $errstr . ' (line '.$errline.' in '.$errfile.')<br />';
	}
	
	//open the file
	$upcomingEventsFile = "/var/www/comrad-dev-read-only/lib/utilities/archiving/upcomingEvents.txt"; //if updating this, also update the same variable in archiveShows.php
	if (!file_exists($upcomingEventsFile)) {
		require('getUpcomingEvents.php'); //create the upcoming events file
	}
	$upcomingEventsText = file_get_contents($upcomingEventsFile);
	$upcomingEventsText = explode("\n",$upcomingEventsText);
	//see if there are any active events
	$activeEvent = false;
	foreach ($upcomingEventsText as $uet) {
		if (!empty($uet)) { //ignore blank lines in the text file
			$v = explode("|",$uet);
			$event = array();
			$event["title"] = $v[0];
			$event["startTime"] = $v[1];
			$event["endTime"] = $v[2];
			if (time() >= (int)$event["startTime"] && time() <= (int)$event["endTime"]) {
				$activeEvent = true;
				break;
			}
		}
	}
	
	
	//if we didn't find an active event, abandon the script
	if (!$activeEvent) {
		echo 'no active event, exiting script.<br />';
		exit();
	} else {
		echo 'active event, trying to begin recording...<br />';
	}
	
	//start the stream to download 
	
	$stream = 'stream.kgnu.net:8000/KGNU_live_high.mp3.m3u'; //the stream URL without the http:// - the http:// causes a hostname can't be resolved error
	$destination = "/var/www/comrad-dev-read-only/lib/utilities/archiving/kgnu-archives/";
	//make a new directory for the show ...streamripper will automatically create the directory
	$folder = date("m-d-y_Gi",$event["startTime"])."_".$event["title"];
	$folder = str_replace(" ","",$folder); //remove spaces from the folder name
	if (file_exists($destination.$folder."/stream.mp3")) {
		//file already exists - the script is already running
		echo 'stream is already being recorded at '.$destination.$folder.'/stream.mp3...exiting script.';
		exit();
	}
	$command = "streamripper ".$stream; //setup the terminal command
	$command .= " -d ".$destination.$folder; //set the destination directory
	$command .= " -a stream"; //record to one file - we don't want to separate the stream into tracks
	$command .= " -m 60"; //reset stream connection after a 60 second timeout
	$command .= " -l ".((int)$event["endTime"] - (int)$event["startTime"]);
	$command .= " --debug"; //save a debugging log
	$command .= " > /dev/null &"; //run asynchronously so the script finishes - got this from http://www.sitecrafting.com/blog/to-run-php-code-in/
	
	echo 'launching streamripper: '.$command."<br />";;
	exec($command);
	

	
	//what if the server clock is off? can we account for that?
	//recording start time/recording end time for each...some may start at different times than they say (afternoon sound alternative is schedueld for 12:00, but doesn't start until 12:06
	//delete old audio script...we may need a new version of this	
	//are these backed up?
	
	//let the script start if it's running late...possibly, just by checking if the file's there. it should also resume if it unexpectedly restarts (the resume functionality can be deferred)
	
	//we'll want a human-readable file name
	
	//note from eric (comrad-dev issue 120):
	//For example, if Afternoon Sound Alternative ends at 3:00 PM, the recording should end at 3:05 PM.
	
	//store file path in database

?>
