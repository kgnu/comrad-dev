<?php
################################################################################
# OBJECT:       AWSStorefront                                                  #
# AUTHOR:       Wil St. Charles (2/13/2010)                                    #
# DESCRIPTION:  Interface to Amazon Web Services Storefront                    #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2/13/10 (STC) - Initial revision					       #
#                                                                              #
################################################################################
#                                                                              #
#   			   --==  IMPLEMENTATION   ==--                         #
#                                                                              #
################################################################################
#require_once(dirname(__FILE__) . '/Datatypes/Album.php');
#require_once(dirname(__FILE__) . '/Datatypes/Track.php');
	
class AWSStorefront
{	
################################################################################
# PRIVATE MEMBER VARIABLES                                                     #

	private $publicKey;
	private $privateKey;
	private $operation;
	private $version;
	private $searchIndex;
	
#                                                                              #
################################################################################
# PUBLIC FUNCTIONS                                                             #

	////////////////////////////////////////////////////////////////////////////
	// Default constructor...
	public function __construct($publicKey, $privateKey)
	{
		$this->publicKey = $publicKey;
		$this->privateKey = $privateKey;
		$this->setRequestParameters();
	}
	
	public function setRequestParameters($operation = 'ItemSearch', $version = '2009-03-31', $searchIndex = 'Music')
	{
		$this->operation = $operation;
		$this->version = $version;
		$this->searchIndex = $searchIndex;
	}
	
	public function queryArtistAlbum($artist, $album)
	{
		$dict = array();
		if(!empty($artist))
		{
			$artist = str_replace(" ", "+", $artist);
			$dict["Artist"] = $artist;
		}
		if(!empty($album))
		{
			$album = str_replace(" ", "+", $album);
			$dict["Title"] = $album;
		}
		
		$this->setRequestParameters();
		return $this->query($dict);
	}
	
	public function queryKeywords($keywords)
	{
		$dict = array();
		if(!empty($keywords))
		{
			$keywords = str_replace(" ", "+", $keywords);
			$dict["Keywords"] = $keywords;
		}
		
		$this->setRequestParameters();
		return $this->query($dict);
	}

	public function queryTrack($track)
	{
		$dict = array();
		if(!empty($track))
		{
			$track = str_replace(" ", "+", $track);
			$dict["Keywords"] = $track;
		}

		$this->setRequestParameters('ItemSearch', '2009-03-31', 'MusicTracks');
	 	return $this->query($dict);
	}

#                                                                              #
################################################################################
# PRIVATE FUNCTIONS                                                            #

	// http://www.money-code.com/2009/08/amazon-product-api-signaturedoesnotmatch-error-response/
	private function query($termDictionary)
	{
		if(empty($termDictionary))
		{
			#TODO: Log Failure
			return array();
		}
		
	    $method = "GET";
	    $host = "ecs.amazonaws.com";
	    $uri = "/onca/xml";
	    
	    // additional parameters
	    $termDictionary["Service"] = "AWSECommerceService";
	    $termDictionary["AWSAccessKeyId"] = $this->publicKey;
	    // GMT timestamp
	    $termDictionary["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z");
	    // What operation we're doing
	    $termDictionary["Operation"] = $this->operation;
	    // API version
	    $termDictionary["Version"] = $this->version;
	    // Where we're searching
	    $termDictionary["SearchIndex"] = $this->searchIndex;
	    // How we want amazon to return our results
	    $termDictionary["ResponseGroup"] = "Large";
	    
	    // sort the parameters
	    ksort($termDictionary);
	    
	    // create the canonicalized query
	    $canonicalQuery = array();
	    foreach($termDictionary as $key => $value)
	    {
	    	$key = rawurlencode(rawurldecode($key));	    	
	        $value = str_replace("+", "%20", $value);
	    	$value = rawurlencode(rawurldecode($value));
	        	        
	        $canonicalQuery[] = $key."=".$value;
	    }
	    
	    $canonicalQuery = implode("&", $canonicalQuery);
	    
	    // create the string to sign
	    $stringToSign = $method."\n".$host."\n".$uri."\n".$canonicalQuery;
	    
	    // calculate HMAC with SHA256 and base64-encoding
	    $signature = base64_encode(hash_hmac("sha256", $stringToSign, $this->privateKey, True));
	    
	    // encode the signature for the request
	    $signature = rawurlencode($signature);
	    
	    // create request
	    $request = "http://".$host.$uri."?".$canonicalQuery."&Signature=".$signature;
		
	    //print("Request: $request\n");
	    
		#Catch the response in the $response object
		$response = file_get_contents($request);
		if(!$response) #if the response is empty, then don't bother parsing it
		{
			#TODO: Log Failure
			return array();
		}
		
		return $this->parseXML($response);
	}
	
	private function parseXML($response)
	{
		try
		{
			#error checking to make sure our request wasn't bad
			$simpleXMLObj = new SimpleXMLElement($response);
			if(isset($simpleXMLObj->OperationRequest->Errors->Error))
			{
				foreach($simpleXMLObj->OperationRequest->Errors->Error as $error)
				{
				   #TODO: Log "Error code: " . $error->Code . "\r\n";
				   #TODO: Log $error->Message . "\r\n";
				}
				return array();
			}
									
			//now on to processing
			$numOfItems = $simpleXMLObj->Items->TotalResults;
			if($numOfItems == '0')
			{
				//no items returned by our seach
				return array();
			}
			
			
			$albums = array();
			//go through our items
			if(isset($simpleXMLObj->Items->Item))
			{
				foreach($simpleXMLObj->Items->Item as $curItem)
				{
					$curAlbum = new Album(array( #TODO: have to figure out how to convert a genre string to genreID
					  'Title' => $curItem->ItemAttributes->Title,
					  'Label' => $curItem->ItemAttributes->Label,
					  'Artist' => $curItem->ItemAttributes->Artist,
					  'AddDate' => time()
					));
					$curTrackArray = array();
					
					if(isset($curItem->Tracks->Disc))
					{
						foreach($curItem->Tracks->Disc as $curDisc)
						{
							if(isset($curDisc->Track))
							{
								foreach($curDisc->Track as $curTrack)
								{
								  // Needs to be updated for new Model
									$curTrackArray[] = new Track(array(
									  'Title' => $curTrack,
									  'TrackNumber' => $curTrack['Number'],
									  'DiskNumber' => $curDisc['Number'],
									  'Artist' => $curItem->ItemAttributes->Artist
									));
								}
							}
						}
					}
					$albums[] = array("album" => $curAlbum, "tracks" => $curTrackArray);
				}
			}
			
			return $albums;
		}
		catch(Exception $e)
		{
			#TODO: Log XML Parsing failure
			return array();
		}
	}
	
#                                                                              #
################################################################################
}

?>
