<?php

class KeywordSearch {
	private $db = NULL;
	private $is_connected = false;
	
	public function __construct() {
		// We are using Initialize to gain access to configuration, make
		//  sure that the class has been defined...
		if (!class_exists('Initialize'))
		die('Class \'' . get_class($this) . '\' requires class \'Initialize\'.\n');
		
		global $init;  // Global InitWebTools object
		 
		// Connect to database...
		$this->db = new mysqli(
			$init->getProp('MySql_Host'),
			$init->getProp('MySql_Username'),
			$init->getProp('MySql_Password'),
			$init->getProp('MySql_Database')
		);
		
		// Check if we have a good connection...
		$this->is_connected = ($this->db->connect_errno == 0);
		if (!$this->is_connected) $init->log("Error connecting to Catalog database: " . $this->db->connect_error);
	}
	
	public function search($searchString, $options = array()) {
		global $init;
		
		// Set up parameters
		$options = array_merge(array(
			'limit' => 30,
			'offset' => 0,
		), $options);
		
		$query = "SELECT t.*, a.*, g.*
			FROM Tracks AS t
			LEFT JOIN Albums AS a ON t.t_AlbumID = a.a_AlbumID
			LEFT JOIN Genres AS g ON a.a_GenreID = g.g_GenreID
			WHERE MATCH (g.g_Name,  a.a_Title, a.a_Artist, a.a_Label, t.t_Title, t.t_Artist) AGAINST (? IN BOOLEAN MODE) LIMIT ".$options['offset'].",".$options['limit'];
				
		// Prepare the query
		$stmt = $this->db->prepare($query);
		if (!$stmt) {
			$init->log("Could not prepare keyword search query: " . $this->db->error . ' [QUERY: ' . $query . ']');
			return false;
		}
		
		// Add '+' to the beginning of each word
		$searchString = preg_replace('/([^ ]+)/', '+$1', $searchString);
		$searchString = str_replace('"', '', $searchString);
		
		// Bind the search string
		$stmt->bind_param('s', $searchString);
	
		// Execute the query
		if (!$stmt->execute()) {
			$init->log("Could not execute keyword search query: " . $this->db->error);
			return false;
		}
		
		// Get the results from the query and clean up
		$meta = $stmt->result_metadata();
		while ($field = $meta->fetch_field()) {
			$newParams[] = &$row[$field->name];
		}
		call_user_func_array(array($stmt, 'bind_result'), $newParams);
		$queryResults = array();
		while ($stmt->fetch()) {
			foreach ($row as $key => $val) {
				$c[$key] = $val;
			}
			$queryResults[] = $c;
		}
		
		// Form the results
		$results = array();
		foreach($queryResults as $queryResult) {
			$t = new Track();
			$a = new Album();
			$g = new Genre();
			
			// Sort out the columns and remove the prefixes
			$tCols = $aCols = $gCols = array();
			foreach ($queryResult as $key => $value) {
				if (strpos($key, $t->getTableColumnPrefix()) === 0) {
					$tCols[str_replace($t->getTableColumnPrefix(), '', $key)] = $value;
				} else if (strpos($key, $a->getTableColumnPrefix()) === 0) {
					$aCols[str_replace($a->getTableColumnPrefix(), '', $key)] = $value;
				} else if (strpos($key, $g->getTableColumnPrefix()) === 0) {
					$gCols[str_replace($g->getTableColumnPrefix(), '', $key)] = $value;
				}
			}
		
			// Build the TrackPlay
			$t = new Track($tCols);
			$t->Album = new Album($aCols);
			$t->Album->Genre = new Genre($gCols);

			array_push($results, $t);
		}
		
		return $results;
	}
}

?>