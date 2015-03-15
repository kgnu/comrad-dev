<?php

################################################################################
# OBJECT:       RecurringEventMonthly                                          #
# AUTHOR:       Bryan C. Callahan (03/22/2010)                                 #
# DESCRIPTION:  Represents a monthly recurring event.                          #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2010/03/22 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

final class RecurringEventMonthly extends AbstractEventsConnector implements InterfaceModule
{

################################################################################
# PRIVATE MEMBER VARIABLES                                                     #

	private $ID;
	private $ParentInstanceID;
	private $StartDate;
	private $EndDate;
	private $EveryXMonths;
	private $RepeatBy;

#                                                                           [X]#
################################################################################
# PUBLIC FUNCTIONS                                                             #

	////////////////////////////////////////////////////////////////////////////
	// Default constructor...
	public function __construct($id = '', &$connection_owner = NULL)
	{
		// Reset private member variables...
		$this->unpopulate();

		// Call parent constructor to create the connection...
		parent::__construct($connection_owner);

		// Lock this object to a single item...
		$this->ID = $id;
	}

	////////////////////////////////////////////////////////////////////////////
	// ToString (Human readable representation of this object)...
	public function __toString()
	{
		return 'RecurringEventMonthly (' . $this->ID . ')';
	}
    
	////////////////////////////////////////////////////////////////////////////
	// Simple accessor functions...
	public function getID() { return $this->ID; }
	public function getParentInstanceID() { return $this->ParentInstanceID; }
	public function getStartDate() { return $this->StartDate; }
	public function getEndDate() { return $this->EndDate; }
	public function getEveryXMonths() { return $this->EveryXMonths; }
	public function getRepeatBy() { return $this->RepeatBy; }

	////////////////////////////////////////////////////////////////////////////
	// Simple mutator functions...
	public function setParentInstanceID($value) { $this->ParentInstanceID = (int) $value; }
	public function setStartDate($value) { $this->StartDate = is_numeric($value) ? $value : strtotime($value); }
	public function setEndDate($value) { $this->EndDate = is_numeric($value) ? $value : strtotime($value); }
	public function setEveryXMonths($value) { $this->EveryXMonths = (int) $value; }
	public function setRepeatBy($value) { $this->RepeatBy = $value; }

	////////////////////////////////////////////////////////////////////////////
	// populate(): Query database to populate member variables...
	public function populate()
	{
		// Prepare general query to extract all item info from database...
		$stmt = $this->db->prepare("SELECT * FROM `RecurringEventsMonthly` WHERE `ID` = ? LIMIT 1;");
		if (!$stmt) die("Could not populate '$this->ID' RecurringEventMonthly.");

		// Bind parameters and perform specific query...
		$stmt->bind_param('i', $this->ID);
		$stmt->execute();
		$stmt->store_result();
		
		// Determine if item exists (we must have precisely one)...
		$this->does_item_exist = ($stmt->num_rows == 1);
		if (!$this->does_item_exist)
		{
                        // The item doesn't exist so we're going to reset the value
                        //  (force an insert if user updates() later...
                        $this->ID = '';

                        // Doesn't exist, return false...
			return false;
		}

		// Bind results from the query...
		$stmt->bind_result($ID, $ParentInstanceID, $StartDate, $EndDate, 
			$EveryXMonths, $RepeatBy);

		// Populate the bound variables w/ data from the query...
		$stmt->fetch();
        
		// Clean up the statement...
		$stmt->close();

		// Maintain member variables with record data...
		$this->ID = $ID;
		$this->ParentInstanceID = $ParentInstanceID;
		$this->StartDate = strtotime($StartDate);
		$this->EndDate = strtotime($EndDate);
		$this->EveryXMonths = $EveryXMonths;
		$this->RepeatBy = $RepeatBy;

		// All done, success...
		return true;
	}

	////////////////////////////////////////////////////////////////////////////
	// unpopulate(): Unpopulates the member variables for this item...
	public function unpopulate()
	{
		$this->does_item_exist = false;
		$this->ID = '';
		$this->ParentInstanceID = '';
		$this->StartDate = '';
		$this->EndDate = '';
		$this->EveryXMonths = '';
		$this->RepeatBy = '';
	}

	////////////////////////////////////////////////////////////////////////////
	// update(): Saves all private member variables to database...
	// Note: This function updates if the item exists and adds if it doesn't.
	public function update()
	{
		// Prepare query...
		if ($this->does_item_exist)
		{
			$query = 'UPDATE `RecurringEventsMonthly` SET ';
			$query .= '`ParentInstanceID`=?, ';
			$query .= '`StartDate`=?, ';
			$query .= '`EndDate`=?, ';
			$query .= '`EveryXMonths`=?, ';
			$query .= '`RepeatBy`=? ';
			$query .= 'WHERE `ID` = ? LIMIT 1;';
		}
		else
		{
			$query = "INSERT INTO ";
			$query .= "`RecurringEventsMonthly` ";
			$query .= "(`ParentInstanceID`,`StartDate`,`EndDate`,";
			$query .= "`EveryXMonths`,`RepeatBy`,`ID`) ";
			$query .= "VALUES (?, ?, ?, ?, ?, ?);";
		}

		// Update or insert item information...
		$stmt = $this->db->prepare($query);
		if (!$stmt) die("Could not update '$this->ID' RecurringEventMonthly.");

		// Bind the parameters to the data...
		$stmt->bind_param('issisi', $ParentInstanceID, $StartDate, $EndDate,
			$EveryXMonths, $RepeatBy, $ID);
		
		$ID = $this->ID;
		$ParentInstanceID = $this->ParentInstanceID;
		$StartDate = date('Y-m-d', $this->StartDate);
		$EndDate = date('Y-m-d', $this->EndDate);
		$EveryXMonths = $this->EveryXMonths;
		$RepeatBy = $this->RepeatBy;

		// Updates or inserts item information...
		if (!$stmt->execute()) die("Could not update or insert '$this->ID' RecurringEventMonthly.");

		// Maintain the ID if we inserted...
		if (!is_numeric($this->ID)) $this->ID = $this->db->insert_id;

		// Clean up statement...
		$stmt->close();

		// At this point we can guarentee that the record exists...
		$this->does_item_exist = true;
	}

	////////////////////////////////////////////////////////////////////////////
	// remove(): Removes this item from the database...
	public function remove()
	{
		// Remove the member...
		$query = 'DELETE FROM `RecurringEventsMonthly` WHERE `ID` = ? LIMIT 1;';
		$stmt = $this->db->prepare($query);
		if (!$stmt) die("Could not remove '$this->ID' RecurringEventMonthly.");
		$stmt->bind_param('i', $this->ID);
		if (!$stmt->execute()) die("Could not remove '$this->ID' RecurringEventMonthly.");
		$stmt->close();
		$this->unpopulate();
	}

#                                                                           [X]#
################################################################################

}

?>
