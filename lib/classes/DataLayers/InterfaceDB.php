<?php

################################################################################
# OBJECT:       InterfaceDB                                                    #
# AUTHOR:       Tom Buzbee (02/18/2010)                                        #
# DESCRIPTION:  Provides the exported interface to the database                #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2010/03/22 (BCC) - Changed the name to follow conventions
#   2010/02/18 (TB) - Created
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

interface InterfaceDB
{
	#                                                                           [X]#
	################################################################################
	# PUBLIC FUNCTIONS                                                             #

	public static function getInstance($dbName);
	public function get($dbObject);
	public function insert($dbObject);
	public function update($dbObject);
	public function delete($dbObject);
	public function find($dbObject = null, &$count = false, $options = array());

	#                                                                           [X]#
	################################################################################

}

?>
