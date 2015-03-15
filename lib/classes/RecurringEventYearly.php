<?php

################################################################################
# OBJECT:       RecurringEventYearly                                           #
# AUTHOR:       Bryan C. Callahan (03/22/2010)                                 #
# DESCRIPTION:  Represents a yearly recurring event.                           #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2010/03/22 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

final class RecurringEventYearly extends AbstractEventsConnector implements InterfaceModule
{

################################################################################
# PRIVATE MEMBER VARIABLES                                                     #

	private $ID;
	private $ParentInstanceID;
	private $StartDate;
	private $EndDate;
	private $EveryXYears;

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
		return 'RecurringEventYearly (' . $this->ID . ')';
	}
    
	////////////////////////////////////////////////////////////////////////////
	// Simple accessor functions...
	public function getID() { return $this->ID; }
	public function getParentInstanceID() { return $this->ParentInstanceID; }
	public function getStartDate() { return $this->StartDate; }
	public function getEndDate() { return $this->EndDate; }
	public function getEveryXYears() { return $this->EveryXYears; }

	////////////////////////////////////////////////////////////////////////////
	// Simple mutator functions...
	public function setParentInstanceID($value) { $this->ParentInstanceID = (int) $value; }
	public function setStartDate($value) { $this->StartDate = is_numeric($value) ? $value : strtotime($value); }
	public function setEndDate($value) { $this->EndDate = is_numeric($value) ? $value : strtotime($value); }
	public function setEveryXYears($value) { $this->EveryXYears = (int) $value; }

	////////////////////////////////////////////////////////////////////////////
	// populate(): Query database to populate member variables...
	public function populate()
	{
		// Prepare general query to extract all item info from database...
		$stmt = $this->db->prepare("SELECT * FROM `RecurringEventsYearly` WHERE `ID` = ? LIMIT 1;");
		if (!$stmt) die("Could not populate '$this->ID' RecurringEventYearly.");

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
		$stmt->bind_result($ID, $ParentInstanceID, $StartDate, $EndDate, $EveryXYears);

		// Populate the bound variables w/ data from the query...
		$stmt->fetch();
        
		// Clean up the statement...
		$stmt->close();

		// Maintain member variables with record data...
		$this->ID = $ID;
		$this->ParentInstanceID = $ParentInstanceID;
		$this->StartDate = strtotime($StartDate);
		$this->EndDate = strtotime($EndDate);
		$this->EveryXYears = $EveryXYears;

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
		$this->EveryXYears = '';
	}

	////////////////////////////////////////////////////////////////////////////
	// update(): Saves all private member variables to database...
	// Note: This function updates if the item exists and adds if it doesn't.
	public function update()
	{
		// Prepare query...
		if ($this->does_item_exist)
		{
			$query = 'UPDATE `RecurringEventsYearly` SET ';
			$query .= '`ParentInstanceID`=?, ';
			$query .= '`StartDate`=?, ';
			$query .= '`EndDate`=?, ';
			$query .= '`EveryXYears`=? ';
			$query .= 'WHERE `ID` = ? LIMIT 1;';
		}
		else
		{
			$query = "INSERT INTO ";
			$query .= "`RecurringEventsYearly` ";
			$query .= "(`ParentInstanceID`,`StartDate`,`EndDate`,`EveryXYears`,`ID`) ";
			$query .= "VALUES (?, ?, ?, ?, ?);";
		}

		// Update or insert item information...
		$stmt = $this->db->prepare($query);
		if (!$stmt) die("Could not update '$this->ID' RecurringEventYearly.");

		// Bind the parameters to the data...
		$stmt->bind_param('issii', $ParentInstanceID, $StartDate, $EndDate, $EveryXYears, $ID);
		
		$ID = $this->ID;
		$ParentInstanceID = $this->ParentInstanceID;
		$StartDate = date('Y-m-d', $this->StartDate);
		$EndDate = date('Y-m-d', $this->EndDate);
		$EveryXYears = $this->EveryXYears;

		// Updates or inserts item information...
		if (!$stmt->execute()) die("Could not update or insert '$this->ID' RecurringEventYearly.");

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
		$query = 'DELETE FROM `RecurringEventsYearly` WHERE `ID` = ? LIMIT 1;';
		$stmt = $this->db->prepare($query);
		if (!$stmt) die("Could not remove '$this->ID' RecurringEventYearly.");
		$stmt->bind_param('i', $this->ID);
		if (!$stmt->execute()) die("Could not remove '$this->ID' RecurringEventYearly.");
		$stmt->close();
		$this->unpopulate();
	}

#                                                                           [X]#
################################################################################

}

?>
