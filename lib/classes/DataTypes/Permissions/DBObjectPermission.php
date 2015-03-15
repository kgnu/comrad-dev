<?php
class DBObjectPermission extends AbstractDBObject {
	public function __construct($params = array()) {
		$this->columns = array(
			'Id' => array(
				'type' => 'PrimaryKey'
			),
			'DBObjectId' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'DBObject',
				'required' => true
			),
			'DBObject' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'DBObject',
				'localcolumn' => 'DBObjectId',
				'foreigncolumn' => 'Id'
			),
			'RoleId' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'Role',
				'required' => true,
				'tostring' => 'Role'
			),
			'Role' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'Role',
				'localcolumn' => 'RoleId',
				'foreigncolumn' => 'Id'
			),
			'Read' => array(
				'type' => 'Boolean'
			),
			'Write' => array(
				'type' => 'Boolean'
			),
			'Insert' => array(
				'type' => 'Boolean'
			),
			'Delete' => array(
				'type' => 'Boolean'
			)
		);

		parent::__construct($params);
	}
	
	public function getTableColumnPrefix() {
		return 'op_';
	}
	 
	public function getTableName()
	{
		return "DBObjectPermission";
	}
	 
	public function __toString()
	{
		return $this->Id;
	}
}
?>