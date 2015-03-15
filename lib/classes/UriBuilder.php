<?php

################################################################################
# OBJECT:       UriBuilder                                                     #
# AUTHOR:       Bryan C. Callahan (6/8/2009)                                   #
# DESCRIPTION:  Manages URIs and includes utilities to make redirection        #
#               easier.                                                        #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2009/06/08 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

class UriBuilder
{

################################################################################
# MEMBER VARIABLES                                                             #

	protected $uri;			// Full URI
	protected $method;		// Request method of the
	protected $data;		// Data to manage (GET, POST)
	protected $useCurl;		// Whether or not to POST via Curl

#                                                                           [X]#
################################################################################
# PUBLIC FUNCTIONS                                                             #

	////////////////////////////////////////////////////////////////////////////
	// Default constructor...
	// Note: We can't use Curl inside of tools because Curl would need to
	//  automate login somehow.
	public function __construct($uri = '', $useCurl = False)
	{
		$this->uri = $this->getURI($uri);
		$this->useCurl = (bool)$useCurl;
		
		if ($uri == '')
		{
			$this->method = strtolower($_SERVER['REQUEST_METHOD']);

			if ($this->method == 'get') $this->data = &$_GET;
			elseif ($this->method == 'post') $this->data = &$_POST;
			else $this->data = array();
		}
		else
		{
			$this->method = 'get';
			$this->data = array();
		}
		
		// Fix all escaped single and double quotes (no escaping)...
		if (get_magic_quotes_gpc())
			$this->data = array_map('stripslashes', $this->data);
	}

	////////////////////////////////////////////////////////////////////////////
	// ToString (Human readable representation of this object)...
	public function __toString()
	{
		return trim($this->uri . '?' . $this->getQueryString(), '?');
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Simple accessors...
	public function getUriScheme() { return parse_url($this->uri, PHP_URL_SCHEME); }
	public function getUriHost() { return parse_url($this->uri, PHP_URL_HOST); }
	public function getUriPort() { return parse_url($this->uri, PHP_URL_PORT); }
	public function getUriUser() { return parse_url($this->uri, PHP_URL_USER); }
	public function getUriPass() { return parse_url($this->uri, PHP_URL_PASS); }
	public function getUriPath() { return parse_url($this->uri, PHP_URL_PATH); }
	public function getUriQuery() { return parse_url($this->uri, PHP_URL_QUERY); }
	public function getUriFragment() { return parse_url($this->uri, PHP_URL_FRAGMENT); }
	public function getMethod() { return $this->method; }
	public function getKeyAsBool($key) { return (bool)$this->getKey($key); }
	public function getKeyAsInt($key) { return intval($this->getKey($key)); }
	public function getKeyAsFloat($key) { return floatval($this->getKey($key)); }
	
	////////////////////////////////////////////////////////////////////////////
	// Simple mutators...
	public function setMethod($value) { $this->method = $value; }
	public function setUseCurl($value) { $this->useCurl = (bool)$value; }

	////////////////////////////////////////////////////////////////////////////
	// Determines if the key exists...
	public function hasKey($key)
	{
		return isset($this->data[$key]);
	}

	////////////////////////////////////////////////////////////////////////////
	// Get a key from the data set (isset prevents stupid notices)...
	public function getKey($key)
	{
		return $this->hasKey($key) ? trim($this->data[$key]) : '';
	}

	////////////////////////////////////////////////////////////////////////////
	// Updates key with value (if key doesn't exist, it's created)...
	public function updateKey($key, $value)
	{
		$this->data[$key] = trim($value);
	}

	////////////////////////////////////////////////////////////////////////////
	// Removes a key from the data set...
	public function removeKey($key)
	{
		unset($this->data[$key]);
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Removes all keys from the data set...
	public function clearKeys()
	{
		$this->data = array();
	}

	////////////////////////////////////////////////////////////////////////////
	// Redirects to the configured URI...
	public function redirect($method = '')
	{
		// Check if we have a custom redirect method...
		if ($method != '') $this->method = $method;
		
		// Redirect according to our method...
		if ($this->method == 'get') $this->redirectGet();
		elseif ($this->method == 'post') $this->redirectPost();
		else die('UriBuilder detected bad redirect method for "' . $this->uri . '"');
	}

#                                                                           [X]#
################################################################################
# PRIVATE FUNCTIONS                                                            #

	////////////////////////////////////////////////////////////////////////////
	// Get the fully qualified address (minus the query/fragment)...
	protected function getURI($uri = '')
	{
		if ($uri == '')
		{
			$scheme = (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
			$host = $_SERVER['HTTP_HOST'];
			$path = $_SERVER['PHP_SELF'];
		}
		else if (substr($uri, 0, 1) == '/')
		{
			$scheme = (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
			$host = $_SERVER['HTTP_HOST'];
			$path = $uri;
		}
		else if (parse_url($uri, PHP_URL_SCHEME) == '')
		{
			$scheme = (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
			$host = $_SERVER['HTTP_HOST'];
			$path = dirname($_SERVER['PHP_SELF']) . '/' . $uri;
		}
		else
		{
			$scheme = parse_url($uri, PHP_URL_SCHEME);
			$host = parse_url($uri, PHP_URL_HOST);
			$path = parse_url($uri, PHP_URL_PATH);
		}
		
		return $scheme . '://' . $host . $path;
	}

	////////////////////////////////////////////////////////////////////////////
	// Builds a query string from the data...
	protected function getQueryString()
	{
		return http_build_query($this->data);
	}

	////////////////////////////////////////////////////////////////////////////
	// Redirect to the current URI via Get...
	protected function redirectGet()
	{
		header('Location: ' . $this->__toString());
		exit();
	}

	////////////////////////////////////////////////////////////////////////////
	// Redirect to the current URI via POST...
	protected function redirectPost()
	{
		if ($this->useCurl)
			$this->redirectCurlPost();
		else
			$this->redirectJsPost();
	}

	////////////////////////////////////////////////////////////////////////////
	// Perform a post redirect via curl...
	protected function redirectCurlPost()
	{
		$ch = curl_init($this->uri);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getQueryString());
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_exec($ch);
		curl_close($ch);
	}

	////////////////////////////////////////////////////////////////////////////
	// Perform a post redirect via javascript...
	protected function redirectJsPost()
	{
		// Open up HTML document...
		$html = '<html><head><title>Redirecting...</title></head><body>';

		// Give the user some guidance as to what is going on...
		$html .= '<p style="font-family: sans-serif; font-size: 12px;"><i>' . "\n";
		$html .= 'If you are not automatically forwarded within <u>30 seconds</u> please ' . "\n";
		$html .= 'click the button below.' . "\n";
		$html .= '</i></p>' . "\n";

		// Create form header...
		$html .= '<form name="myform" action="' . $this->uri . '" method="POST">' . "\n";

		// Add form data...
		foreach ($this->data as $key => $value)
			$html .= '<textarea style="width: 10px; height: 10px; position: absolute; left: -2000px; visibility: hidden;" name="' . $key . '">' . $value . '</textarea>' . "\n";

		// Close out form...
		$html .= '<input type="submit" value="Processing..." />' . "\n";
		$html .= '</form>' . "\n";

		// Create javascript for redirect...
		$html .= '<script language="javascript">' . "\n";
		$html .= ' document.myform.submit();' . "\n";
		$html .= '</script>' . "\n\n";

		// Close out HTML document...
		$html .= '</body></html>';

		// Write the html and exit php processing for client redirect...
		echo $html;
		exit();
	}

#                                                                           [X]#
################################################################################

}

?>