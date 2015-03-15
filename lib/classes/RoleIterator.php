<?php

################################################################################
# OBJECT:       RoleIterator                                                   #
# AUTHOR:       Bryan C. Callahan (12/15/2009)                                 #
# DESCRIPTION:  Iterates through user roles.                                   #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2009/12/15 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

final class RoleIterator extends AbstractConnector implements InterfaceIterator
{

################################################################################
# PRIVATE MEMBER VARIABLES                                                     #

	private $db_query;		// Query to the database
	private $itemCount;		// Number of items in collection
	private $nextCount;		// Number of times next has been called

#                                                                           [X]#
################################################################################
# PUBLIC FUNCTIONS                                                             #

	////////////////////////////////////////////////////////////////////////////
	// Default constructor...
	public function __construct(&$connection_owner = NULL)
	{
		parent::__construct($connection_owner);

		$query = "SELECT `Name` FROM `Roles` ORDER BY `Name` ASC;";
		$this->db_query = $this->db->query($query);
		if (!$this->db_query) die('Cannot iterate through roles.');
		$this->itemCount = $this->db_query->num_rows;
		$this->nextCount = 0;
	}

	////////////////////////////////////////////////////////////////////////////
	// Default destruct...
	public function __destruct()
	{
		$this->db_query->close();
		
		parent::__destruct();
	}

	////////////////////////////////////////////////////////////////////////////
	// Returns whether or not there are more results...
	public function hasNext()
	{
		return ($this->nextCount < $this->itemCount);
	}

	////////////////////////////////////////////////////////////////////////////
	// Returns the number of items in the collection...
	public function getItemCount()
	{
		return $this->itemCount;
	}

	////////////////////////////////////////////////////////////////////////////
	// Returns the number of times we've taken a new item from the collection...
	public function getNextCount()
	{
		return $this->nextCount;
	}

	////////////////////////////////////////////////////////////////////////////
	// Gets the next item in the collection...
	public function getNext()
	{
		$this->nextCount++;
		$row = $this->db_query->fetch_object();
		$user = new Role($row->Name, $this);
		$user->populate();
		return $user;
	}

#                                                                           [X]#
################################################################################

}

?>
