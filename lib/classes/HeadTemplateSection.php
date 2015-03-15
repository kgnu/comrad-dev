<?php

################################################################################
# OBJECT:       HeadTemplateSection                                            #
# AUTHOR:       Bryan C. Callahan (12/01/2008)                                 #
# DESCRIPTION:  Represents the opening of the template head. Data appearing    #
#                below this template is written between <head></head> tags.    #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2008/12/01 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

final class HeadTemplateSection extends AbstractTemplateSection
{

################################################################################
# PRIVATE MEMBER VARIABLES                                                     #

	private $jsGoogleUseKey;		// Whether to use Google API key
	private $jsGoogleLoad;			// Array of google loads to be called
	private $jsGoogleCallBack;		// Array of google call backs

#                                                                           [X]#
################################################################################
# PUBLIC FUNCTIONS                                                             #

	////////////////////////////////////////////////////////////////////////////
	// Default constructor...
	public function __construct()
	{
		// Need to call the parent constructor if we are overwritting the
		//  constructor in a subclass...
		parent::__construct();

		$this->jsGoogleUseKey = true;
		$this->jsGoogleLoad = array();
		$this->jsGoogleCallBack = array();
	}

	////////////////////////////////////////////////////////////////////////////
	// Simple accessors...
	public function getJsGoogleUseKey() { return $this->jsGoogleCallBack; }

	////////////////////////////////////////////////////////////////////////////
	// Simple mutators...
	public function setJsGoogleUseKey($value) { $this->jsGoogleCallBack = (bool)$value; }

	////////////////////////////////////////////////////////////////////////////
	// Adds a javascript library to load via Google AJAX APIs...
	public function addJsGoogleLoad($moduleName, $moduleVersion, $optionalSettings = '')
	{
		$this->jsGoogleLoad[] = array($moduleName, $moduleVersion, $optionalSettings);
	}

	////////////////////////////////////////////////////////////////////////////
	// Adds a javascript function to google call back...
	public function addJsGoogleCallBack($functionName)
	{
		$this->jsGoogleCallBack[] = trim($functionName, ';');
	}

	////////////////////////////////////////////////////////////////////////////
	// Returns the HTML of this template section...
	public function getHTML()
	{
		global $init;

		$html = '
			<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
			<html>
			<head>

			<meta http-equiv="X-UA-Compatible" content="IE=8" />
			';

		if ($init->getProp('Admin_Url') != '')
		{
			$html .= '
				<base href="' . trim($init->getProp('Admin_Url'), '/') . '/" />

				<meta http-equiv="content-type" content="text/html; charset=utf-8" />
				<link rel="StyleSheet" href="' . $init->getProp('Admin_JQueryStyle') . '" type="text/css" />
				';
		}

		$html .= '
			<!-- This page is (C) Copyright 2009-' . date('Y') . ' by ' . $init->getProp('Organization_Name') .  ' -->
			<!-- Developed by Tom Buzbee, Bryan C. Callahan, Timothy Wilson St Charles, Eric Freesee, Stephanie Pitts -->
			
			<script type="text/javascript" src="js/jquery/jquery.js"></script>
			<script type="text/javascript" src="js/jquery/ui/jquery-ui.js"></script>
			';

		return $html;
	}

#                                                                           [X]#
################################################################################

}

?>
