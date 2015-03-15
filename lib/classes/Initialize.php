<?php

################################################################################
# CLASS:        Initialize                                                     #
# AUTHOR:       Bryan C. Callahan (05/31/2009)                                 #
# DESCRIPTION:  Represents the basic initialization for Comrad.                #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2009/05/31 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################
#                                                                              #
#                       --==  (DO NOT EDIT BELOW!)  ==--                       #
#                                                                              #
################################################################################
# Define Initalization Class...                                                #

final class Initialize
{
	private $config;
	private $version = 0;
	private $logPath;

	public static $this_dir = '';

	////////////////////////////////////////////////////////////////////////////
	// Constructions the initialization object...
	public function __construct($configFile = '')
	{
		// Set the default configuration file...
		if ($configFile == '') $configFile = dirname(__FILE__) . '/../config.php';

		// Prepare the Initialize object...
		require($configFile);
		$this->config = $config;
		@session_start();

		// Remember the configured default log path...
		$this->logPath = $this->getProp('Log_Admin');
	}

	////////////////////////////////////////////////////////////////////////////
	// Allow the logging path to be changed...
	public function setLogPath($path)
	{
		$this->logPath = $path;
	}

	////////////////////////////////////////////////////////////////////////////
	// Gets a Web Tools property from the configuration...
	// Note: Protect this function! It can get properties from the configuration
	//  which includes sensitive information (like database passwords)!
	public function getProp($key)
	{	
		switch ($key)
		{
			case 'Admin_Subversion_Revision':

				chdir(dirname(__FILE__) . '/../../');
				return intval(shell_exec('svnversion'));
				break;

			case 'Admin_Version':			return $this->version; break;
			case 'Admin_FullVersion':		return 'v' . number_format($this->version, 1); break;
			default: return (array_key_exists($key, $this->config) ? $this->config[$key] : null);
		}
	}

	////////////////////////////////////////////////////////////////////////////
	// Append log entry to log file...
	public function log($entry)
	{
		// Must have a log entry...
		if (trim($entry) == '') die('Nothing to log.');

		// If we don't have a log path, we're disabled...
		if ($this->logPath == '') return;

		// Configure entry...
		$ip = array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR'] : '---.---.---.---';
		$username = array_key_exists('Username', $_SESSION) ? $_SESSION['Username'] . ' - ': '';
		$file = ' (File: ' . $_SERVER['REQUEST_URI'] . ')';
		$entry = $ip . "\t[" . date('m/d/Y H:i:s') . "]\t" . $username . $entry . $file . "\n";

		// Make sure the file exists...
		if (!file_exists($this->logPath)) die('Could not find log file.');

		// Open the log file...
		$handle = fopen($this->logPath, 'a+');

		// Make sure we have a valid handle...
		if (!$handle) die('Could not open log file.');

		// Write log entry...
		fwrite($handle, $entry);

		// Close handle...
		fclose($handle);
	}

	////////////////////////////////////////////////////////////////////////////
	// Logs the message and ends execution...
	public function abort($message, $isSilent = false)
	{
		$this->log($message);
		if ($isSilent) $message = '';
		die($message);
	}

	////////////////////////////////////////////////////////////////////////////
	// Locks down the page to valid Active code...
	public function lockDown()
	{
		// See this link for some suggestions on session management:
		//  http://stackoverflow.com/questions/520237/how-do-i-expire-a-php-session-after-30-minutes
		
		// Check if this is an ajax request
		$isXHR = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
		
		// Derive the web tools sign in page URL from host root...
		$signin_uri = parse_url($this->getProp('Admin_Url'), PHP_URL_PATH) . '/index.php';

		// Make sure that we have a proper signin_uri...
		if (trim($signin_uri) == '') die('Malformatted login URL detected.');

		// If we are not at the sign in page, we need to validate the Active code
		//  we are carrying around with our session...
		if ($_SERVER['PHP_SELF'] != $signin_uri) {
			try {
				$this->checkSessionExpired();
				$this->checkClientNotAuthenticated();
			
				$_SESSION['LastActive'] = time(); // Keep track of the last time that the user was active
			} catch (Exception $e) {
				$this->destroySession();
				if ($isXHR) {
					if ($e instanceof ClientNotAuthenticatedException) {
						header('HTTP/1.0 401 Not Authenticated');
					} else if ($e instanceof SessionExpiredException) {
						header('HTTP/1.0 408 Session Expired');
					}
				} else {
					$_SESSION['JumpBack'] = $_SERVER['REQUEST_URI'];
					header('Location: ' . $this->getProp('Admin_Url') . '/index.php');
				}
				exit;
			}
		}
	}
	
	private function destroySession() {
		// Destroy session and redirect to login page
		session_unset();
		session_destroy();
		session_start();
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Throw an exception if the client is not authenticated
	public function checkClientNotAuthenticated() {
		if (!isset($_SESSION['Active']) || !$this->isActiveCodeValid($_SESSION['Active'])) {
			throw new ClientNotAuthenticatedException();
		}
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Throw an exception if the user's session is expired
	public function checkSessionExpired() {
		if (!isset($_SESSION['LastActive']) || time() - $_SESSION['LastActive'] > $this->getProp('Session_LifetimeSeconds')) {
			throw new SessionExpiredException();
		}
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Returns whether provided Active code is valid...
	public function isActiveCodeValid($code)
	{
		return ( $code == $this->genActiveCode() );
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Generates session Active code...
	public function genActiveCode()
	{
		return md5((array_key_exists('Username', $_SESSION) ? $_SESSION['Username'] : '') . $_SERVER['REMOTE_ADDR'] . $this->getProp('Secret_SessionActiveKey'));
	}

	////////////////////////////////////////////////////////////////////////////
	// Determines if we're running via secure protocol (https)...
	public function isHttps()
	{
		return isset($_SERVER['HTTPS']);
	}

	////////////////////////////////////////////////////////////////////////////
	// Set the autoload for classes properly...
	public function setAutoload()
	{
		spl_autoload_register(array('Initialize', 'autoload'));
	}

	////////////////////////////////////////////////////////////////////////////
	// This string converts a string of characters to their html ascii 
	//  equivalent. Instead of PHP's htmlentities() this function converts each 
	//  character, even normal ones, to ascii code...
	// Example: "bryan" => "&#98;&#114;&#121;&#97;&#110;"
	public function asciiHtmlEntities($string)
	{
		$result = '';
		$strlen = strlen($string);

		for ($i = 0; $i < $strlen; $i++)
			$result .= '&#' . ord(substr($string, $i, 1)) . ';';

		return $result;
	}

	////////////////////////////////////////////////////////////////////////////
	// Asserts that the condition is true, if it isn't, redirect to denied...
	public function assertPermission($condition)
	{
		if (!$condition)
		{
			$filename = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/')+1);
			$jumpUri = new UriBuilder('denied.php');
			$jumpUri->updateKey('from', $filename);
			$jumpUri->redirect();
		}
	}

	////////////////////////////////////////////////////////////////////////////
	// Autoloads unspecific objects (globally registered later)...
	public static function autoload($name)
	{
		// Path relative to Initialize.php
		$path = '.'.
			':DataTypes'.
			':DataTypes/ColumnSet'.
			':DataTypes/ColumnSet/Event'.
			':DataTypes/ColumnValidators'.
			':DataTypes/Event'.
			':DataTypes/FloatingShowElements'.
			':DataTypes/Permissions'.
			':DataTypes/ScheduledEvent'.
			':DataTypes/ScheduledEventInstance'.
			':DataTypes/TimeInfo'.
			':DataLayers'.
			':Exceptions'.
			':Manager';

		// Search the path
		foreach (explode(":", $path) as $dir)
		{
			$filename = dirname(__FILE__) . "/" . $dir . "/" . $name . ".php";

			if (file_exists($filename))
			{
				@include_once($filename);
				if (class_exists($name) || interface_exists($name)) return;
			}
		}

		die("Could not load class/interface '$name'\n");
	}
}

#                                                                          [X] #
################################################################################

?>
