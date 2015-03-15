<?php

################################################################################
# OBJECT:       AbstractTemplateSection                                        #
# AUTHOR:       Bryan C. Callahan (12/01/2008)                                 #
# DESCRIPTION:  Represents a template section.                                 #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2008/12/01 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

abstract class AbstractTemplateSection
{

################################################################################
# PUBLIC FUNCTIONS                                                             #

	////////////////////////////////////////////////////////////////////////////
	// Default constructor...
	public function __construct()
	{
		// We are using Initialize to gain access to configuration, make
		//  sure that the class has been defined...
		if (!class_exists('Initialize'))
			die('Class \'' . get_class($this) . '\' requires class \'Initialize\'.');
	}

	////////////////////////////////////////////////////////////////////////////
	// ToString (Human readable representation of this object)...
	public function __toString() { return get_class($this); }

	////////////////////////////////////////////////////////////////////////////
	// Writes the HTML output of this template section...
	public function write() { echo $this->getHTML(); }

	////////////////////////////////////////////////////////////////////////////
	// Returns the HTML of this template section...
	abstract public function getHTML();

#                                                                           [X]#
################################################################################

}

?>
