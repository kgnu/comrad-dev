<?php

################################################################################
# OBJECT:       BodyTemplateSection                                            #
# AUTHOR:       Bryan C. Callahan (12/01/2008)                                 #
# DESCRIPTION:  Represents the opening of the template body. Data appearing    #
#                below this template is written between <body></body> tags.    #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2008/12/01 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

final class BodyTemplateSection extends AbstractTemplateSection
{

################################################################################
# PRIVATE MEMBER VARIABLES                                                     #

	private $doesModifyCSS;			// Whether to modify css sheet

	private $pathPageIcon;			// Path to page icon image
	private $pathContents;			// Path to external contents page
	private $footHeight;			// Height of footer (pixels)
	private $showBackground;		// Whether to show background image
	private $showSessionTools;		// Whether to show session tools
	private $showHeaderNav;			// Whether to show header navigation
	private $showSignOut;			// Whether to show sign out

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

		$this->doesModifyCSS = false;

		$this->pathPageIcon = '';
		$this->pathContents = 'contents.php';
		$this->footHeight = -1;
		$this->showBackground = true;
		$this->showSessionTools = true;
		$this->showHeaderNav = true;
		$this->showSignOut = true;
	}
    
	////////////////////////////////////////////////////////////////////////////
	// Simple accessors...
	public function getPathPageIcon() { return $this->pathPageIcon; }
	public function getPathContents() { return $this->pathContents; }
	public function getFootHeight() { return $this->footHeight; }
	public function getShowBackground() { return $this->showBackground; }
	public function getShowSessionTools() { return $this->$showSessionTools; }
	public function getShowHeaderNav() { return $this->$showHeaderNav; }
	public function getShowSignOut() { return $this->showSignOut; }

	////////////////////////////////////////////////////////////////////////////
	// Simple mutators...
	public function setPathPageIcon($value) { $this->pathPageIcon = (string)$value; $this->doesModifyCSS = true; }
	public function setPathContents($value) { $this->pathContents = $value; }
	public function setFootHeight($value) { $this->footHeight = intval($value); $this->doesModifyCSS = true; }
	public function setShowBackground($value) { $this->showBackground = (bool)$value; $this->doesModifyCSS = true; }
	public function setShowSessionTools($value) { $this->showSessionTools = (bool)$value; }
	public function setShowHeaderNav($value) { $this->showHeaderNav = (bool)$value; }
	public function setShowSignOut($value) { $this->showSignOut = (bool)$value; }

	////////////////////////////////////////////////////////////////////////////
	// getHTML(): Returns the HTML of this template section...
	public function getHTML()
	{
		global $init;
		
		$html = '';

		if ($this->doesModifyCSS)
		{
			$html .= '
				<style type="text/css">
				';

			if ($this->pathPageIcon != '')
			{
				$html .= '
					#Head #Head_Content {
						background-image: url("' . $this->pathPageIcon . '");
						background-repeat: no-repeat;
						background-position: top right;
						padding-right: 160px;
						min-height: 160px;
					}
					';
			}

			if ($this->footHeight >= 0)
			{
				$html .= '
					#Head {	margin: 0px auto -' . $this->footHeight . 'px;	}
					#Head #Head_Content_Push { height: ' . $this->footHeight . 'px; }
					#Foot { height: ' . ($this->footHeight-1) . 'px; }
					';
			}

			if (!$this->showBackground)
			{
				$html .= '
					body {
						background-image: none;
					}
					';
			}

			$html .= '
				</style>
				';
		}

		$html .= '
			<link rel="StyleSheet" href="css/default.css" type="text/css" />
			<link rel="StyleSheet" href="css/rte_global.css" type="text/css" />

			</head>
			<body>

			<!-- Head -->
			<div id="Head">

			<!-- Head - Session Tools -->
			<div id="Head_SessionTools">
			';

		if ($this->showSessionTools)
		{
			if ($this->showHeaderNav)
			{
				$html .= '
					<span>
					<!--<a href="javascript:history.go(-1);">..back</a>&nbsp;&nbsp;-->
					<a href="' . $this->pathContents . '">Main Menu</a><br />
					</span>
					';
			}
            
			$html .= '
				Signed In As: <tt>' . $_SESSION['Username'] . '</tt>
				';

			if ($this->showSignOut)
			{
				$html .= '
					&nbsp;&nbsp;&#149;&nbsp;&nbsp;&nbsp;<a href="index.php?signout=1">Sign Out</a>
					';
			}
		}

		$html .= '
			</div>
			<!-- / Head - Session Tools -->

			<!-- Head - Content -->
			<div id="Head_Content">
			';

		return $html;
	}

#                                                                           [X]#
################################################################################

}

?>
