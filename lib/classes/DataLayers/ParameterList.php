<?php

################################################################################
# OBJECT:       ParameterList                                                  #
# AUTHOR:       Tom Buzbee (01/25/2010)                                        #
# DESCRIPTION:  Manages binding parameters to MySQL statements                 #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2010/01/25 (TB) - Created
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

class ParameterList
{

################################################################################
# PRIVATE MEMBER VARIABLES                                                     #

	private $types = array();			// Type characters for each parameter
	private $names = array();			// Column names
	private $values = array();			// Values
	
	private $secondaryTypes = array();	// Type characters for each parameter
	private $secondaryNames = array();	// Column names
	private $secondaryValues = array();	// Values

#                                                                           [X]#
################################################################################
# PUBLIC FUNCTIONS                                                             #

	////////////////////////////////////////////////////////////////////////////
	// Constructor
	public function __construct()
	{
	}

	////////////////////////////////////////////////////////////////////////////
	// Adds a regular parameter to the list
	public function add($type, $name, $value)
	{
		if (!is_null($value))
		{
			$this->types[] = $type;
			$this->names[] = $name;
			$this->values[] = $value;
		}
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Adds a secondary parameter to the list
	public function addSecondary($type, $name, $value)
	{
		if (!is_null($value))
		{
			$this->secondaryTypes[] = $type;
			$this->secondaryNames[] = $name;
			$this->secondaryValues[] = $value;
		}
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Returns the list of all regular types
	public function getTypes()
	{
		return $this->types;
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Returns the list of all regular column names with non-null values
	public function getNames()
	{
		return $this->names;
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Returns the list of regular non-null values
	public function getValues()
	{
		return $this->values;
	}

	////////////////////////////////////////////////////////////////////////////
	// Binds parameters to the statement
	public function bindParams($stmt)
	{
		if(count($this->values) + count($this->secondaryValues) > 0)
		{
			// Create an array of references to the values stored in $this->values and
			// $this->secondaryValues b/c bind_param requires params by reference.
			// See http://stackoverflow.com/questions/2045875/pass-by-reference-problem-with-php-5-3-1
			$values = array_merge($this->values, $this->secondaryValues);
			$refs = array();
			foreach ($values as $key => $value) {
				$refs[$key] = &$values[$key];
			}
			
			call_user_func_array(array($stmt, 'bind_param'),
				array_merge(array(implode($this->types) . implode($this->secondaryTypes)), $refs)
			);
		}
	}

#                                                                           [X]#
################################################################################

}

?>
