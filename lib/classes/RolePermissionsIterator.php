<?php

################################################################################
# OBJECT:       RolePermissionsIterator                                        #
# AUTHOR:       Bryan C. Callahan (12/17/2009)                                 #
# DESCRIPTION:  Iterates through permissions of a given role.                  #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2009/12/17 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

final class RolePermissionsIterator extends AbstractConnector implements InterfaceIterator
{

################################################################################
# PRIVATE MEMBER VARIABLES                                                     #

	private $permissions;	// Array of permission keys and values
	private $itemCount;		// Number of items in collection
	private $nextCount;		// Number of times next has been called

#                                                                           [X]#
################################################################################
# PUBLIC FUNCTIONS                                                             #

	////////////////////////////////////////////////////////////////////////////
	// Default constructor...
	public function __construct($role, &$connection_owner = NULL)
	{
		parent::__construct($connection_owner);

		$stmt = $this->db->prepare("SELECT `Permissions` FROM `Roles` WHERE `ID` = ? LIMIT 1;");
		if (!$stmt) die("Could not iterate through id:{$role->getID()} role permissions.");
		$stmt->bind_param('i', $role->getID());
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($Permissions);
		$stmt->fetch();
		$stmt->close();

		parse_str($Permissions, $this->permissions);
		$this->itemCount = count($this->permissions);
		$this->nextCount = 0;
	}

	////////////////////////////////////////////////////////////////////////////
	// Default destruct...
	public function __destruct()
	{
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
		$p = new Permission(current($this->permissions));
		$p->setName(key($this->permissions));
		next($this->permissions);
		$this->nextCount++;
		return $p;
	}

#                                                                           [X]#
################################################################################

}

?>
