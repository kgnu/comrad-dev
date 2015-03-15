<?php
	
	if ($_SERVER['REQUEST_METHOD'] == 'GET') $disableAuthentication = true;
	
	require_once(dirname(__FILE__) . '/../../../lib/classes/Initialize.php');

	// Define a temporary initialize object to get at config...
	$tmpInit = new Initialize();
	$tmpInit->setAutoload();
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['token']) && $_POST['token'] === $tmpInit->getProp('ArchiveToken')) $disableAuthentication = true;
	unset($tmpInit);
	
	require_once('initialize.php');
	
	PermissionManager::getInstance()->disableAuthorization();
	
	function get($dbObject, $params, $db)
	{
		return DB::getInstance($db)->get($dbObject)->toArray();
	}
	
	function find($dbObject, $params, $db)
	{	
		$database = DB::getInstance($db);
		
		$results = $database->find($dbObject, $count, $params['Options']);
		
		$objects = array();
		foreach($results as $result) {
			array_push($objects, $result->toArray());
		}
		
		return $objects;
	}
	
	function save($dbObject, $params, $db)
	{
		if ($dbObject->{$dbObject->getPrimaryKey()}) {
			return update($dbObject, $params, $db);
		} else {
			return insert($dbObject, $params, $db);
		}
	}
	
	function update($dbObject, $params, $db)
	{
		$id = DB::getInstance($db)->update($dbObject);
		return array($dbObject->getPrimaryKey() => $id);
	}
	
	function insert($dbObject, $params, $db)
	{
		$id = DB::getInstance($db)->insert($dbObject);
		return array($dbObject->getPrimaryKey() => $id);
	}
	
	function delete($dbObject, $params, $db)
	{
		DB::getInstance($db)->delete($dbObject);
		return array();
	}
	
	function updateGenreTags($dbObject, $params)
	{
		if (DBUtilities::updateGenreTags($dbObject, $params['names']))
			return array();
		else
			return array("error" => "Error updating genres");
	}
	
	function findSubGenres($dbObject, $params)
	{
		// Get query text
		global $uri;
		$dbObject->Name = $uri->getKey('q');
		
		// Find matches
		$catalog = DB::getInstance('MySql');
		$results = $catalog->find($dbObject, $count, array(
			"sortcolumn" => 'Name',
			"ascending" => true,
			"fuzzytextsearch" => true,
			"limit" => 50
		));
		
		$genres = array();
		foreach($results as $genre)
		{
			array_push($genres, $genre->getColumnValues());
		}
		
		// Add "Create New" option
		$create = '<img src="media/icon-small-add.png" width="12" height="12" class="ac_icon" /> Create New "';
		$create .= $dbObject->Name . '" Genre';
		array_push($genres, array('Name' => $create, 'create' => $dbObject->Name));

		return $genres;
	}
	
	function getExistingTags($dbObject, $params)
	{
		// Get tags
		$catalog = DB::getInstance('MySql');
		$genreNames = DBUtilities::getGenreNames();
		$tags = $catalog->find(new GenreTag(array('AlbumID' => $dbObject->AlbumID)), $count, array("limit" => 20));
		
		$genres = array();
		foreach($tags as $tag)
		{
			array_push($genres, $genreNames[$tag->GenreID]);
		}

		return $genres;
	}
	
	function doSingleQuery($method, $params, $db) {
		try {
			if (array_key_exists('Criteria', $params)) {
				$output = $method(new DBCriteria($params['Type'], $params['Criteria']), $params, $db);
			} else {
				$output = $method(new $params['Type']($params['Attributes']), $params, $db);
			}
			return $output;
		} catch (Exception $e) {
			return $output = array('error' => array(get_class($e) => $e->getMessage()));
		}
	}

	if ($uri->hasKey('queries') || ($uri->hasKey('method') && $uri->hasKey('params')))
	{
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . date('D, d M Y H:i:s') . 'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: text/x-json');
		
		$output = array();
		
		if ($uri->hasKey('queries')) {
			$queries = json_decode($uri->getKey('queries'), true);
			if (is_array($queries)) {
				foreach ($queries as $query) {
					array_push($output, doSingleQuery($query['method'], json_decode($query['params'], true), array_key_exists('db', $query) ? $query['db'] : 'MySql'));
				}
			}
		} else {
			$method = $uri->getKey('method');
			$params = json_decode($uri->getKey('params'), true);
			$db = $uri->hasKey('db') ? $uri->getKey('db') : 'MySql';
			$output = doSingleQuery($method, $params, $db);
		}
		
		$json = json_encode($output);
		
		echo ($uri->hasKey('callback') ? $uri->getKey('callback') . '(' . $json .')' : $json);
		
		exit();
	}
?>
