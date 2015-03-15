<?php
################################################################################
# OBJECT:       AWSStoreFrontSimple                                            #
# AUTHOR:       Wil St. Charles (2/13/2010)                                    #
# DESCRIPTION:  Interface to Amazon Web Services Storefront                    #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2/13/10 (STC) - Initial revision					       #
#   4/19/10 (BCC) - Simplified things down (removed our containers)
#                                                                              #
################################################################################
#                                                                              #
#   			   --==  IMPLEMENTATION   ==--                         #
#                                                                              #
################################################################################

final class AWSStoreFrontSimple
{	
################################################################################
# PRIVATE MEMBER VARIABLES                                                     #

	private $publicKey;
	private $privateKey;
	private $operation;
	private $version;
	private $searchIndex;
	private $results;
	
#                                                                              #
################################################################################
# PUBLIC FUNCTIONS                                                             #

	////////////////////////////////////////////////////////////////////////
	// Default constructor...
	public function __construct($publicKey = '', $privateKey = '')
	{
		global $init;

		// Pull the keys from the config if we weren't passed specifics...
		if ($publicKey == '') $publicKey = $init->getProp('AmazonPublicKey');
		if ($privateKey == '') $privateKey = $init->getProp('AmazonPrivateKey');

		// Initalize the structure...
		$this->publicKey = $publicKey;
		$this->privateKey = $privateKey;
		$this->operation = 'ItemSearch';
		$this->version = '2009-03-31';
		$this->searchIndex = 'Music';
		$this->results = array();
	}

	////////////////////////////////////////////////////////////////////////
	public function getTracks() { return $this->results; }
	public function getAlbumTitles() { return array_keys($this->results); }
	
	////////////////////////////////////////////////////////////////////////
	// Query the Amazone database give keywords...
	public function query($keywords = '')
	{
		$host = 'ecs.amazonaws.com';
		$uri = '/onca/xml';

		// Create the list of parameters (remember to sort the list by key!)...
		// Note: List of things to search by: 'Keywords','Title','Power',
		//  'BrowseNode','Artist','Author','Actor','Director','AudienceRating',
		//  'Manufacturer','MusicLabel','Composer','Publisher','Brand',
		//  'Conductor','Orchestra','TextStream','Cuisine','City','Neighborhood'
		$params = array(
			'AWSAccessKeyId' => $this->publicKey,
			//'Artist' => 'Owl City',
			'Keywords' => $keywords, 
			'Operation' => $this->operation,
			'ResponseGroup' => 'Large',		// Tracks
			'SearchIndex' => $this->searchIndex,
			'Service' => 'AWSECommerceService',
			'Timestamp' => gmdate("Y-m-d\TH:i:s\Z"),
			'Version' => $this->version
			);

		// Params need to be sorted as in ksort(), but we should just list 
		//  them in order (we know what order they'll be in) so we can save 
		//  some cycles. It's slow enough. :(
		//ksort($params);

		// We can't use http_build_query because it doesn't format things 
		//  EXACTLY as super picky AWS wants...
		$cleanParams = array();
		foreach ($params as $key => $value)
		{
			$key = rawurlencode(rawurldecode($key));
			$value = str_replace('+', '%20', $value);
			$value = rawurlencode(rawurldecode($value));
			$cleanParams[] = "$key=$value";
		}
		$cleanParams = implode('&', $cleanParams);

		// AWS want's us to validate our request so we need to do some 
		//  signing (w/ HMAC SHA256 in base64)...
		$request = "GET\n$host\n$uri\n$cleanParams";
		$signature = base64_encode(hash_hmac('sha256', $request, $this->privateKey, true));

		// Create the request url and query AWS...
		$url = "http://$host$uri?$cleanParams&Signature=$signature";
		$response = @file_get_contents($url);

		// If we couldn't connect, bail (AWS is SLOOOOOOOOOOOW)...
		if ($response === false) return false;

		// Load the parse-o-matic (if problem parsing bail)...
		$doc = new DOMDocument();
		if (!$doc->loadXML($response)) return false;

		// Extra the data from the AWS results...
		$items = $doc->getElementsByTagName('Item');
		foreach ($items as $item)
		{
			$itemAttributes = $item->getElementsByTagName('ItemAttributes');
			$album = $itemAttributes->item(0)->getElementsByTagName('Title');
			$albumTitle = $album->item(0)->nodeValue;

			$this->results[$albumTitle] = array();

			$tracksCollection = $item->getElementsByTagName('Tracks');

			// If we don't have any tracks this is likely not a CD, skip...
			if (!$tracksCollection->item(0)) continue;

			$discs = $tracksCollection->item(0)->getElementsByTagName('Disc');
			foreach ($discs as $disc)
			{
				$tracks = $disc->getElementsByTagName('Track');
				foreach ($tracks as $track)
				{
					$trackNumber = $track->getAttribute('Number');
					$trackTitle = $track->nodeValue;

					$this->results[$albumTitle][$trackNumber] = $trackTitle;
				}
			}
		}

		return true;
	}
	
#                                                                              #
################################################################################

}
?>
