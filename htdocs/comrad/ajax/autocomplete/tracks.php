<?php
	require_once('initialize.php');
	
	if ($uri->hasKey('q')) {
		// The string to fuzzy search on
		$q = $uri->getKey('q');
		
		// Whether we should return the option to create a new track (defaults to false)
		$allowCreateNew = ($uri->hasKey('allowcreatenew') && $uri->getKey('allowcreatenew') == true);
		
		// Limit the number of results (max of 50, defaults to 25)
		$limit = $uri->hasKey('limit') && $uri->getKey('limit') > 0 ? min($uri->getKey('limit'), 50) : 25;
		
		// Criteria to be included in the find query in associative array form
		$criteria = array();
		if ($uri->hasKey('criteria')) {
			$criteria = json_decode($uri->getKey('criteria'), true);
		}
		
		$mappedCriteria = array();
		foreach ($criteria as $key => $value) {
			array_push($mappedCriteria, array($key, '=', $value));
		}
		
		// Create the query object
		array_push($mappedCriteria, array('Title', 'LIKE', "%$q%"));
		
		// Get the results
		$tracks = DB::getInstance('MySql')->find(new DBCriteria('Track', $mappedCriteria), $count, array('fuzzytextsearch' => true, 'limit' => $limit, 'sortcolumn' => 'Title'));
		
		// Format the results
		$results = array();
		foreach($tracks as $track) {
			array_push($results, $track->Title.'|'.json_encode($track->toArray()));
		}
		
		// Print the results
		foreach($results as $result) {
			echo "$result\n";
		}
		
		if ($allowCreateNew && strlen(trim($uri->getKey('q'))) > 0) {
			// Initialize the soon-to-be-created track
			$newTrack = new Track();
			$newTrack->Title = $uri->getKey('q');
			
			// Set the defaults for the soon-to-be-created event
			foreach ($newTrack->getColumns() as $columnName => $column) {
			    if (array_key_exists('default', $column)) {
			        $newTrack->$columnName = $column['default'];
			    }
			}
			
			// Print out the needed stuff to send back the soon-to-be-created event
			echo '<img src="media/icon-small-add.png" width="12" height="12" class="ac_icon" />';
			echo $init->asciiHtmlEntities('Create New "') . ucwords($uri->getKey('q')) . '"';
            echo '|'.json_encode($newTrack->toArray());
		}
		
		exit();
	}
?>