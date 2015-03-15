<?php

abstract class AbstractDBObject
{

	################################################################################
	# PRIVATE MEMBER VARIABLES                                                     #

	protected $columns;
	protected $primaryKey;
	protected $titleColumn;
	
	protected $DISCRIMINATOR;
	protected $possibleDiscriminatorValues;

	#                                                                           [X]#
	################################################################################
	# PUBLIC FUNCTIONS                                                             #

	////////////////////////////////////////////////////////////////////////////
	// Required implementation functions
	public abstract function __toString();
	public abstract function getTableName();

	////////////////////////////////////////////////////////////////////////////
	// Constructor: Subclasses should define $this->columns, then call this super-class constructor.
	public function __construct($params = array())
	{
		// We are using Initialize to gain access to configuration, make
		//  sure that the class has been defined...
		if (!class_exists('Initialize'))
			die('Class \'' . get_class($this) . '\' requires class \'Initialize\'.\n');

		// Set column values from parameters
		foreach ($this->columns as $columnName => $data)
		{
			if ($data['type'] == 'PrimaryKey')
			{
				$this->primaryKey = $columnName;
			}
			
			if (array_key_exists('titlecolumn', $data) && $data['titlecolumn'] == true)
			{
				$this->titleColumn = $columnName;
			}

			if (array_key_exists($columnName, $params))
			{
				$this->$columnName = $params[$columnName];
			}
			// else if(array_key_exists('default', $data))
			// {
			// 	// Defaults seemed like a good idea, but these objects are used for more than
			// 	// just data storage, for example querying. These other tasks often
			// 	// require null member variables, which defaults don't respect. I left this here
			// 	// as a note in case someone wants to extend defaults to apply only when
			// 	// inserting into the database, or to autofill incomplete fields, etc.
			// 	
			// 	$this->$columnName = null;
			// }
			// else
			// {
			// 	$this->$columnName = null;
			// }
		}
	}


	////////////////////////////////////////////////////////////////////////////
	// Simple accessor functions
	public function __get($columnName)
	{
		return (array_key_exists('value', $this->columns[$columnName]) ? $this->columns[$columnName]['value'] : null);
	}

	public function getColumnType($columnName)
	{
		return $this->columns[$columnName]['type'];
	}
	
	public function getColumnDefault($columnName)
	{
		if (array_key_exists('default', $this->columns[$columnName])) {
			return $this->columns[$columnName]['default'];
		} else {
			return null;
		}
	}

	public function getColumnAbbreviatedType($columnName)
	{
		if ($columnName == 'DISCRIMINATOR') return 's';
		switch($this->columns[$columnName]['type'])
		{
			case 'ShortString':
			case 'UppercaseString':
			case 'String':
			case 'Enumeration':
			case 'Date':			// We're saving a MySQL Date which is in Y-m-d H:i:s format (a string, BCC)
				return 's';
			case 'PrimaryKey':
			case 'ForeignKey':
			case 'Integer':
			case 'Boolean':
				return 'i';
		}
		global $init;
		$init->log("Attempting to get abbreviated type for unknown column type [$columnName:".$this->columns[$columnName]['type']."]");
	}

	public function getPrimaryKey()
	{
		return $this->primaryKey;
	}

	public function getTitleColumn()
	{
		return $this->titleColumn;
	}
	
	public function getColumns()
	{
		return $this->columns;
	}
	
	public function getColumnValues() {
		$columnValues = array();
		
		foreach($this->columns as $columnName => $data) {
		    if (array_key_exists('value', $data)) {
    			if ($data['value'] instanceof self) {
    				$columnValues[$columnName] = $data['value']->toArray();
    			} else {
    				$columnValues[$columnName] = $data['value'];
    			}
			}
		}
		
		return $columnValues;
	}
	
	public function setColumnAttribute($columnName, $attributeName, $attributeValue) {
		if ($this->columns[$columnName]) {
			$this->columns[$columnName][$attributeName] = $attributeValue;
		} else {
			$init->log("setColumnAttribute: Column '$columnName' does not exist!");
		}
	}
	
	public function addColumns($columns) {
		$this->columns = array_merge(($this->columns ? $this->columns : array()), $columns);
	}
	
	public function addColumnSets($columnSets) {
		foreach ($columnSets as $columnSet) {
			$this->addColumns($columnSet->getColumns());
		}
	}
	
	public function toArray() {
		return array(
			'Type' => get_class($this),
			'Attributes' => $this->getColumnValues()
		);
	}

	////////////////////////////////////////////////////////////////////////////
	// Simple mutator functions
	public function __set($columnName, $value)
	{
		$this->{'process'.$this->columns[$columnName]['type']}($columnName, $value);
	}

	////////////////////////////////////////////////////////////////////////////
	// Processing functions
	protected function processInteger($columnName, $value)
	{
		if (is_int($value)) {
			$this->columns[$columnName]['value'] = $value;
		} elseif (is_numeric($value)) {
			$this->columns[$columnName]['value'] = (int)$value;
		}
	}
	
	protected function processPrimaryKey($columnName, $value)
	{
		if (is_numeric($value) && (int)$value > 0) $this->processInteger($columnName, $value);
	}
	
	protected function processForeignKey($columnName, $value)
	{
		if (is_numeric($value)) $this->processInteger($columnName, $value);
	}

	protected function processString($columnName, $value)
	{
		$this->columns[$columnName]['value'] = is_null($value) ? null : trim($value);
	}

	protected function processShortString($columnName, $value)
	{
	    $this->processString($columnName, $value);
	}

	protected function processUppercaseString($columnName, $value)
	{
		$this->columns[$columnName]['value'] = is_null($value) ? null : ucwords(trim($value));
	}

	protected function processEnumeration($columnName, $value)
	{
	    $this->processUppercaseString($columnName, $value);
	}

	protected function processDate($columnName, $value)
	{
		if (is_null($value))
			$this->columns[$columnName]['value'] = null;
		else if (is_int($value))
			$this->columns[$columnName]['value'] = $value;
		else if ($value === "NOW")
			$this->columns[$columnName]['value'] = time();
		else
			$this->columns[$columnName]['value'] = strtotime($value);
	}

	protected function processBoolean($columnName, $value)
	{
		$this->columns[$columnName]['value'] = is_null($value) ? null : ($value ? 1 : 0);
	}
	
	protected function processForeignKeyItem($columnName, $value) {
		$this->columns[$columnName]['value'] = $value;
	}
	
	protected function processForeignKeyCollection($columnName, $value)
	{
		if (is_array($value)) {
			$this->columns[$columnName]['value'] = $value;
		}
	}

	////////////////////////////////////////////////////////////////////////////
	// Other functions
	public function isNew()
	{
		return (!array_key_exists('value', $this->columns[$this->primaryKey]) || $this->columns[$this->primaryKey]['value'] == null);
	}
	
	public function isRequiredField($columnName) {
		return (array_key_exists('required', $this->columns[$columnName]) && $this->columns[$columnName]['required'] == true);
	}

	public function hasAllRequiredFields()
	{
		global $init;
		foreach ($this->columns as $columnName => $data)
		{
			if (array_key_exists('required', $data) && $data['required'] == true && !array_key_exists('value', $data)) {
				return false;
			}
		}
		return true;
	}
	
	public function hasValidFieldValues() {
		foreach ($this->columns as $columnName => $data) {
			if (array_key_exists('value', $data)) {
				// Don't allow required columns to be null or empty
				if (array_key_exists('required', $data) && $data['required'] == true) {
					if (is_null($data['value'])) return false;
					if (!is_int($data['value']) && $data['value'] == '') return false;
				}
				
				// Make sure all of the validators pass
				if (array_key_exists('validators', $data)) {
					foreach ($data['validators'] as $validator) {
						if (!$validator->isValid($data['value'])) return false;
					}
				}
			}
		}
		return true;
	}

	public function hasColumn($columnName)
	{
		return array_key_exists($columnName, $this->columns);
	}
	
	public function fetchForeignKeyItem($columnName) {
		if (array_key_exists($columnName, $this->columns) && $this->columns[$columnName]['type'] == 'ForeignKeyItem') {
			$foreignType = $this->columns[$columnName]['foreignType'];
			$localColumn = $this->columns[$columnName]['localcolumn'];
			$foreignColumn = $this->columns[$columnName]['foreigncolumn'];
			
			if ($this->$localColumn != null) {
				$this->$columnName = DB::getInstance('MySql')->get(new $foreignType(array($foreignColumn => $this->$localColumn)));
			}
		}
	}

	public function buildParameterList($includePrimaryKey = true)
	{
		$params = new ParameterList();
		
		if ($this->usesDiscriminator()) {
			$params->add('s', 'DISCRIMINATOR', $this->DISCRIMINATOR);
		}
		
		foreach ($this->columns as $columnName => $data)
		{
			if (!$includePrimaryKey && $columnName == $this->primaryKey || $data['type'] == 'ForeignKeyItem')
			{
				continue;
			}
			else if (array_key_exists('value', $data) && !is_null($data['value']))
			{
				// Convert timestamps to strings. Not the best place to do this, but better than before.
				if ($this->getColumnType($columnName) == 'Date')
					$params->add($this->getColumnAbbreviatedType($columnName), $columnName, date('Y-m-d H:i:s', $data['value']));
				else
					$params->add($this->getColumnAbbreviatedType($columnName), $columnName, $data['value']);
			}
		}
		return $params;
	}
	
	public function buildDBCriteria($includePrimaryKey = true) {
		$criteria = array();
		foreach ($this->columns as $columnName => $data) {
			if (!$includePrimaryKey && $columnName == $this->primaryKey || $data['type'] == 'ForeignKeyItem')
			{
				continue;
			} else if (array_key_exists('value', $data) && !is_null($data['value'])) {
				array_push($criteria, array($columnName, '=', $data['value']));
			}
		}
		
		return new DBCriteria(get_class($this), $criteria);
	}
	
	public function useDiscriminator($possibleValues)
	{
		$this->possibleDiscriminatorValues = $possibleValues;
	}
	
	public function getDiscriminatorValue() {
		return $this->DISCRIMINATOR;
	}
	
	public function usesDiscriminator() {
		return ($this->possibleDiscriminatorValues != null);
	}
	
	// Can be overridden in subclasses to add a prefix to each column in the database.
	public function getTableColumnPrefix() {
		return '';
	}

	#                                                                           [X]#
	################################################################################

}

?>
