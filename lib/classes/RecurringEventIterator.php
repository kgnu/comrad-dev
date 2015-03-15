<?php

################################################################################
# OBJECT:       RecurringEventIterator                                         #
# AUTHOR:       Bryan C. Callahan (01/20/2010)                                 #
# DESCRIPTION:  Iterates through the various recurring event schedules.        #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2010/01/20 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

final class RecurringEventIterator extends AbstractEventsConnector implements InterfaceIterator
{

################################################################################
# PRIVATE MEMBER VARIABLES                                                     #

	private $db_query;		// Query to the database
	private $itemCount;		// Number of items in collection
	private $nextCount;		// Number of times next has been called
	private $type;			// Type of recurring event (e.g. daily, weekly, etc)

#                                                                           [X]#
################################################################################
# PUBLIC FUNCTIONS                                                             #

	////////////////////////////////////////////////////////////////////////////
	// Default constructor...
	public function __construct($type, &$connection_owner = NULL)
	{
		parent::__construct($connection_owner);

		$this->type = $type;

		if ( ($this->type != 'Daily') && ($this->type != 'Weekly') &&
			($this->type != 'Monthly') && ($this->type != 'Yearly') )
			die('Cannot iterate through recurring events');

		$query = "SELECT `ID` FROM `RecurringEvents{$type}` ORDER BY `StartDate` ASC";
		$this->db_query = $this->db->query($query);
		if (!$this->db_query) die('Cannot iterate through recurring events.');
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

		// Load the event...
		switch($this->type)
		{
			case 'Daily':		$ret = new RecurringEventDaily($row->ID, $this); break;
			case 'Weekly':		$ret = new RecurringEventWeekly($row->ID, $this); break;
			case 'Monthly':		$ret = new RecurringEventMonthly($row->ID, $this); break;
			case 'Yearly':		$ret = new RecurringEventYearly($row->ID, $this); break;
		}

		// Populate the event and return it...
		$ret->populate();
		return $ret;
	}

#                                                                           [X]#
################################################################################

}

?>
