<?php

	require_once('initialize.php');
	
	if ($uri->hasKey('q')) {
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . date('D, d M Y H:i:s') . 'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: text/x-json');
		
		$q = $uri->getKey('q');
		
		$options = array();
		if ($limit = $uri->getKey('limit')) $options['limit'] = $limit;
		if ($offset = $uri->getKey('offset')) $options['offset'] = $offset;
		if ($sortcolumn = $uri->getKey('sortcolumn')) $options['sortcolumn'] = $sortcolumn;
		if ($ascending = $uri->getKey('ascending')) $options['ascending'] = ($ascending != 'false');
		if ($type = $uri->getKey('type')) $options['type'] = $type;
		
		$results = MusicCatalogSearchManager::getInstance()->search($q, $options);
		
		$objects = array();
		foreach($results as $result) {
			array_push($objects, $result->toArray());
		}
		
		echo json_encode($objects);
		exit();
	}
?>