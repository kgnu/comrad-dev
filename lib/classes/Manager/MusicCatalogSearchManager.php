<?php

class MusicCatalogSearchManager extends Manager {
	public static function getInstance() {
		static $instance;
		if (!isset($instance)) {
			$c = __CLASS__;
			$instance = new $c();
		}
		return $instance;
	}
	
	public function search($searchString, $options) {
		$options = array_merge(array(
			'type' => 'track',
			'limit' => 30,
			'offset' => 0,
			'sortcolumn' => 'Title',
			'ascending' => true
		), $options);
		
		// Add '+' to the beginning of each word for the binary search
		if ($searchString != '') {
			$searchString = preg_replace('/([^ ]+)/', '+$1', $searchString);
			$searchString = str_replace('"', '', $searchString);
		}
		
		return $options['type'] == 'album' ? $this->searchAlbums($searchString, $options) : $this->searchTracks($searchString, $options);
	}
	
	protected function searchTracks($searchString, $options = array()) {
		//we will query Tracks, Albums and Genres separately so that the queries go faster
		//otherwise, the query takes a long time because the full text index isn't set up to span all three tables
		
		$trackIds = array();
		
		$query = "SELECT t.t_TrackId
			FROM Tracks AS t
			".($searchString != '' ? "WHERE MATCH (t.t_Title, t.t_Artist) AGAINST (? IN BOOLEAN MODE)" : "")."
			ORDER BY t.t_{$options['sortcolumn']} ".($options['ascending'] ? "ASC" : "DESC")."
			LIMIT {$options['offset']}, {$options['limit']}";
		
		$params = new ParameterList();
		if ($searchString != null) $params->add('s', '', $searchString);
		
		$queryResults = $this->doQuery($query, $params);
		
		foreach ($queryResults as $queryResult) {
			$trackIds[] = $queryResult['t_TrackId'];
		}
		
		$query = "SELECT t.t_TrackId
			FROM Albums AS a
			INNER JOIN Tracks as t ON t.t_AlbumID = a.a_AlbumId
			".($searchString != '' ? "WHERE MATCH (a.a_Title, a.a_Artist, a.a_Label) AGAINST (? IN BOOLEAN MODE)" : "")."
			ORDER BY t.t_{$options['sortcolumn']} ".($options['ascending'] ? "ASC" : "DESC")."
			LIMIT {$options['offset']}, {$options['limit']}";
		
		$queryResults = $this->doQuery($query, $params);
		
		foreach ($queryResults as $queryResult) {
			$trackIds[] = $queryResult['t_TrackId'];
		}
		
		$query = "SELECT t.t_TrackId
			FROM Albums AS a
			INNER JOIN Tracks as t ON t.t_AlbumID = a.a_AlbumId
			INNER JOIN Genres AS g ON a.a_GenreId = g.g_GenreId
			".($searchString != '' ? "WHERE MATCH (g.g_Name) AGAINST (? IN BOOLEAN MODE)" : "")."
			LIMIT {$options['offset']}, {$options['limit']}";
		
		$queryResults = $this->doQuery($query, $params);
		
		foreach ($queryResults as $queryResult) {
			$trackIds[] = $queryResult['t_TrackId'];
		}
		
		if (count($trackIds) == 0) {
			return array();
		}
		
		$query = "SELECT t.*, a.*, g.*
			FROM Tracks AS t
			LEFT JOIN Albums AS a ON t.t_AlbumID = a.a_AlbumID
			LEFT JOIN Genres AS g ON a.a_GenreID = g.g_GenreID
			WHERE t.t_TrackId IN (" . implode(",", $trackIds) . ") 
			ORDER BY t.t_{$options['sortcolumn']} ".($options['ascending'] ? "ASC" : "DESC")."
			LIMIT {$options['offset']}, {$options['limit']}";
		
		$queryResults = $this->doQuery($query);
		
		// Form the results
		$results = array();
		foreach ($queryResults as $queryResult) {
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
			
			$t = new Track($tCols);
			$t->Album = new Album($aCols);
			$t->Album->Genre = new Genre($gCols);

			array_push($results, $t);
		}
		
		return $results;
	}
	
	protected function searchAlbums($searchString, $options = array()) {
		$query = "SELECT a.*, g.*
			FROM Albums AS a
			LEFT JOIN Genres AS g ON a.a_GenreID = g.g_GenreID
			".($searchString != '' ? "WHERE MATCH (g.g_Name, a.a_Title, a.a_Artist, a.a_Label) AGAINST (? IN BOOLEAN MODE)" : "")."
			ORDER BY a.a_{$options['sortcolumn']} ".($options['ascending'] ? "ASC" : "DESC")."
			LIMIT {$options['offset']}, {$options['limit']}";

		$params = new ParameterList();
		if ($searchString != null) $params->add('s', '', $searchString);
		
		$queryResults = $this->doQuery($query, $params);
		
		// Form the results
		$results = array();
		foreach ($queryResults as $queryResult) {
			$a = new Album();
			$g = new Genre();
			
			// Sort out the columns and remove the prefixes
			$aCols = $gCols = array();
			foreach ($queryResult as $key => $value) {
				if (strpos($key, $a->getTableColumnPrefix()) === 0) {
					$aCols[str_replace($a->getTableColumnPrefix(), '', $key)] = $value;
				} else if (strpos($key, $g->getTableColumnPrefix()) === 0) {
					$gCols[str_replace($g->getTableColumnPrefix(), '', $key)] = $value;
				}
			}
			
			$a = new Album($aCols);
			$a->Genre = new Genre($gCols);

			array_push($results, $a);
		}
		
		return $results;
	}
}

?>