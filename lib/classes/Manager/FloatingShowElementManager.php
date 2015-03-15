<?php

class FloatingShowElementManager extends Manager {
	public static function getInstance()
	{
		static $instance;
		if (!isset($instance)) {
			$c = __CLASS__;
			$instance = new $c();
		}
		return $instance;
	}
	
	public function getFloatingShowElementsForShow($showId) {
		$query = "SELECT fse.*, t.*, a.*, g.*, e.*
			FROM FloatingShowElement AS fse
			LEFT JOIN Event AS e ON e.e_Id = fse.fse_EventId
			LEFT JOIN Tracks AS t ON t.t_TrackID = fse.fse_TrackId
			LEFT JOIN Albums AS a ON t.t_AlbumID = a.a_AlbumID
			LEFT JOIN Genres AS g ON a.a_GenreID = g.g_GenreID
			WHERE fse.fse_ScheduledShowInstanceId = ?";
		
		$params = new ParameterList();
		$params->add('i', '', $showId);
		
		$queryResults = $this->doQuery($query, $params);
		
		// Form the results
		$results = array();
		foreach ($queryResults as $queryResult) {
			$fseClass = $queryResult['fse_DISCRIMINATOR'];
			$fse = new $fseClass();
			$t = new Track();
			$a = new Album();
			$g = new Genre();
			$eClass = $queryResult['e_DISCRIMINATOR'];
			$e = ($eClass ? new $eClass() : new Event());
		
			// Sort out the columns and remove the prefixes
			$fseCols = $tCols = $aCols = $gCols = $eCols = array();
			foreach ($queryResult as $key => $value) {
				if (strpos($key, $fse->getTableColumnPrefix()) === 0) {
					$fseCols[str_replace($fse->getTableColumnPrefix(), '', $key)] = $value;
				} else if (strpos($key, $t->getTableColumnPrefix()) === 0) {
					$tCols[str_replace($t->getTableColumnPrefix(), '', $key)] = $value;
				} else if (strpos($key, $a->getTableColumnPrefix()) === 0) {
					$aCols[str_replace($a->getTableColumnPrefix(), '', $key)] = $value;
				} else if (strpos($key, $g->getTableColumnPrefix()) === 0) {
					$gCols[str_replace($g->getTableColumnPrefix(), '', $key)] = $value;
				} else if (strpos($key, $e->getTableColumnPrefix()) === 0) {
					$eCols[str_replace($e->getTableColumnPrefix(), '', $key)] = $value;
				}
			}
		
			// Build the FloatingShowElement
			$fse = new $fseClass($fseCols);
			
			if ($fse->hasColumn('Track')) {
				$fse->Track = new Track($tCols);
				$fse->Track->Album = new Album($aCols);
				$fse->Track->Album->Genre = new Genre($gCols);
			}
			
			if ($fse->hasColumn('Event')) {
				$fse->Event = new $eClass($eCols);
				
				// TODO: Should be replaced by some more generic method such as $se->Event->fetchAllForeignKeyItems
				if ($eClass == 'PSAEvent') $fse->Event->fetchForeignKeyItem('PSACategory');
			}
		
			array_push($results, $fse);
		}
		
		return $results;
	}
}

?>