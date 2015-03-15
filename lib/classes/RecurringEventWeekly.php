<?php

################################################################################
# OBJECT:       RecurringEventWeekly                                           #
# AUTHOR:       Bryan C. Callahan (03/22/2010)                                 #
# DESCRIPTION:  Represents a weekly recurring event.                           #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2010/03/22 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

final class RecurringEventWeekly extends AbstractEventsConnector implements InterfaceModule
{

################################################################################
# PRIVATE MEMBER VARIABLES                                                     #

	private $ID;
	private $ParentInstanceID;
	private $StartDate;
	private $EndDate;
	private $EveryXWeeks;
	private $Sunday;
	private $Monday;
	private $Tuesday;
	private $Wednesday;
	private $Thursday;
	private $Friday;
	private $Saturday;

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
		return 'RecurringEventWeekly (' . $this->ID . ')';
	}
    
	////////////////////////////////////////////////////////////////////////////
	// Simple accessor functions...
	public function getID() { return $this->ID; }
	public function getParentInstanceID() { return $this->ParentInstanceID; }
	public function getStartDate() { return $this->StartDate; }
	public function getEndDate() { return $this->EndDate; }
	public function getEveryXWeeks() { return $this->EveryXWeeks; }
	public function getIsSunday() { return $this->Sunday; }
	public function getIsMonday() { return $this->Monday; }
	public function getIsTuesday() { return $this->Tuesday; }
	public function getIsWednesday() { return $this->Wednesday; }
	public function getIsThursday() { return $this->Thursday; }
	public function getIsFriday() { return $this->Friday; }
	public function getIsSaturday() { return $this->Saturday; }

	////////////////////////////////////////////////////////////////////////////
	// Simple mutator functions...
	public function setParentInstanceID($value) { $this->ParentInstanceID = (int) $value; }
	public function setStartDate($value) { $this->StartDate = is_numeric($value) ? $value : strtotime($value); }
	public function setEndDate($value) { $this->EndDate = is_numeric($value) ? $value : strtotime($value); }
	public function setEveryXWeeks($value) { $this->EveryXWeeks = (int) $value; }
	public function setIsSunday($value) { $this->Sunday = (bool) $value; }
	public function setIsMonday($value) { $this->Monday = (bool) $value; }
	public function setIsTuesday($value) { $this->Tuesday = (bool) $value; }
	public function setIsWednesday($value) { $this->Wednesday = (bool) $value; }
	public function setIsThursday($value) { $this->Thursday = (bool) $value; }
	public function setIsFriday($value) { $this->Friday = (bool) $value; }
	public function setIsSaturday($value) { $this->Saturday = (bool) $value; }

	////////////////////////////////////////////////////////////////////////////
	// populate(): Query database to populate member variables...
	public function populate()
	{
		// Prepare general query to extract all item info from database...
		$stmt = $this->db->prepare("SELECT * FROM `RecurringEventsWeekly` WHERE `ID` = ? LIMIT 1;");
		if (!$stmt) die("Could not populate '$this->ID' RecurringEventWeekly.");

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
		$stmt->bind_result($ID, $ParentInstanceID, $StartDate, $EndDate, $EveryXWeeks, 
			$Sunday, $Monday, $Tuesday, $Wednesday, $Thursday, $Friday, $Saturday);

		// Populate the bound variables w/ data from the query...
		$stmt->fetch();
        
		// Clean up the statement...
		$stmt->close();

		// Maintain member variables with record data...
		$this->ID = $ID;
		$this->ParentInstanceID = $ParentInstanceID;
		$this->StartDate = strtotime($StartDate);
		$this->EndDate = strtotime($EndDate);
		$this->EveryXWeeks = $EveryXWeeks;
		$this->Sunday = (bool) $Sunday;
		$this->Monday = (bool) $Monday;
		$this->Tuesday = (bool) $Tuesday;
		$this->Wednesday = (bool) $Wednesday;
		$this->Thursday = (bool) $Thursday;
		$this->Friday = (bool) $Friday;
		$this->Saturday = (bool) $Saturday;

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
		$this->EveryXWeeks = '';
		$this->Sunday = false;
		$this->Monday = false;
		$this->Tuesday = false;
		$this->Wednesday = false;
		$this->Thursday = false;
		$this->Friday = false;
		$this->Saturday = false;
	}

	////////////////////////////////////////////////////////////////////////////
	// update(): Saves all private member variables to database...
	// Note: This function updates if the item exists and adds if it doesn't.
	public function update()
	{
		// Prepare query...
		if ($this->does_item_exist)
		{
			$query = 'UPDATE `RecurringEventsWeekly` SET ';
			$query .= '`ParentInstanceID`=?, ';
			$query .= '`StartDate`=?, ';
			$query .= '`EndDate`=?, ';
			$query .= '`EveryXWeeks`=?, ';
			$query .= '`Sunday`=?, ';
			$query .= '`Monday`=?, ';
			$query .= '`Tuesday`=?, ';
			$query .= '`Wednesday`=?, ';
			$query .= '`Thursday`=?, ';
			$query .= '`Friday`=?, ';
			$query .= '`Saturday`=? ';
			$query .= 'WHERE `ID` = ? LIMIT 1;';
		}
		else
		{
			$query = "INSERT INTO ";
			$query .= "`RecurringEventsWeekly` ";
			$query .= "(`ParentInstanceID`,`StartDate`,`EndDate`,`EveryXWeeks`,";
			$query .= "`Sunday`,`Monday`,`Tuesday`,`Wednesday`,`Thursday`,`Friday`,";
			$query .= "`Saturday`,`ID`) ";
			$query .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
		}

		// Update or insert item information...
		$stmt = $this->db->prepare($query);
		if (!$stmt) die("Could not update '$this->ID' RecurringEventWeekly.");

		// Bind the parameters to the data...
		$stmt->bind_param('issiiiiiiiii', $ParentInstanceID, $StartDate, $EndDate, $EveryXWeeks, 
			$Sunday, $Monday, $Tuesday, $Wednesday, $Thursday, $Friday, $Saturday, $ID);
		
		$ID = $this->ID;
		$ParentInstanceID = $this->ParentInstanceID;
		$StartDate = date('Y-m-d', $this->StartDate);
		$EndDate = date('Y-m-d', $this->EndDate);
		$EveryXWeeks = $this->EveryXWeeks;
		$Sunday = $this->Sunday ? 1 : 0;
		$Monday = $this->Monday ? 1 : 0;
		$Tuesday = $this->Tuesday ? 1 : 0;
		$Wednesday = $this->Wednesday ? 1 : 0;
		$Thursday = $this->Thursday ? 1 : 0;
		$Friday = $this->Friday ? 1 : 0;
		$Saturday = $this->Saturday ? 1 : 0;

		// Updates or inserts item information...
		if (!$stmt->execute()) die("Could not update or insert '$this->ID' RecurringEventWeekly.");

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
		$query = 'DELETE FROM `RecurringEventsWeekly` WHERE `ID` = ? LIMIT 1;';
		$stmt = $this->db->prepare($query);
		if (!$stmt) die("Could not remove '$this->ID' RecurringEventWeekly.");
		$stmt->bind_param('i', $this->ID);
		if (!$stmt->execute()) die("Could not remove '$this->ID' RecurringEventWeekly.");
		$stmt->close();
		$this->unpopulate();
	}

        ///////////////////////////////////////////////////////////////////////////
        // getWeeklyCode(): Gets the weekly code...
        public function getWeeklyCode()
        {
                $ret = ($this->Sunday) ? 'S' : '-';
                $ret .= ($this->Monday) ? 'M' : '-';
                $ret .= ($this->Tuesday) ? 'T' : '-';
                $ret .= ($this->Wednesday) ? 'W' : '-';
                $ret .= ($this->Thursday) ? 'T' : '-';
                $ret .= ($this->Friday) ? 'F' : '-';
                $ret .= ($this->Saturday) ? 'S' : '-';
                return $ret;
        }

#                                                                           [X]#
################################################################################

}

?>
