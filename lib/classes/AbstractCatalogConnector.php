<?php

################################################################################
# OBJECT:       AbstractCatalogConnector                                       #
# AUTHOR:       Bryan C. Callahan (09/14/2008)                                 #
# DESCRIPTION:  Represents a database connector to the sql tables.             #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2008/09/14 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

abstract class AbstractCatalogConnector
{

################################################################################
# PRIVATE MEMBER VARIABLES                                                     #

	protected $db = NULL;						// Database handle
	protected $does_item_exist = false;			// Whether the user exists
	private $is_connected = false;				// Whether we successfully connected
	private $does_handle_connection = true;		// Whether we handle connect/disconnect

#                                                                           [X]#
################################################################################
# PUBLIC FUNCTIONS                                                             #

	////////////////////////////////////////////////////////////////////////////
	// Default constructor...
	public function __construct(&$connection_owner = NULL)
	{
		// We are using Initialize to gain access to configuration, make
		//  sure that the class has been defined...
		if (!class_exists('Initialize'))
			die('Class \'' . get_class($this) . '\' requires class \'Initialize\'.');

		// Connect to the global InitWebTools object...
		global $init;
		
		// Determine whether this object should handle the connection...
		$this->does_handle_connection = is_null($connection_owner);

		// Connect to database...
		if ($this->does_handle_connection)
			$this->db = new mysqli($init->getProp('Catalog_Host'),
				$init->getProp('Catalog_Username'),
				$init->getProp('Catalog_Password'),
				$init->getProp('Catalog_Database'));
		else
			$this->db = $connection_owner->getDatabase();

		// Check if we have a good connection...
		$this->is_connected = ($this->db->connect_errno == 0);
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Deconstructor...
	public function __destruct()
	{
		// Close database connection if we own it...
		if ($this->does_handle_connection && !is_null($this->db))
			$this->db->close();
	}

	////////////////////////////////////////////////////////////////////////////
	// Simple accessor functions...
	public function getDatabase() { return $this->db; }
	public function getIsConnected() { return $this->is_connected; }
	
#                                                                           [X]#
################################################################################

}

?>
