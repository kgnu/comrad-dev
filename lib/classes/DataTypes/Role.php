<?php
class Role extends AbstractDBObject {
	public function __construct($params = array()) {
		$this->columns = array(
			'Id' => array(
				'type' => 'PrimaryKey'
			),
			'Name' => array(
				'type' => 'String',
				'required' => true,
				'titlecolumn' => true
			),
			'DBObjectPermissions' => array(
				'type' => 'ForeignKeyCollection',
				'foreignType' => 'DBObjectPermission',
				'localcolumn' => 'Id',
				'foreigncolumn' => 'RoleId'
			)
		);

		parent::__construct($params);
	}
	 
	public function getTableName()
	{
		return "Role";
	}
	 
	public function __toString()
	{
		return $this->Name;
	}
}
?>