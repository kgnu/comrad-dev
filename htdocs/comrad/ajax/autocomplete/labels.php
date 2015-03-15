<?php
	require_once('initialize.php');
	
	if ($uri->hasKey('q')) {
		// The string to fuzzy search on
		$q = $uri->getKey('q');
		
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
		array_push($mappedCriteria, array('Label', 'LIKE', "%$q%"));
		
		// Get the results
		$albums = DB::getInstance('MySql')->find(new DBCriteria('Album', $mappedCriteria), $count, array('fuzzytextsearch' => true, 'limit' => $limit, 'groupcolumn' => 'Label', 'sortcolumn' => 'Label'));
		
		// Format the results
		$results = array();
		foreach($albums as $album) {
			array_push($results, $album->Label);
		}
		
		// Print the results
		foreach($results as $result) {
			echo "$result\n";
		}
		
		exit();
		break;
	}
?>