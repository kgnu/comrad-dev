<?php

################################################################################
# OBJECT:       ScheduledInstance                                              #
# AUTHOR:       Bryan C. Callahan (03/04/2010)                                 #
# DESCRIPTION:  Represents an item in the ScheduledInstances table.            #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2010/03/04 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

final class ScheduledInstance extends AbstractEventsConnector implements InterfaceModule
{

################################################################################
# PRIVATE MEMBER VARIABLES                                                     #

	private $ID;
	private $Username;
	private $EventID;
	private $EventType;
	private $RecurringID;
	private $RecurringType;
	private $StartDateTime;
	private $Duration;
	private $Description;
	private $Scheduled;
	private $SafeToRebuild;		// Whether the event can be recreated
	private $Parent;		// Whether the event is a parent event

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
		return 'ScheduledInstance (' . $this->ID . ')';
	}
    
	////////////////////////////////////////////////////////////////////////////
	// Simple accessor functions...
	public function getID() { return $this->ID; }
	public function getUsername() { return $this->Username; }
	public function getEventID() { return $this->EventID; }
	public function getEventType() { return $this->EventType; }
	public function getRecurringID() { return $this->RecurringID; }
	public function getRecurringType() { return $this->RecurringType; }
	public function getStartDateTime() { return $this->StartDateTime; }
	public function getDuration() { return $this->Duration; }
	public function getDescription() { return $this->Description; }
	public function getIsScheduled() { return $this->Scheduled; }
	public function getIsSafeToRebuild() { return $this->SafeToRebuild; }
	public function getIsParent() { return $this->Parent; }

	////////////////////////////////////////////////////////////////////////////
	// Simple mutator functions...
	public function setUsername($value) { $this->Username = $value; }
	public function setEventID($value) { $this->EventID = (int) $value; }
	public function setEventType($value) { $this->EventType = $value; }
	public function setRecurringID($value) { $this->RecurringID = (int) $value; }
	public function setRecurringType($value) { $this->RecurringType = $value; }
	public function setStartDateTime($value) { $this->StartDateTime = is_numeric($value) ? $value : strtotime($value); }
	public function setDuration($value) { $this->Duration = (int) $value; }
	public function setDescription($value) { $this->Description = $value; }
	public function setIsScheduled($value) { $this->Scheduled = (bool) $value; }
	public function setIsSafeToRebuild($value) { $this->SafeToRebuild = (bool) $value; }
	public function setIsParent($value) { $this->Parent = (bool) $value; }

	////////////////////////////////////////////////////////////////////////////
	// populate(): Query database to populate member variables...
	public function populate()
	{
		// Prepare general query to extract all item info from database...
		$stmt = $this->db->prepare("SELECT * FROM `ScheduledInstances` WHERE `ID` = ? LIMIT 1;");
		if (!$stmt) die("Could not populate '$this->ID' ScheduledInstance.");

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
		$stmt->bind_result($ID, $Username, $EventID, $EventType, $RecurringID, 
			$RecurringType, $StartDateTime, $Duration, $Description, $Scheduled,
			$SafeToRebuild, $Parent);

		// Populate the bound variables w/ data from the query...
		$stmt->fetch();
        
		// Clean up the statement...
		$stmt->close();

		// Maintain member variables with record data...
		$this->ID = $ID;
		$this->Username = $Username;
		$this->EventID = $EventID;
		$this->EventType = $EventType;
		$this->RecurringID = $RecurringID;
		$this->RecurringType = $RecurringType;
		$this->StartDateTime = strtotime($StartDateTime);
		$this->Duration = $Duration;
		$this->Description = $Description;
		$this->Scheduled = (bool) $Scheduled;
		$this->SafeToRebuild = (bool) $SafeToRebuild;
		$this->Parent = (bool) $Parent;

		// All done, success...
		return true;
	}

	////////////////////////////////////////////////////////////////////////////
	// unpopulate(): Unpopulates the member variables for this item...
	public function unpopulate()
	{
		$this->does_item_exist = false;
		$this->ID = '';
		$this->Username = '';
		$this->EventID = '';
		$this->EventType = '';
		$this->RecurringID = '';
		$this->RecurringType = '';
		$this->StartDateTime= '';
		$this->Duration = '';
		$this->Description = '';
		$this->Scheduled = '';
		$this->SafeToRebuild = '';
		$this->Parent = '';
	}

	////////////////////////////////////////////////////////////////////////////
	// update(): Saves all private member variables to database...
	// Note: This function updates if the item exists and adds if it doesn't.
	public function update()
	{
		// Prepare query...
		if ($this->does_item_exist)
		{
			$query = 'UPDATE `ScheduledInstances` SET ';
			$query .= '`Username`=?, ';
			$query .= '`EventID`=?, ';
			$query .= '`EventType`=?, ';
			$query .= '`RecurringID`=?, ';
			$query .= '`RecurringType`=?, ';
			$query .= '`StartDateTime`=?, ';
			$query .= '`Duration`=?, ';
			$query .= '`Description`=?, ';
			$query .= '`Scheduled`=?, ';
			$query .= '`SafeToRebuild`=?, ';
			$query .= '`Parent`=? ';
			$query .= 'WHERE `ID` = ? LIMIT 1;';
		}
		else
		{
			$query = "INSERT INTO ";
			$query .= "`ScheduledInstances` ";
			$query .= "(`Username`,`EventID`,`EventType`,`RecurringID`,`RecurringType`,";
			$query .= "`StartDateTime`,`Duration`,`Description`,`Scheduled`,`SafeToRebuild`,`Parent`,`ID`) ";
			$query .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
		}

		// Every ScheduledInstance requires an EventID and EventType in order 
		//  to point to the correct instance. If we don't have one, log it...
		if ($this->EventID == '' || $this->EventType == '')
		{
			global $init;
			$id = $this->EventID;
			$type = $this->EventType;
			$init->log("ScheduledInstance was created with an empty EventID and/or EventType (id: $id, type: $type) in " . $_SERVER['PHP_SELF']);
		}

		// Update or insert item information...
		$stmt = $this->db->prepare($query);
		if (!$stmt) die("Could not update '$this->ID' ScheduledInstances.");

		// Bind the parameters to the data...
		$stmt->bind_param('sisissisiiii', $Username, $EventID, $EventType,
			$RecurringID, $RecurringType, $StartDateTime, $Duration,
			$Description, $Scheduled, $SafeToRebuild, $Parent, $ID);

		$ID = $this->ID;
		$Username = $this->Username;
		$EventID = $this->EventID;
		$EventType = $this->EventType;
		$RecurringID = $this->RecurringID;
		$RecurringType = $this->RecurringType;
		$StartDateTime = @date('Y-m-d H:i:s', $this->StartDateTime);
		$Duration = $this->Duration;
		$Description = $this->Description;
		$Scheduled = $this->Scheduled ? 1 : 0;
		$SafeToRebuild = $this->SafeToRebuild ? 1 : 0;
		$Parent = $this->Parent ? 1 : 0;

		// Updates or inserts item information...
		if (!$stmt->execute()) die("Could not update or insert '$this->ID' ScheduledInstances.");

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
		$query = 'DELETE FROM `ScheduledInstances` WHERE `ID` = ? LIMIT 1;';
		$stmt = $this->db->prepare($query);
		if (!$stmt) die("Could not remove '$this->ID' ScheduledInstance.");
		$stmt->bind_param('i', $this->ID);
		if (!$stmt->execute()) die("Could not remove '$this->ID' ScheduledInstance.");
		$stmt->close();
		$this->unpopulate();
	}

	////////////////////////////////////////////////////////////////////////////
	// getRecurringEvent(): Gets the recurring event which created this instance,
	//  if it doesn't have one, return null...
	public function getRecurringEvent()
	{
		if ( ($this->RecurringID == 0) && ($this->RecurringType == '') ) return null;

		switch ($this->RecurringType)
		{
			case 'daily': $event = new RecurringEventDaily($this->RecurringID); break;
			case 'weekly': $event = new RecurringEventWeekly($this->RecurringID); break;
			case 'monthly': $event = new RecurringEventMonthly($this->RecurringID); break;
			case 'yearly': $event = new RecurringEventYearly($this->RecurringID); break;
			default: return null;
		}
		$event->populate();

		return $event;
	}

	////////////////////////////////////////////////////////////////////////////
	// getParent(): Gets the parent ScheduledInstance of this instance, if it
	//  already is a parent, returns null...
	public function getParent()
	{
		$event = $this->getRecurringEvent();
		if (is_null($event)) return null;

		$parent = new ScheduledInstance($event->getParentInstanceID());
		$parent->populate();

		return $parent;
	}

	////////////////////////////////////////////////////////////////////////////
	// hasChildren(): Gets whether this node has any children...
	public function hasChildren()
	{
	/*
		// Prepare general query to extract all item info from database...
		$stmt = $this->db->prepare("SELECT * FROM `ScheduledInstances` WHERE `ID` = ? LIMIT 1;");
		if (!$stmt) die("Could not populate '$this->ID' ScheduledInstance.");

		// Bind parameters and perform specific query...
		$stmt->bind_param('i', $this->ID);
		$stmt->execute();
		$stmt->store_result();
		
		// Determine if item exists (we must have precisely one)...
		$this->does_item_exist = ($stmt->num_rows == 1);
	
		return false;

*/

	}

#                                                                           [X]#
################################################################################

}

?>
