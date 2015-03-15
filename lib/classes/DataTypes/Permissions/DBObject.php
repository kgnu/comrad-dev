<?php
class DBObject extends AbstractDBObject {
	public function __construct($params = array()) {
		$this->columns = array(
			'Id' => array(
				'type' => 'PrimaryKey'
			),
			'ParentId' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'DBObject',
				'tostring' => 'Parent'
			),
			'Parent' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'DBObject',
				'localcolumn' => 'ParentId',
				'foreigncolumn' => 'Id'
			),
			'Children' => array(
				'type' => 'ForeignKeyCollection',
				'foreignType' => 'DBObject',
				'localcolumn' => 'Id',
				'foreigncolumn' => 'ParentId'
			),
			'Name' => array(
				'type' => 'String',
				'required' => true,
				'titlecolumn' => true
			)
		);

		parent::__construct($params);
	}
	
	public function getTableColumnPrefix() {
		return 'o_';
	}
	 
	public function getTableName()
	{
		return 'DBObject';
	}
	 
	public function __toString()
	{
		return $this->Name;
	}
}
?>