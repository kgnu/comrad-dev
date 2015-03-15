<?php

################################################################################
# FILENAME:     initializeWebTools.php                                         #
# AUTHOR:       Bryan C. Callahan (05/31/2009)                                 #
# DESCRIPTION:  Performs the basic initialization steps for Web Tools.         #
#               1. Defines initialization class                                #
#               2. Prepares autoload functionality                             #
#               3. Prepares initialization object                              #
#                                                                              #
# IMPORTANT:    This script MUST be required at the beginning of any and ALL   #
#                web tool pages for security! Even AJAX requests!              #
#                                                                              #
# NOTE:         This script cannot make any output. If it does, we cannot      #
#                change the header later.                                      #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2009/05/31 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################
#                                                                              #
#                       --==  (DO NOT EDIT BELOW!)  ==--                       #
#                                                                              #
################################################################################

	// Require the initialize class...
	require_once(dirname(__FILE__) . '/classes/Initialize.php');

	// Define the initialize object...
	$init = new Initialize();
	$init->setAutoload();
	if (!isset($disableAuthentication) || !$disableAuthentication) $init->lockDown();

	// Prepare the UriBuilder...
	$uri = new UriBuilder();

	// Since we don't have PHP 5.2 or greater and we NEED to have the 
	//  json_encode() function we'll implement it here...
	if (!function_exists('json_encode'))
	{
		function json_encode($a=false)
		{
			if (is_null($a)) return 'null';
			if ($a === false) return 'false';
			if ($a === true) return 'true';

			if (is_scalar($a))
			{
				if (is_float($a))
				{
				// Always use "." for floats.
				return floatval(str_replace(",", ".", strval($a)));
				}

				if (is_string($a))
				{
					static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), 
						array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
					return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
				}
				else
					return $a;
			}

			$isList = true;
			for ($i = 0, reset($a); $i < count($a); $i++, next($a))
			{
				if (key($a) !== $i)
				{
					$isList = false;
					break;
				}
			}

			$result = array();
			if ($isList)
			{
				foreach ($a as $v) $result[] = json_encode($v);
				return '[' . join(',', $result) . ']';
			}
			else
			{
				foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
				return '{' . join(',', $result) . '}';
			}
		}
	}
	
	// Since we don't have PHP 5.2 or greater and we NEED to have the 
	//  json_decode() function we'll implement it here...
	if (!function_exists('json_decode')) { 
		function json_decode($json) {
			// Author: walidator.info 2009
			$comment = false;
			$out = '$x=';

			for ($i=0; $i<strlen($json); $i++) {
				if (!$comment) {
					if ($json[$i] == '{' || $json[$i] == '[') {
						$out .= ' array(';
					} else if ($json[$i] == '}' || $json[$i] == ']') {
						$out .= ')';
					} else if ($json[$i] == ':') {
						$out .= '=>';
					} else {
						$out .= $json[$i];
					}
				} else {
					$out .= $json[$i];
				}
				
				if ($json[$i] == '"')    $comment = !$comment;
			}
			eval($out . ';');
			return $x;
		}
	}
?>
