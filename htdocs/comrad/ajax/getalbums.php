<?php
	require_once('initialize.php');
	
	if ($uri->hasKey('page'))
	{
		$searchAlbum = NULL;
	
		$page = $uri->getKey('page');
		$itemsPerPage = $uri->getKey('rp');
		$sortname = $uri->getKey('sortname');
		$sortorder = $uri->getKey('sortorder');
	
		if ($uri->hasKey('query'))
		{
			$searchAlbum = new Album();
			if ($uri->getKey('qtype') == 'artist' && strlen($uri->getKey('query')) > 0)
			{
				$searchAlbum->Artist = $uri->getKey('query');
			}
			else if ($uri->getKey('qtype') == 'title' && strlen($uri->getKey('query')) > 0)
			{
				$searchAlbum->Title = $uri->getKey('query');
			}
			else if ($uri->getKey('qtype') == 'label' && strlen($uri->getKey('query')) > 0)
			{
				$searchAlbum->Label = $uri->getKey('query');
			}
		}
	
		// Connect to Catalog and get a page's worth of albums
		$catalog = DB::getInstance('MySql');
		$albums = $catalog->find($searchAlbum, $count, array(
				"limit" => $itemsPerPage,
				"offset" => ($page - 1) * $itemsPerPage,
				"sortcolumn" => $sortname,
				"ascending" => $sortorder === 'asc',
				"fuzzytextsearch" => true
		));
		$genres = DBUtilities::getGenreNames();
	
		$data = array();
		$data['page'] = $page;
		$data['total'] = $count;
		$data['rows'] = array();
	
		foreach ($albums as $album)
		{
			$data['rows'][] = array(
				'id' => $album->AlbumID,
				'cell' => array(
					$album->CDCode,
					$album->Artist,
					$album->Title,
					$album->Label,
					$genres[$album->GenreID],
					date('M j, Y', $album->AddDate),
					$album->Local ? '<img src="media/flexigrid/check.png" width="16" height="16" alt="Local" />' : '',
					$album->Compilation ? '<img src="media/flexigrid/check.png" width="16" height="16" alt="Compilation" />' : '',
					$album->Location
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
