<?php

class Manager {
	private $db = NULL;
	private $is_connected = false;
	
	public static function getInstance() {
		static $instance;
		if (!isset($instance)) {
			$c = __CLASS__;
			$instance = new $c();
		}
		return $instance;
	}
	
	public function __construct() {
		// We are using Initialize to gain access to configuration, make
		//  sure that the class has been defined...
		if (!class_exists('Initialize'))
		die('Class \'' . get_class($this) . '\' requires class \'Initialize\'.\n');
		
		global $init;  // Global InitWebTools object
		 
		// Connect to database...
		$this->db = new mysqli(
			$init->getProp('MySql_Host'),
			$init->getProp('MySql_Username'),
			$init->getProp('MySql_Password'),
			$init->getProp('MySql_Database')
		);
		
		// Check if we have a good connection...
		$this->is_connected = ($this->db->connect_errno == 0);
		if (!$this->is_connected) $init->log("Error connecting to database: " . $this->db->connect_error);
	}
	
	protected function doQuery($query, $params = null) {
		global $init;
		
		// Prepare the query
		$stmt = $this->db->prepare($query);
		if (!$stmt) {
			$init->log("Could not prepare query: " . $this->db->error . ' [QUERY: ' . $query . ']');
			return false;
		}
		
		if (!is_null($params)) $params->bindParams($stmt);
	
		// Execute the query
		if (!$stmt->execute()) {
			$init->log("Could not execute get {$type} query: " . $this->db->error);
			return false;
		}
		
		// Get the results from the query and clean up
		$meta = $stmt->result_metadata();
		while ($field = $meta->fetch_field()) {
			$newParams[] = &$row[$field->name];
		}
		call_user_func_array(array($stmt, 'bind_result'), $newParams);
		$queryResults = array();
		while ($stmt->fetch()) {
			foreach ($row as $key => $val) {
				$c[$key] = $val;
			}
			$queryResults[] = $c;
		}
		
		$stmt->close();
		
		return $queryResults;
	}
	
	protected function bindParams(&$stmt) {
		return $stmt;
	}
	
}