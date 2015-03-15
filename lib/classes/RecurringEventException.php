<?php

################################################################################
# OBJECT:       RecurringEventException                                        #
# AUTHOR:       Bryan C. Callahan (03/22/2010)                                 #
# DESCRIPTION:  Represents a exception recurring event.                        #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2010/03/22 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

final class RecurringEventException extends AbstractEventsConnector implements InterfaceModule
{

################################################################################
# PRIVATE MEMBER VARIABLES                                                     #

	private $ID;
	private $RecurringID;
	private $RecurringType;
	private $StartDateTime;
	private $EndDateTime;

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
		return 'RecurringEventException (' . $this->ID . ')';
	}
    
	////////////////////////////////////////////////////////////////////////////
	// Simple accessor functions...
	public function getID() { return $this->ID; }
	public function getRecurringID() { return $this->RecurringID; }
	public function getRecurringType() { return $this->RecurringType; }
	public function getStartDateTime() { return $this->StartDateTime; }
	public function getEndDateTime() { return $this->EndDateTime; }

	////////////////////////////////////////////////////////////////////////////
	// Simple mutator functions...
	public function setRecurringID($value) { $this->RecurringID = $value; }
	public function setRecurringType($value) { $this->RecurringType = $value; }
	public function setStartDateTime($value) { $this->StartDateTime = is_numeric($value) ? $value : strtotime($value); }
	public function setEndDateTime($value) { $this->EndDateTime = is_numeric($value) ? $value : strtotime($value); }

	////////////////////////////////////////////////////////////////////////////
	// populate(): Query database to populate member variables...
	public function populate()
	{
		// Prepare general query to extract all item info from database...
		$stmt = $this->db->prepare("SELECT * FROM `RecurringEventsException` WHERE `ID` = ? LIMIT 1;");
		if (!$stmt) die("Could not populate '$this->ID' RecurringEventException.");

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
		$stmt->bind_result($ID, $RecurringID, $RecurringType, $StartDateTime, $EndDateTime);

		// Populate the bound variables w/ data from the query...
		$stmt->fetch();
        
		// Clean up the statement...
		$stmt->close();

		// Maintain member variables with record data...
		$this->ID = $ID;
		$this->RecurringID = $RecurringID;
		$this->RecurringType = $RecurringType;
		$this->StartDateTime = strtotime($StartDateTime);
		$this->EndDateTime = strtotime($EndDateTime);

		// All done, success...
		return true;
	}

	////////////////////////////////////////////////////////////////////////////
	// unpopulate(): Unpopulates the member variables for this item...
	public function unpopulate()
	{
		$this->does_item_exist = false;
		$this->ID = '';
		$this->RecurringID = '';
		$this->RecurringType = '';
		$this->StartDateTime = '';
		$this->EndDateTime = '';
	}

	////////////////////////////////////////////////////////////////////////////
	// update(): Saves all private member variables to database...
	// Note: This function updates if the item exists and adds if it doesn't.
	public function update()
	{
		// Prepare query...
		if ($this->does_item_exist)
		{
			$query = 'UPDATE `RecurringEventsException` SET ';
			$query .= '`RecurringID`=?, ';
			$query .= '`RecurringType`=?, ';
			$query .= '`StartDateTime`=?, ';
			$query .= '`EndDateTime`=? ';
			$query .= 'WHERE `ID` = ? LIMIT 1;';
		}
		else
		{
			$query = "INSERT INTO ";
			$query .= "`RecurringEventsException` ";
			$query .= "(`RecurringID`,`RecurringType`,`StartDateTime`,`EndDateTime`,`ID`) ";
			$query .= "VALUES (?, ?, ?, ?, ?);";
		}

		// Update or insert item information...
		$stmt = $this->db->prepare($query);
		if (!$stmt) die("Could not update '$this->ID' RecurringEventException.");

		// Bind the parameters to the data...
		$stmt->bind_param('isssi', $RecurringID, $RecurringType, $StartDateTime,
			$EndDateTime, $ID);
		
		$ID = $this->ID;
		$RecurringID = $this->RecurringID;
		$RecurringType = $this->RecurringType;
		$StartDateTime = date('Y-m-d H:i:s', $this->StartDateTime);
		$EndDateTime = date('Y-m-d H:i:s', $this->EndDateTime);

		// Updates or inserts item information...
		if (!$stmt->execute()) die("Could not update or insert '$this->ID' RecurringEventException.");

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
		$query = 'DELETE FROM `RecurringEventsException` WHERE `ID` = ? LIMIT 1;';
		$stmt = $this->db->prepare($query);
		if (!$stmt) die("Could not remove '$this->ID' RecurringEventException.");
		$stmt->bind_param('i', $this->ID);
		if (!$stmt->execute()) die("Could not remove '$this->ID' RecurringEventException.");
		$stmt->close();
		$this->unpopulate();
	}

	////////////////////////////////////////////////////////////////////////////
	public static function doesExceptionExist($recurringID, $recurringType, $startDateTime, $endDateTime)
	{
		// Format the start and end datetimes...
		if (is_numeric($startDateTime)) $startDateTime = date('Y-m-d H:i:s', $startDateTime);
		if (is_numeric($endDateTime)) $endDateTime = date('Y-m-d H:i:s', $endDateTime);

		// Set up the query...
		$query = 'SELECT * FROM `RecurringEventsException` WHERE ';
		$query .= '`RecurringID` = ? AND ';
		$query .= '`RecurringType` = ? AND ';
		$query .= '`StartDateTime` = ? AND ';
		$query .= '`EndDateTime` = ? ';
		$query .= 'LIMIT 1;';

		// Create an exception instance to hook the database...
		$db = new RecurringEventException();

		// Query the database to see if it exists...
		$stmt = $db->getDatabase()->prepare($query);
		if (!$stmt) die("Could not determine if RecurringEventException exists.");
		$stmt->bind_param('isss', $recurringID, $recurringType, $startDateTime, $endDateTime);
		if (!$stmt->execute()) die("Could not determine if RecurringEventException exists.");
		$stmt->store_result();
		$nRows = $stmt->num_rows;
		$stmt->close();

		// Return whether it exists...
		return ($nRows == 1);
	}

	////////////////////////////////////////////////////////////////////////////
	public static function generateExceptionString($recurringID, $recurringType)
	{
		// Create an exception instance to hook the database...
		$db = new RecurringEventException();

		// Clean up params...
		$recurringID = (int) $recurringID;
		$recurringType = $db->getDatabase()->real_escape_string($recurringType);

		// Set up the query...
		$query = 'SELECT * FROM `RecurringEventsException` WHERE ';
		$query .= '`RecurringID` = ' . $recurringID . ' AND ';
		$query .= '`RecurringType` = \'' . $recurringType . '\' ';
		$query .= ';';

		// Execute the query...
		$dbQuery = $db->getDatabase()->query($query);
		if (!$dbQuery) die('Could not generate exception string.');
		$nRows = $dbQuery->num_rows;

		// Loop through the results...
		$ret = '';
		for ($i = 0; $i < $nRows; $i++)
		{
			$row = $dbQuery->fetch_object();
			$ret .= date('n/j/Y', strtotime($row->StartDateTime)) . ',';
		}
		$ret = trim($ret, ',');

		return $ret;
	}

	////////////////////////////////////////////////////////////////////////////
	public function removeAllExceptions($recurringID, $recurringType)
	{
		// Create the query to nuke all recurringID / recurringType combos...
		$query = 'DELETE FROM `RecurringEventsException` WHERE ';
		$query .= '`RecurringID` = ? AND `RecurringType` = ?;';

		// Create an exception instance to hook the database...
		$db = new RecurringEventException();

		// Prepare the query...
		$stmt = $db->getDatabase()->prepare($query);
		if (!$stmt) die("Could not remove RecurringEventExceptions.");
		$stmt->bind_param('is', $recurringID, $recurringType);
		if (!$stmt->execute()) die("Could not remove all RecurringEventExceptions.");
		$stmt->close();

		// All good...
		return true;
	}

#                                                                           [X]#
################################################################################

}

?>
