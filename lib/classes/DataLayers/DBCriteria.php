<?php

class DBCriteria {
	protected $dbObject;
	protected $mysqli;
	protected $className;
	protected $criteria;
	protected $whereClause;
	protected $paramList;
	
	public function __construct($className, $criteria, $mysqli = true) {
		$this->dbObject = new $className();
		
		$this->mysqli = $mysqli;
		$this->className = $className;
		$this->criteria = $criteria;
		
		if ($this->dbObject->usesDiscriminator()) {
			array_push($this->criteria, array('DISCRIMINATOR', '=' , $this->dbObject->getDiscriminatorValue()));
		}
		
		$this->whereClause = $this->buildWhereClause();
	}
	
	public function getClassName() {
		return $this->className;
	}
	
	public function getCriteria() {
		return $this->criteria;
	}
	
	public function getWhereClause() {
		return $this->whereClause;
	}
	
	public function getParameterList() {
		return $this->paramList;
	}
	
	public function buildWhereClause($key = null, $value = null) {
		if ($key == null && $value == null) {
			$value = $this->criteria;
			$key = 'AND';
			$this->paramList = new ParameterList();
		}
		
		if ($key == null && (count($value) == 2 || count($value) == 3)) {
			// If key is not set, this is a leaf. Return its string
			$column = $value[0];
			$operator = $value[1];
			if ($this->dbObject->hasColumn($column) || ($this->dbObject->usesDiscriminator() && $column == 'DISCRIMINATOR')) {
				if (count($value) > 2) {
					if ($this->mysqli) {
						$this->paramList->add($this->dbObject->getColumnAbbreviatedType($column), '', $value[2]);
						return '`' . $this->dbObject->getTableColumnPrefix() . $column . '` ' . $operator . ' ?';
					} else {
						return '`' . $this->dbObject->getTableColumnPrefix() . $column . '` ' . $operator . ' ' . (is_string($value[2]) ? "'" : '') . ($value[2] === false ? 0 : $value[2]) . (is_string($value[2]) ? "'" : '');
					}
				} else {
					return '`' . $this->dbObject->getTableColumnPrefix() . $column . '` ' . $operator;
				}
			} else {
				return null;
			}
		} else if ($key == 'AND' || $key == 'OR') {
			// Descend over each child and join them with the key
			$childClauses = array();
			
			foreach ($value as $condition => $conditionNode) {
				$whereClause = $this->buildWhereClause(!is_numeric($condition) ? $condition : null, $conditionNode);
				if ($whereClause != null) array_push($childClauses, $whereClause);
			}
			
			if (count($childClauses) > 1) {
				return '(' . implode(' ' . $key . ' ', $childClauses) . ')';
			} else {
				return implode(' ' . $key . ' ', $childClauses);
			}
		} else {
			return null;
		}
	}
}

?>
