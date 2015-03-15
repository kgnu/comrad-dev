<?php
class User extends AbstractDBObject {
	public function __construct($params = array()) {
		$this->columns = array(
			'Id' => array(
				'type' => 'PrimaryKey'
			),
			'Username' => array(
				'type' => 'String',
				'required' => true,
				'titlecolumn' => true
			),
			'PasswordHash' => array(
				'type' => 'String',
				'required' => true
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
			'LastVisit' => array(
				'type' => 'Date',
				'tostring' => 'Last Visit'
			),
			'Shared' => array(
				'type' => 'Boolean',
				'required' => true
			)
		);

		parent::__construct($params);
	}
	
	public function markLastVisit() {
		$this->LastVisit = time();
		DB::getInstance('MySql')->update($this);
	}
	
	public function encryptPassword($password, $username = null) {
		global $init;
		return md5($password . $init->getProp('Password_Salt') . ($username != null ? $username : $this->Username));
	}
	 
	public function getTableName()
	{
		return "User";
	}
	 
	public function __toString()
	{
		return $this->Username;
	}
}
?>