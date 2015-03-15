<?php

################################################################################
# OBJECT:       UserIterator                                                   #
# AUTHOR:       Bryan C. Callahan (12/01/2008)                                 #
# DESCRIPTION:  Iterates through the Web Tools users.                          #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2008/12/01 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

final class UserIterator extends AbstractConnector implements InterfaceIterator
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

		$query = "SELECT `Username` FROM `Users` ORDER BY `Username` ASC;";
		$this->db_query = $this->db->query($query);
		if (!$this->db_query) die('Cannot iterate through users.');
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
		$user = new User($row->Username, $this);
		$user->populate();
		return $user;
	}

#                                                                           [X]#
################################################################################

}

?>
