<?php

################################################################################
# OBJECT:       CloseTemplateSection                                           #
# AUTHOR:       Bryan C. Callahan (12/01/2008)                                 #
# DESCRIPTION:  Represents the closing of the template body. Data appearing    #
#                above this template is written before the </body> tag.        #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2008/12/01 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

final class CloseTemplateSection extends AbstractTemplateSection
{

################################################################################
# PRIVATE MEMBER VARIABLES                                                     #

	private $pathContents;			// Path to external contents page
	private $showFooterNav;			// Whether to show footer navigation

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

		$this->pathContents = 'contents.php';
		$this->showFooterNav = true;
	}

	////////////////////////////////////////////////////////////////////////////
	// Simple accessors...
	public function getPathContents() { return $this->pathContents; }
	public function getShowFooterNav() { return $this->showFooterNav; }

	////////////////////////////////////////////////////////////////////////////
	// Simple mutators...
	public function setPathContents($value) { $this->pathContents = $value; }
	public function setShowFooterNav($value) { $this->showFooterNav = (bool)$value; }

	////////////////////////////////////////////////////////////////////////////
	// getHTML(): Returns the HTML of this template section...
	public function getHTML()
	{
		global $init;
		
		$html = '
				<div id="Head_Content_Push"></div>
			</div>
			<!-- / Head - Content -->

			</div>
			<!-- / Head -->

			<!-- Foot -->
			<div style="clear: both"></div>
			<div id="Foot">
			';

		if ($this->showFooterNav)
		{
			$html .= '
				<!-- Foot - Nav -->
				<div id="Foot_Nav">
				<!--<a href="javascript: history.go(-1);">..back</a>&nbsp;&nbsp;
				<a href="' . $this->pathContents . '">..contents</a>-->
				</div>
				<!-- / Foot - Nav -->
				';
		}

		$html .= '
				<!-- Foot - Credits -->
				<div id="Foot_Credits">
				<!--comrad v' . $init->getProp('Admin_Version') . '.' . $init->getProp('Admin_Subversion_Revision') . ' beta-->
				</div>
				<!-- / Foot - Credits -->

			</div>
			<!-- / Foot -->

			</body>
			</html>
			';

		return $html;
	}

#                                                                           [X]#
################################################################################

}

?>
