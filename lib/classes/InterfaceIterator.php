<?php

################################################################################
# OBJECT:       InterfaceIterator                                              #
# AUTHOR:       Bryan C. Callahan (12/01/2008)                                 #
# DESCRIPTION:  Represents a simple iterator to navigate through collections.  #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2008/12/01 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################

interface InterfaceIterator
{
	////////////////////////////////////////////////////////////////////////////
	// Returns whether or not there are more results...
	public function hasNext();
    
	////////////////////////////////////////////////////////////////////////////
	// Returns the number of items in the collection...
	public function getItemCount();

	////////////////////////////////////////////////////////////////////////////
	// Returns the number of times we've taken a new item from the collection...
	public function getNextCount();

	////////////////////////////////////////////////////////////////////////////
	// Gets the next item in the collection...
	public function getNext();
}

?>