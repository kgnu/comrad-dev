<?php

################################################################################
# OBJECT:       DB                                                             #
# AUTHOR:       Eric Freese (02/17/2010)                                       #
# DESCRIPTION:  Implementation of CatalogInterface                             #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2010/03/22 (BCC) - Changes the name to make it more clear. This isn't an
#     interface in the OO construct, we should really try to follow conventions
#   2010/02/17 (EF) - Created
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

class DB implements InterfaceDB
{
	################################################################################
	# PROTECTED MEMBER VARIABLES                                                   #

	protected $db = NULL;                 // Database handle
	protected $is_connected = false;      // Whether we successfully connected

	#                                                                           [X]#
	################################################################################
	# PROTECTED FUNCTIONS                                                          #

	////////////////////////////////////////////////////////////////////////////
	// These methods build multi-part query strings from an array of column names
	protected function buildInsertQuery($tableName, $tableColumnPrefix, $params)
	{
		$columnNames = $params->getNames();
		 
		$query = "INSERT INTO `$tableName`";
		if (count($columnNames) > 0)
		$query .= " (`$tableColumnPrefix" . implode("`,`$tableColumnPrefix", $columnNames) . "`) VALUES (" .
		implode(",", array_fill(0, count($columnNames), "?")) . ")";
		return $query;
	}

	protected function buildSelectQuery($tableName, $whereClause, $tableColumnPrefix, $params, $options)
	{
		$limit = $options['limit'];
		$offset = $options['offset'];
		$sortColumn = $options['sortcolumn'];
		$groupColumn = $options['groupcolumn'];
		$asc = $options['ascending'];
		
		$columnTypes = $params->getTypes();
		$columnNames = $params->getNames();
		 
		// Generate SELECT clause
		$query = "SELECT ".($limit < 0 ? "COUNT(*) AS count" : "*")." FROM `$tableName`";

		// Generate WHERE clause
		if (strlen($whereClause) > 0) $query .= ' WHERE '.$whereClause;

		// Generate ORDER and LIMIT clauses
		if ($groupColumn) $query .= " GROUP BY `$tableColumnPrefix$groupColumn`";
		if ($sortColumn) $query .= " ORDER BY `$tableColumnPrefix$sortColumn` " . ($asc ? "ASC" : "DESC");
		if ($limit > 0) $query .= " LIMIT $offset,$limit";

		return $query;
	}

	protected function buildUpdateQuery($tableName, $tableColumnPrefix, $params, $primaryKeyName)
	{
		$query = "UPDATE `$tableName` SET ";
		$query .= "`$tableColumnPrefix" . implode("` = ?, `$tableColumnPrefix", $params->getNames()) . "` = ? WHERE `$tableColumnPrefix$primaryKeyName` = ?";
		
		return $query;
	}

	////////////////////////////////////////////////////////////////////////////
	// Prepares and executes a query. Returns the statement, or FALSE if unsuccessful
	/**
	 * @throws CouldNotPrepareQueryException
	 * @throws CouldNotExecuteQueryException
	 */
	protected function doQuery($query, $params)
	{
		// Prepare the query
		if (!$stmt = $this->db->prepare($query))
			throw new CouldNotPrepareQueryException($this->db->error);
		
		// Bind the params
		if (!is_null($params)) $params->bindParams($stmt);
		
		// Execute the query
		if (!$stmt->execute())
			throw new CouldNotExecuteQueryException($this->db->error);
		
		return $stmt;
	}

	////////////////////////////////////////////////////////////////////////////
	// Default constructor...
	private function __construct($dbName)
	{
		// We are using Initialize to gain access to configuration, make
		//  sure that the class has been defined...
		if (!class_exists('Initialize'))
		die('Class \'' . get_class($this) . '\' requires class \'Initialize\'.\n');

		global $init;  // Global InitWebTools object
		 
		// Connect to database...
		$this->db = new mysqli($init->getProp($dbName . '_Host'),
		$init->getProp($dbName . '_Username'),
		$init->getProp($dbName . '_Password'),
		$init->getProp($dbName . '_Database'));

		// Check if we have a good connection...
		$this->is_connected = ($this->db->connect_errno == 0);
		if (!$this->is_connected) {
			throw new CouldNotConnectToDatabaseException();
			$init->log("Error connecting to $dbName database: " . $this->db->connect_error);
		}
	}

	#                                                                           [X]#
	################################################################################
	# PUBLIC FUNCTIONS                                                             #

	////////////////////////////////////////////////////////////////////////////
	// Singleton access
	/**
	* @throws CouldNotConnectToDatabaseException
	*/
	public static function getInstance($dbName)
	{
		static $instances = array();
		if (isset($instances[$dbName]))
		{
			return $instances[$dbName];
		}
		else
		{
			$c = __CLASS__;
			try {
				$instances[$dbName] = new $c($dbName);
				return $instances[$dbName];
			} catch (CouldNotConnectToDatabaseException $e) {
				throw $e;
			}
		}
	}
	
	public function __destruct()
	{
		// Close database connection...
		if ($this->is_connected && !is_null($this->db))
		$this->db->close();
	}
	
	/**
	 * @throws ClientNotAuthorizedException
	 * @throws NotConnectedToDatabaseException
	 * @throws MissingRequiredPrimaryKeyException
	 * @throws AmbiguousGetQueryException
	 * @throws CouldNotPrepareQueryException
	 * @throws CouldNotExecuteQueryException
	 */
	public function get($dbObject) {
		if (!PermissionManager::getInstance()->currentUserHasPermission('read', get_class($dbObject)))
			throw new ClientNotAuthorizedException();
		
		if (!$this->is_connected)
			throw new NotConnectedToDatabaseException();
		
		if ($dbObject->isNew())
			throw new MissingRequiredPrimaryKeyException();
		
		// Execute the query
		$params = $dbObject->buildParameterList();
		$stmt = $this->doQuery("SELECT * FROM `".$dbObject->getTableName()."` WHERE `".$dbObject->getTableColumnPrefix().$dbObject->getPrimaryKey()."` = ? LIMIT 1", $params);

		// See if anything was returned. If not, return FALSE
		$stmt->store_result();
		if ($stmt->num_rows == 0) return null;
		if ($stmt->num_rows > 1) throw new AmbiguousGetQueryException();

		// Get the results from the query and clean up
		$meta = $stmt->result_metadata();
		while ($field = $meta->fetch_field()) {
			$newParams[] = &$row[$field->name];
		}
		call_user_func_array(array($stmt, 'bind_result'), $newParams);
		$result = array();
		while ($stmt->fetch()) {
			foreach ($row as $key => $val) {
				$result[str_replace($dbObject->getTableColumnPrefix(), '', $key)] = $val;
			}
		}
		
		// If not using a discriminator, use the class of the provided dbObject
		if (!$dbObject->usesDiscriminator()) {
			$class = get_class($dbObject);
		} else {
			$class = $result['DISCRIMINATOR'];
		}
		return new $class($result);
	}

	/**
	 * @throws ClientNotAuthorizedException
	 * @throws NotConnectedToDatabaseException
	 * @throws MissingRequiredFieldsException
	 * @throws InvalidFieldsException
	 * @throws ObjectAlreadyExistsException
	 * @throws CouldNotPrepareQueryException
	 * @throws CouldNotExecuteQueryException
	 */
	public function insert($dbObject) {
		if (!PermissionManager::getInstance()->currentUserHasPermission('insert', get_class($dbObject)))
			throw new ClientNotAuthorizedException();
		
		if (!$this->is_connected)
			throw new NotConnectedToDatabaseException();
		
		if (!$dbObject->hasAllRequiredFields())
			throw new MissingRequiredFieldsException();
		
		if (!$dbObject->hasValidFieldValues())
			throw new InvalidFieldsException();
		
		if (!$dbObject->isNew())
			throw new ObjectAlreadyExistsException();
		
		$params = $dbObject->buildParameterList();
		$this->doQuery($this->buildInsertQuery($dbObject->getTableName(), $dbObject->getTableColumnPrefix(), $params), $params)->close();
		
		return $this->db->insert_id;
	}
	
	/**
	 * @throws ClientNotAuthorizedException
	 * @throws NotConnectedToDatabaseException
	 * @throws ObjectDoesNotExistException
	 * @throws InvalidFieldsException
	 * @throws CouldNotPrepareQueryException
	 * @throws CouldNotExecuteQueryException
	 */
	public function update($dbObject) {
		if (!PermissionManager::getInstance()->currentUserHasPermission('write', get_class($dbObject)))
			throw new ClientNotAuthorizedException();
		
		if (!$this->is_connected)
			throw new NotConnectedToDatabaseException();
		
		if ($dbObject->isNew())
			throw new ObjectDoesNotExistException();
			
		if (!$dbObject->hasValidFieldValues())
			throw new InvalidFieldsException();
		
		// Get parameters from the object excluding the primary key
		$params = $dbObject->buildParameterList(false);
		
		// Build the query string
		$query = $this->buildUpdateQuery($dbObject->getTableName(), $dbObject->getTableColumnPrefix(), $params, $dbObject->getPrimaryKey());
		
		// Add the primary key at the end of the parameters for the WHERE clause
		$params->add(
			$dbObject->getColumnAbbreviatedType($dbObject->getPrimaryKey()),
			$dbObject->getPrimaryKey(),
			$dbObject->{$dbObject->getPrimaryKey()}
		);
		
		// Execute the query
		$stmt = $this->doQuery($query, $params);
		
		// TODO: affected_rows is 0 in two cases: when a row with the specified primary key does
		// not exist, and when the update query does not specify any fields different from what
		// already exists in the row.  We should differentiate between these.
		// if ($stmt->affected_rows == 0)
			// throw new ObjectDoesNotExistException();
			
		return $dbObject->{$dbObject->getPrimaryKey()};
	}
	
	/**
	 * @throws ClientNotAuthorizedException
	 * @throws NotConnectedToDatabaseException
	 * @throws ObjectDoesNotExistException
	 * @throws CouldNotPrepareQueryException
	 * @throws CouldNotExecuteQueryException
	 */
	public function delete($dbObject) {
		if (!PermissionManager::getInstance()->currentUserHasPermission('delete', get_class($dbObject)))
			throw new ClientNotAuthorizedException();
		
		if (!$this->is_connected)
			throw new NotConnectedToDatabaseException();
		
		if ($dbObject->isNew())
			throw new ObjectDoesNotExistException();
		
		// Create the ParameterList with only the primary key
		$params = new ParameterList();
		$params->add(
			$dbObject->getColumnAbbreviatedType($dbObject->getPrimaryKey()),
			$dbObject->getPrimaryKey(),
			$dbObject->{$dbObject->getPrimaryKey()}
		);
		
		// Execute the query
		$stmt = $this->doQuery("DELETE FROM `".$dbObject->getTableName()."` WHERE `".$dbObject->getTableColumnPrefix().$dbObject->getPrimaryKey()."` = ? LIMIT 1", $params);
		
		if ($stmt->affected_rows == 0)
			throw new ObjectDoesNotExistException();
	}
	
	/**
	 * @throws ClientNotAuthorizedException
	 * @throws NotConnectedToDatabaseException
	 * @throws SortColumnDoesNotExistException
	 * @throws CouldNotPrepareQueryException
	 * @throws CouldNotExecuteQueryException
	 */
	public function find($dbCriteria = null, &$count = false, $options = array()) {
		// Set up options
		$options = array_merge(array(
			'limit' => 100,
			'offset' => 0,
			'sortcolumn' => false,
			'groupcolumn' => false,
			'ascending' => true,
		), $options);
		
		// To be backward compatible, convert passed in DBObjects into DBCriteria objects
		if ($dbCriteria instanceof AbstractDBObject) {
			$dbCriteria = $dbCriteria->buildDBCriteria();
		}
		
		// Create a DBObject to access its attributes
		$className = $dbCriteria->getClassName();
		$dbObject = new $className();
		
		if (!PermissionManager::getInstance()->currentUserHasPermission('read', $className))
			throw new ClientNotAuthorizedException();
		
		if (!$this->is_connected)
			throw new NotConnectedToDatabaseException();
		
		if ($options['sortcolumn'] && !$dbObject->hasColumn($options['sortcolumn']))
			throw new SortColumnDoesNotExistException();
		
		// Add parameters from the object
		$params = $dbCriteria->getParameterList();
		
		// Execute the query
		$query = $this->buildSelectQuery($dbObject->getTableName(), $dbCriteria->getWhereClause(), $dbObject->getTableColumnPrefix(), $params, $options);
		$stmt = $this->doQuery($query, $params);
		
		// Get the results from the query and clean up
		$meta = $stmt->result_metadata();
		while ($field = $meta->fetch_field())
		{
			$newParams[] = &$row[$field->name];
		}
		call_user_func_array(array($stmt, 'bind_result'), $newParams);
		$queryResults = array();
		while ($stmt->fetch())
		{
			foreach ($row as $key => $val)
			{
				$c[$key] = $val;
			}
			$queryResults[] = $c;
		}
		
		// Build objects from the rows returned
		$results = array();
		if (!is_null($queryResults)) foreach($queryResults as $queryResult)
		{
			// Remove the table column prefix
			if (strlen($dbObject->getTableColumnPrefix()) > 0) {
				$a = array();
				foreach ($queryResult as $key => $value) {
					$a[str_replace($dbObject->getTableColumnPrefix(), '', $key)] = $value;
				}
				$queryResult = $a;
			}
			
			// If not using a discriminator, use the class of the provided dbObject
			if (!$dbObject->usesDiscriminator()) {
				$class = get_class($dbObject);
			} else {
				$class = $queryResult['DISCRIMINATOR'];
			}
			
			array_push($results, new $class($queryResult));
		}
		$stmt->close();
		
		// Perform another short query to get full count without limits
		$query = $this->buildSelectQuery($dbObject->getTableName(), $dbCriteria->getWhereClause(), $dbObject->getTableColumnPrefix(), $params, array_merge($options, array(
			'limit' => -1,
			'offset' => 0,
			'sortcolumn' => false,
			'ascending' => true
		)));
		
		$stmt = $this->doQuery($query, $params);
		
		$stmt->store_result();
		$stmt->bind_result($count);
		$stmt->fetch();
		$stmt->close();
		
		return $results;
	}

	#                                                                           [X]#
	################################################################################

}

?>
