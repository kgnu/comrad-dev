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
		
		$query = "SELECT *
			FROM TrackFullTextSearchInfo
			".($searchString != '' ? "WHERE MATCH (tftsi_TrackArtist,tftsi_TrackTitle,tftsi_AlbumArtist,tftsi_AlbumLabel,tftsi_AlbumTitle,tftsi_GenreName) AGAINST (? IN BOOLEAN MODE)" : "")."
			LIMIT {$options['offset']}, {$options['limit']}";
			
		$params = new ParameterList();
		if ($searchString != null) $params->add('s', '', $searchString);
		
		$queryResults = $this->doQuery($query, $params);
		
		// Form the results
		$results = array();
		foreach ($queryResults as $queryResult) {
			$t = new Track();
			$a = new Album();
			$g = new Genre();
			
			// Sort out the columns and remove the prefixes
			$tCols['TrackID'] = $queryResult['tftsi_TrackId'];
			$tCols['AlbumID'] = $queryResult['tftsi_AlbumId'];
			$tCols['Artist'] = $queryResult['tftsi_TrackArtist'];
			$tCols['Title'] = $queryResult['tftsi_TrackTitle'];
			$aCols['AlbumID'] = $queryResult['tftsi_AlbumId'];
			$aCols['Artist'] = $queryResult['tftsi_AlbumArtist'];
			$aCols['Label'] = $queryResult['tftsi_AlbumLabel'];
			$aCols['Title'] = $queryResult['tftsi_AlbumTitle'];
			$aCols['AlbumArt'] = $queryResult['tftsi_AlbumArt'];
			$gCols['GenreName'] = $queryResult['tftsi_GenreName'];
			
			$t = new Track($tCols);
			$t->Album = new Album($aCols);
			$t->Album->Genre = new Genre($gCols);

			array_push($results, $t);
		}
		
		if (count($results) == 0) {
			//check to be sure TrackFullTextSearchInfo has values. If not, regenerate the table
			$query = "SELECT COUNT(*) as c FROM TrackFullTextSearchInfo";
			$queryResults = $this->doQuery($query);
			if ($queryResults[0]['c'] == 0) {
				$this->doQuery("
					INSERT INTO TrackFullTextSearchInfo 
						(tftsi_TrackId, tftsi_TrackArtist, tftsi_TrackTitle, 
						tftsi_AlbumId, tftsi_AlbumArtist, tftsi_AlbumLabel, 
						tftsi_AlbumTitle, tftsi_AlbumArt,
						tftsi_GenreName)
					SELECT
						t.t_TrackId, t.t_Artist, t.t_Title,
						a.a_AlbumId, a.a_Artist, a.a_Label, 
						a.a_Title, a.a_AlbumArt,
						g.g_Name
					FROM
						Tracks AS t
						LEFT JOIN Albums as a ON t.t_AlbumId = a.a_AlbumId
						LEFT JOIN Genres as g ON a.a_GenreId = g.g_GenreId
				");
			}
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