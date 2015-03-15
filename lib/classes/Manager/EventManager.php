<?php

class EventManager extends Manager {
	public static function getInstance() {
		static $instance;
		if (!isset($instance)) {
			$c = __CLASS__;
			$instance = new $c();
		}
		return $instance;
	}
	
	public function getEventsOccurringAtTime($type = false, $dateTime = false) {
		if (!$dateTime) $dateTime = time();
		return $this->getEventsBetween($dateTime, $dateTime, $type);
	}
	
	// This function is responsible for searching for all scheduled events within the search window
	// It should return an array of ScheduledEventInstance objects
	public function getEventsBetween($startDateTime, $endDateTime, $type = false, $eventParameters = false, $instanceParameters = false, $scheduledEventId = NULL) {
	
		if (is_numeric($startDateTime)) $startDateTime = date('Y-m-d H:i:s', $startDateTime);
		if (is_numeric($endDateTime)) $endDateTime = date('Y-m-d H:i:s', $endDateTime);
		
		$exceptions = $this->getScheduledEventExceptionsBetween(date('Y-m-d', strtotime($startDateTime)), date('Y-m-d', strtotime($endDateTime)));
		
		// Be backwards compatible, convert $eventParameters and $instanceParameters
		if ($eventParameters && count($eventParameters) > 0 && !array_key_exists(0, $eventParameters)) {
			$newEventParameters = array();
			foreach ($eventParameters as $columnName => $columnValue) {
				array_push($newEventParameters, array($columnName, '=', $columnValue));
			}
			$eventParameters = $newEventParameters;
		}
		
		if ($instanceParameters && count($instanceParameters) > 0 && !array_key_exists(0, $instanceParameters)) {
			$newInstanceParameters = array();
			foreach ($instanceParameters as $columnName => $columnValue) {
				array_push($newInstanceParameters, array($columnName, '=', $columnValue));
			}
			$instanceParameters = $newInstanceParameters;
		}
		
		$instancesFromScheduledEvents = $this->getEventInstancesFromScheduledEventsBetween(
			$startDateTime,
			$endDateTime,
			$this->buildTypeClause($type),
			$this->buildEventParametersClause($eventParameters, $type),
			$scheduledEventId
		);	
		
		$instancesFromScheduledEventInstances = $this->getEventInstancesFromScheduledEventInstancesBetween(
			$startDateTime,
			$endDateTime,
			$this->buildTypeClause($type),
			$this->buildEventParametersClause($eventParameters, $type),
			$scheduledEventId
		);
		
		$results = $instancesFromScheduledEventInstances;
		
		// Add in all of the instances from ScheduledEvents that are replaced by ScheduledEventInstances
		foreach ($instancesFromScheduledEvents as $instanceFromScheduledEvents) {
			$isInstance = $isException = false;
			
			// Check for an instance
			foreach ($instancesFromScheduledEventInstances as $instanceFromScheduledEventInstances) {
				if (date('Y-m-d', $instanceFromScheduledEvents->StartDateTime) == date('Y-m-d', $instanceFromScheduledEventInstances->StartDateTime) && $instanceFromScheduledEvents->ScheduledEventId == $instanceFromScheduledEventInstances->ScheduledEventId) {
					$isInstance = true;
					break;
				}
			}
			if ($isInstance) continue;
			
			// Check for an exception
			foreach ($exceptions as $exception) {
				if (date('Y-m-d', $instanceFromScheduledEvents->StartDateTime) == date('Y-m-d', $exception->ExceptionDate) && $instanceFromScheduledEvents->ScheduledEventId == $exception->ScheduledEventId) {
					$isException = true;
					break;
				}
			}
			if ($isException) continue;
			
			array_push($results, $instanceFromScheduledEvents);
		}
		
		// Filter out all results that do not match the instanceParameter criteria
		if ($instanceParameters && count($instanceParameters) > 0) {
			$filteredResults = array();
			foreach ($results as $instance) {
				$validResult = true;
				
				foreach ($instanceParameters as $instanceParameter) {
					if (is_array($instanceParameter) && count($instanceParameter) > 2) {
						$columnName = $instanceParameter[0];
						$operator = $instanceParameter[1];
						$columnValue = $instanceParameter[2];
						
						if ($instance->hasColumn($columnName)) {
							if ($this->checkCriteria($instance->{$columnName}, $operator, $columnValue)) {
								continue; // Valid
							}
						} else if ($instance->ScheduledEvent->Event->hasColumn($columnName) && $this->checkCriteria($instance->ScheduledEvent->Event->{$columnName}, $operator, $columnValue)) {
							continue; // Valid
						}
						
						$validResult = false;
						break;
					}
				}
				if ($validResult) array_push($filteredResults, $instance);
			}
			$results = $filteredResults;
		}
		
		return $results;
	}
	
	// This function returns the event instances associated with a particular HostId
	// It should return an array of ScheduledEventInstance objects
	public function getShowsByHostId($hostId) {
		
		$startDateTime = date('Y-m-d H:i:s', 0);
		$endDateTime = date('Y-m-d H:i:s', time());
		
		$instancesFromScheduledEventInstances = $this->getEventInstancesFromScheduledEventInstancesBetween(
			$startDateTime,
			$endDateTime,
			$this->buildTypeClause('Show'),
			'AND sei.sei_HostId = "' . intval($hostId) . '"' //use intval to prevent SQL injection by only allowing integers
		);
		
		$results = $instancesFromScheduledEventInstances;
		
		return $results;
	}
	
	// Helper function to manually check criteria for instanceParameters, since we're not doing a sql query for that
	private function checkCriteria($left, $operator, $right = null) {
		switch ($operator) {
			case '=':
				return $left == $right;
				break;
			case '!=':
				return $left != $right;
				break;
			case '>':
				return $left > $right;
				break;
			case '>=':
				return $left >= $right;
				break;
			case '<':
				return $left < $right;
				break;
			case '<=':
				return $left <= $right;
				break;
			case 'IS NULL':
				return $left === null;
			case 'IS NOT NULL':
				return $left !== null;
		}
		
		return false;
	}
	
	private function buildTypeClause($type) {
		if ($type) {
			if (is_array($type)) {
				if (count($type) > 0) {
					$typeClause = "AND (";
					$pastFirst = false;
					foreach ($type as $singleType) {
						if ($pastFirst) $typeClause .= ' OR ';
						$typeClause .= "e.e_DISCRIMINATOR = '{$singleType}Event'";
						$pastFirst = true;
					}
					$typeClause .= ") ";
				}
			} else {
				$typeClause = "AND e.e_DISCRIMINATOR = '{$type}Event' ";
			}
		} else {
			$typeClause = "";
		}
		
		return $typeClause;
	}
	
	private function buildEventParametersClause($eventParameters, $type) {
		$whereClauses = array();
		
		if ($eventParameters && count($eventParameters) > 0) {
			$criterias = array();
			if (!$type) {
				array_push($criterias, new DBCriteria('Event', $eventParameters, false));
			} else if (is_array($type)) {
				foreach ($type as $singleType) {
					array_push($criterias, new DBCriteria($singleType.'Event', $eventParameters, false));
				}
			} else {
				array_push($criterias, new DBCriteria($type.'Event', $eventParameters, false));
			}
			
			// Concatenate all the Criteria's where clauses
			foreach ($criterias as $criteria) {
				array_push($whereClauses, $criteria->getWhereClause());
			}
		}
		
		if (count($whereClauses) > 1) {
			$eventParametersClause = 'AND ('.implode(' OR ', $whereClauses).') ';
		} elseif (count($whereClauses) > 0) {
			$eventParametersClause = 'AND '.$whereClauses[0];
		} else {
			$eventParametersClause = '';
		}
		
		return $eventParametersClause;
	}
	
	private function getScheduledEventExceptionsBetween($startDateTime, $endDateTime) {
		$query = "SELECT see.*
			FROM ScheduledEventException AS see
			WHERE see.see_ExceptionDate <= ?
			AND see.see_ExceptionDate >= ?";
		
		$params = new ParameterList();
		$params->add('s', '', $endDateTime);
		$params->add('s', '', $startDateTime);
		
		$queryResults = $this->doQuery($query, $params);
		
		// Transform the MySQL data into ScheduledEvent objects
		$results = array();
		foreach ($queryResults as $queryResult) {
			$see = new ScheduledEventException(array(
				'Id' => $queryResult['see_Id'],
				'ScheduledEventId' => $queryResult['see_ScheduledEventId'],
				'ExceptionDate' => $queryResult['see_ExceptionDate']
			));
			
			array_push($results, $see);
		}
		
		return $results;
	}
	
	private function getEventInstancesFromScheduledEventInstancesBetween($startDateTime, $endDateTime, $typeClause, $eventParametersClause, $scheduledEventId = NULL) {

		
		if ($scheduledEventId) {
			$scheduledEventClause = 'se.se_id = ?';
		} else {
			$scheduledEventClause = '';
		}
		
		$query = "SELECT e.*, se.*, ti.*, sei.*
			FROM ScheduledEventInstance AS sei
			LEFT JOIN ScheduledEvent AS se ON sei.sei_ScheduledEventId = se.se_Id
			LEFT JOIN TimeInfo AS ti ON se.se_TimeInfoId = ti.ti_Id
			LEFT JOIN Event AS e ON se.se_EventId = e.e_Id
			WHERE sei.sei_StartDateTime < ?
			{$typeClause}
			{$eventParametersClause}
			AND (UNIX_TIMESTAMP(sei.sei_StartDateTime) + sei.sei_Duration * 60 > UNIX_TIMESTAMP(?))";
		$params = new ParameterList();
		$params->add('s', '', $endDateTime);
		$params->add('s', '', $startDateTime);
		
		if ($scheduledEventId) {
			$params->add('s', '', $scheduledEventId);
		}
		
		$queryResults = $this->doQuery($query, $params);
		
		// Transform the MySQL data into ScheduledEvent objects
		$results = array();
		foreach ($queryResults as $queryResult) {
			if ($queryResult['sei_DISCRIMINATOR']) {
				$seiClass = $queryResult['sei_DISCRIMINATOR'];
				$sei = new $seiClass();
				
				$se = new ScheduledEvent();
				
				if ($eClass = $queryResult['e_DISCRIMINATOR']) {
					$e = new $eClass();
					
					if ($tiClass = $queryResult['ti_DISCRIMINATOR']) {
						$ti = new $tiClass();
			
						// Sort out the columns and remove the prefixes
						$seiCols = $seCols = $eCols = $tiCols = array();
						foreach ($queryResult as $key => $value) {
							if (strpos($key, $sei->getTableColumnPrefix()) === 0) {
								$seiCols[str_replace($sei->getTableColumnPrefix(), '', $key)] = $value;
							} else if (strpos($key, $se->getTableColumnPrefix()) === 0) {
								$seCols[str_replace($se->getTableColumnPrefix(), '', $key)] = $value;
							} else if (strpos($key, $e->getTableColumnPrefix()) === 0) {
								$eCols[str_replace($e->getTableColumnPrefix(), '', $key)] = $value;
							} else if (strpos($key, $ti->getTableColumnPrefix()) === 0) {
								$tiCols[str_replace($ti->getTableColumnPrefix(), '', $key)] = $value;
							}
						}
			
						// Build the ScheduledEvent
						$se = new ScheduledEvent($seCols);
						$se->Event = new $eClass($eCols);
						
						// TODO: Should be replaced by some more generic method such as $se->Event->fetchAllForeignKeyItems
						if ($eClass == 'PSAEvent') $se->Event->fetchForeignKeyItem('PSACategory');
						if ($eClass == 'ShowEvent') $se->Event->fetchForeignKeyItem('Host');
						
						$se->TimeInfo = new $tiClass($tiCols);
			
						// Build the ScheduledEventInstance
						$sei = new $seiClass($seiCols);
						
						if ($seiClass == 'ScheduledShowInstance') $sei->fetchForeignKeyItem('Host');
						
						$sei->ScheduledEvent = $se;
			
						array_push($results, $sei);
					}
				}
			}
		}
		
		return $results;
	}
	
	private function getEventInstancesFromScheduledEventsBetween($startDateTime, $endDateTime, $typeClause, $eventParametersClause, $scheduledEventId = NULL) {
	
		if ($scheduledEventId) {
			$scheduledEventClause = 'AND se.se_Id = ?';
		} else {
			$scheduledEventClause = '';
		}
	
		$query = "SELECT e.*, se.*, ti.*
			FROM ScheduledEvent AS se
			LEFT JOIN TimeInfo AS ti ON se.se_TimeInfoId = ti.ti_Id
			LEFT JOIN Event AS e ON se.se_EventId = e.e_Id
			WHERE ti.ti_StartDateTime < ?
			{$typeClause}
			{$eventParametersClause}
			AND (
				(ti.ti_DISCRIMINATOR = 'NonRepeatingTimeInfo' AND UNIX_TIMESTAMP(ti.ti_StartDateTime) + ti.ti_Duration * 60 > UNIX_TIMESTAMP(?))
			OR
				(ti.ti_DISCRIMINATOR != 'NonRepeatingTimeInfo' AND (ti.ti_EndDate IS NULL OR UNIX_TIMESTAMP(ti.ti_EndDate) + ti.ti_Duration * 60 + 86400 > UNIX_TIMESTAMP(?)))
			)
			{$scheduledEventClause}";
		
		$params = new ParameterList();
		$params->add('s', '', $endDateTime);
		$params->add('s', '', $startDateTime);
		$params->add('s', '', $startDateTime);
		if ($scheduledEventId) {
			$params->add('s', '', $scheduledEventId);
		}
		
		$queryResults = $this->doQuery($query, $params);
		
		// Transform the MySQL data into ScheduledEvent objects
		
		$results = array();
		foreach ($queryResults as $queryResult) {
			$se = new ScheduledEvent();
			
			if ($queryResult['e_DISCRIMINATOR']) {
				$eClass = $queryResult['e_DISCRIMINATOR'];
				$e = new $eClass();
				
				if ($queryResult['ti_DISCRIMINATOR']) {
					$tiClass = $queryResult['ti_DISCRIMINATOR'];
					$ti = new $tiClass();
			
					// Sort out the columns and remove the prefixes
					$seCols = $eCols = $tiCols = array();
					foreach ($queryResult as $key => $value) {
						if (strpos($key, $se->getTableColumnPrefix()) === 0) {
							$seCols[str_replace($se->getTableColumnPrefix(), '', $key)] = $value;
						} else if (strpos($key, $e->getTableColumnPrefix()) === 0) {
							$eCols[str_replace($e->getTableColumnPrefix(), '', $key)] = $value;
						} else if (strpos($key, $ti->getTableColumnPrefix()) === 0) {
							$tiCols[str_replace($ti->getTableColumnPrefix(), '', $key)] = $value;
						}
					}
					
					// Build the ScheduledEvent
					$se = new ScheduledEvent($seCols);
					$se->Event = new $eClass($eCols);
					
					// TODO: Should be replaced by some more generic method such as $se->Event->fetchAllForeignKeyItems
					if ($eClass == 'PSAEvent') $se->Event->fetchForeignKeyItem('PSACategory');
					if ($eClass == 'ShowEvent') $se->Event->fetchForeignKeyItem('Host');
					
					$se->TimeInfo = new $tiClass($tiCols);
					
					// Delegate to the TimeInfo to create an array of ScheduledEventInstances inside the specified time window
					$results = array_merge($results, $se->TimeInfo->createScheduledEventInstancesForTimeWindow(strtotime($startDateTime), strtotime($endDateTime), $se));
					
				}
			}
		}
		
		return $results;
	}
}

?>