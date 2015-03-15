<?php

class TrackPlayManager extends Manager {
	public static function getInstance()
	{
		static $instance;
		if (!isset($instance)) {
			$c = __CLASS__;
			$instance = new $c();
		}
		return $instance;
	}
	
	public function getTrackPlaysForShow($showId) {
		$query = "SELECT tp.*, t.*, a.*, g.*
			FROM Tracks AS t
			LEFT JOIN TrackPlay AS tp ON t.t_TrackID = tp.tp_TrackId
			LEFT JOIN Albums AS a ON t.t_AlbumID = a.a_AlbumID
			LEFT JOIN Genres AS g ON a.a_GenreID = g.g_GenreID
			WHERE tp.tp_ScheduledShowInstanceId = ?";
		
		$params = new ParameterList();
		$params->add('i', '', $showId);
		
		$queryResults = $this->doQuery($query, $params);
		
		// Form the results
		$results = array();
		foreach ($queryResults as $queryResult) {
			$tp = new TrackPlay();
			$t = new Track();
			$a = new Album();
			$g = new Genre();
		
			// Sort out the columns and remove the prefixes
			$tpCols = $tCols = $aCols = $gCols = array();
			foreach ($queryResult as $key => $value) {
				if (strpos($key, $tp->getTableColumnPrefix()) === 0) {
					$tpCols[str_replace($tp->getTableColumnPrefix(), '', $key)] = $value;
				} else if (strpos($key, $t->getTableColumnPrefix()) === 0) {
					$tCols[str_replace($t->getTableColumnPrefix(), '', $key)] = $value;
				} else if (strpos($key, $a->getTableColumnPrefix()) === 0) {
					$aCols[str_replace($a->getTableColumnPrefix(), '', $key)] = $value;
				} else if (strpos($key, $g->getTableColumnPrefix()) === 0) {
					$gCols[str_replace($g->getTableColumnPrefix(), '', $key)] = $value;
				}
			}
		
			// Build the TrackPlay
			$tp = new TrackPlay($tpCols);
			$tp->Track = new Track($tCols);
			$tp->Track->Album = new Album($aCols);
			$tp->Track->Album->Genre = new Genre($gCols);
		
			array_push($results, $tp);
		}
		
		return $results;
	}
}

?>