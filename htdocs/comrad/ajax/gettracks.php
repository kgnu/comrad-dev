<?php
	require_once('initialize.php');
	
	if ($uri->hasKey('page'))
	{
		$searchTrack = NULL;
	
		$page = $uri->getKey('page');
		$itemsPerPage = $uri->getKey('rp');
		$sortname = $uri->getKey('sortname');
		$sortorder = $uri->getKey('sortorder');
	
		if ($uri->hasKey('query'))
		{
			$searchTrack = new Track();
			if ($uri->getKey('qtype') == 'artist' && strlen($uri->getKey('query')) > 0)
			{
				$searchTrack->Artist = ($uri->getKey('query'));
			}
			else if ($uri->getKey('qtype') == 'title' && strlen($uri->getKey('query')) > 0)
			{
				$searchTrack->Title = ($uri->getKey('query'));
			}
			else if ($uri->getKey('qtype') == 'AlbumID' && strlen($uri->getKey('query')) > 0)
			{
				$searchTrack->AlbumID = (int)($uri->getKey('query'));
			}
		}
	
		// Connect to Catalog and get a page's worth of tracks
		$catalog = DB::getInstance('MySql');
		$tracks = $catalog->find($searchTrack, $count, array(
				"limit" => $itemsPerPage,
				"offset" => ($page - 1) * $itemsPerPage,
				"sortcolumn" => $sortname,
				"ascending" => $sortorder === 'asc',
				"fuzzytextsearch" => true
		));
	
		$data = array();
		$data['page'] = $page;
		$data['total'] = $count;
		$data['rows'] = array();
			
		foreach ($tracks as $track)
		{	
			$data['rows'][] = array(
				'id' => $track->TrackID,
				'cell' => array(
					$track->DiskNumber,
					$track->TrackNumber,
					$track->Title,
					sprintf("%d:%02d", (int)($track->Duration / 60), ($track->Duration % 60)),
					$track->Artist
				)
			);
		}
	
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . date('D, d M Y H:i:s') . 'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: text/x-json');
		echo json_encode($data);
		exit();
}
?>
