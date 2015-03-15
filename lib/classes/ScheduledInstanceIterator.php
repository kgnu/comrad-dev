<?php

################################################################################
# OBJECT:       ScheduledInstanceIterator                                      #
# AUTHOR:       Bryan C. Callahan (02/08/2010)                                 #
# DESCRIPTION:  Iterates through all of the scheduled event instances.         #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2010/02/08 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

final class ScheduledInstanceIterator extends AbstractEventsConnector implements InterfaceIterator
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
	public function __construct($start = NULL, $end = NULL, $offset = -1, $limit = -1, 
	$sortBy = '', $sortDirection = '', $showParents = false, &$connection_owner = NULL)
	{
		parent::__construct($connection_owner);

		// Make sure offset and limit are valid ints (prevent injections)...
		$offset = (int) $offset;
		$limit = (int) $limit;
		if (!is_null($start)) $start = (int) $start;
		if (!is_null($end)) $end = (int) $end;
		$sortBy = $this->db->real_escape_string($sortBy);
		if ($sortBy == '') $sortBy = 'StartDateTime';
		$sortDirection = $this->db->real_escape_string($sortDirection);
		if ($sortDirection == '') $sortDirection = 'ASC';

		// Setup query...
		$query = 'SELECT `ID` FROM `ScheduledInstances` ';

		// Determine what range of instances to return...
		if (is_null($start))
			$query .= " WHERE `StartDateTime` >= '" . date('Y-m-d H:i:s') . "'";
		else
			$query .= " WHERE `StartDateTime` >= '" . date('Y-m-d H:i:s', $start) . "' AND `StartDateTime` < '" . date('Y-m-d H:i:s', $end) . "'";

		// Can we return parents?...
		if (!$showParents)
			$query .= " AND `Parent` != 1";

		// Finish up query...
		//$query .= ' GROUP BY `EventType`,`StartDateTime`,`Duration`';
		$query .= ' ORDER BY `' . $sortBy . '` ' . $sortDirection;
		if ( ($offset >= 0) && ($limit > 0) ) $query .= " LIMIT $offset,$limit";
		$query .= ';';

		// Snag the data...
		$this->db_query = $this->db->query($query);
		if (!$this->db_query) die('Cannot iterate through scheduled events.');
		$this->itemCount = $this->db_query->num_rows;
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
		$this->nextCount++;
		$row = $this->db_query->fetch_object();
		$event = new ScheduledInstance($row->ID, $this);
		$event->populate();
		return $event;
	}

#                                                                           [X]#
################################################################################

}

?>
