<?php
abstract class AbstractColumnSet
{
	protected $columns;
	
	public function __construct() {
		$this->columns = array();
	}
	
	public function addColumns($columns) {
		$this->columns = array_merge($this->columns, $columns);
	}
	
	public function getColumns() {
		return $this->columns;
	}
}
?>
