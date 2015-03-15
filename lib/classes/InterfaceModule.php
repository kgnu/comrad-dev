<?php

################################################################################
# OBJECT:       InterfaceModule                                                #
# AUTHOR:       Bryan C. Callahan (12/01/2008)                                 #
# DESCRIPTION:  Represents a module which can contain database information.    #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2008/12/01 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################

interface InterfaceModule
{
	////////////////////////////////////////////////////////////////////////////
	// Populates item data to the module (if item exists in module, returns
	//  true otherwise false)...
	public function populate();

	////////////////////////////////////////////////////////////////////////////
	// Unpopulates item data from the module...
	public function unpopulate();

	////////////////////////////////////////////////////////////////////////////
	// Updates the item...
	public function update();

	////////////////////////////////////////////////////////////////////////////
	// Instantly removes an item without an update()...
	public function remove();
}

?>