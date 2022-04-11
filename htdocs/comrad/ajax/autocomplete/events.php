<?php
	require_once('initialize.php');
	
	if ($uri->hasKey('q') && $uri->hasKey('type')) {
		// The string to fuzzy search on
	    $q = $uri->getKey('q');
		
		// Search results are restricted to this particular type of event
		$type = $uri->getKey('type');
		
		// If showall is true, return non-active events as well.  Defaults to false.
		$showall = false;
		if ($uri->hasKey('showall')) {
		    $showall = ($uri->getKey('showall') == 'true' ? true : false);
		}
		
		// Whether we should return the option to create a new event (defaults to false)
		$allowCreateNew = ($uri->hasKey('allowcreatenew') && $uri->getKey('allowcreatenew') == true);
		
		// Whether we should return the option to select none (defaults to false)
		$allowNone = ($uri->hasKey('allownone') && $uri->getKey('allownone') == true);
		
		// Limit the number of results
		$limit = ($uri->hasKey('limit') && $uri->getKey('limit') > 0 ? $uri->getKey('limit') : null);
		
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
		$event = new $type($criteria);
		array_push($mappedCriteria, array($event->getTitleColumn(), 'LIKE', "%$q%"));
		if ($event->hasColumn('Active') && !$showall) array_push($mappedCriteria, array('Active', '=', true));
		
		// Get the results
		$queryResults = DB::getInstance('MySql')->find(new DBCriteria($type, $mappedCriteria), $count, array('fuzzytextsearch' => true, 'limit' => $limit, 'sortcolumn' => $event->getTitleColumn()));
		
		// Format the results
		$results = array();
		foreach($queryResults as $queryResult) {
			array_push($results, $queryResult->{$event->getTitleColumn()}.'|'.str_replace('|', '&vert;', json_encode($queryResult->toArray())));
		}
		
		if ($allowNone && strlen(trim($uri->getKey('q'))) == 0) {
			echo "<i>None</i>|\n";
		}
		
		// Print the results
		foreach($results as $result) {
			echo "$result\n";
		}
		
		if ($allowCreateNew && strlen(trim($uri->getKey('q'))) > 0) {
			// Initialize the soon-to-be-created event
			$event = new $type();
			$event->{$event->getTitleColumn()} = $uri->getKey('q');
			
			// Set the defaults for the soon-to-be-created event
			foreach ($event->getColumns() as $columnName => $column) {
			    if (array_key_exists('default', $column)) {
			        $event->$columnName = $column['default'];
			    }
			}
			
			// Print out the needed stuff to send back the soon-to-be-created event
			echo '<img src="media/icon-small-add.png" width="12" height="12" class="ac_icon" />';
			echo $init->asciiHtmlEntities('Create New "') . ucwords($uri->getKey('q')) . '"';
            echo '|'.json_encode($event->toArray());
		}
		
		exit();
	}
?>