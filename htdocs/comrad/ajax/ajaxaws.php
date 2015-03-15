<?php
	require_once('initialize.php');
		
	function JSONPrepare($results)
	{
		$pairList = array();
		foreach($results as $pair)
		{
			$newPair = array();
			$newPair['album'] = $pair['album']->getColumnValues();
			$newTracks = array();
			foreach($pair['tracks'] as $track)
			{
				$newTracks[] = $track->getColumnValues();
			}
			$newPair['tracks'] = $newTracks;
			$pairList[] = $newPair;
		}
		
		return $pairList;
	}
	
	
	$AWS = new AWSStorefront($init->getProp('AmazonPublicKey'), $init->getProp('AmazonPrivateKey'));
	
	if ($uri->hasKey('cmd')) switch ($uri->getKey('cmd'))
	{
		case 'ajaxArtistName':
		case 'ajaxAlbumName':
			$JSON = json_decode($uri->getKey('JSON'));
			$albumInfo = $JSON->attributes;
			//$jsonString = $uri->getKey('JSON');
			//echo "JSON string: $jsonString\n";
			//print_r($albumInfo);
			if(empty($albumInfo))
			{
				echo "an error occured\n";
			}
			else
			{
				$results = $AWS->queryArtistAlbum($albumInfo->Artist, $albumInfo->Title);
				//echo "Artist: $albumInfo->Artist, Album: $albumInfo->Title\n";
    			echo json_encode(JSONPrepare($results));
			}
			exit();
			
		case 'ajaxLabelName':
			$JSON = json_decode($uri->getKey('JSON'));
			$albumInfo = $JSON->attributes;
			//$jsonString = $uri->getKey('JSON');
			//echo "JSON string: $jsonString\n";
			//print_r($albumInfo);
			if(empty($albumInfo))
			{
				echo "an error occured\n";
			}
			else
			{
				$results = $AWS->queryKeywords($albumInfo->Label . " " . $albumInfo->Title . " " . $albumInfo->Artist);
				//echo "Artist: $albumInfo->Artist, Album: $albumInfo->Title\n";
    			echo json_encode(JSONPrepare($results));
			}
			exit();
			
		case 'ajaxTrackName':				
			$JSON = json_decode($uri->getKey('JSON'));
			$trackInfo = $JSON->attributes;
			
			$catalog = DB::getInstance('MySql');
			$results = $catalog->find(new Album(array(
				'AlbumID' => $trackInfo->AlbumID,
			)));
			if(empty($trackInfo))
			{
				echo "an error occured\n";
			}
			else if(empty($results))
			{
				echo "no album associated with this AlbumID (did you select an album?)";
			}
			else
			{
				$albums = $AWS->queryArtistAlbum($results[0]->Artist, $results[0]->Title);
				$results = array();
				foreach ($albums as $album)
				{
					foreach ($album['tracks'] as $track)
					{
						$results[] = $track->getColumnValues();
					}
				}
				echo json_encode($results);
			}
			exit();
			
		case 'ajaxSingleTrack':
			$JSON = json_decode($uri->getKey('JSON'));
			$trackInfo = $JSON->attributes;
			//$jsonString = $uri->getKey('JSON');
			//echo "JSON string: $jsonString\n";
			if(empty($trackInfo))
			{
				echo "an error occured\n";
			}
			else
			{
				$results = $AWS->queryTrack($trackInfo->Title);
    			echo json_encode(JSONPrepare($results));
			}
			exit();
	
	}
?>