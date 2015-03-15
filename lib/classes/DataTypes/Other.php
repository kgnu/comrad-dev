<?php
class Other extends AbstractDBObject
{
	public function __construct($params = array())
	{
		$this->columns = array(
			'UID' => array(
				'type' => 'PrimaryKey'
			),
			'Name' => array(
				'type' => 'UppercaseString',
				'required' => true,
				'titlecolumn' => true
			),
			'Description' => array(
				'type' => 'String',
				'required' => true
			),
			'Active' => array(
				'type' => 'Boolean',
				'required' => true,
				'default' => true
			)
		);

		parent::__construct($params);
	}
	 
	public function getTableName()
	{
		return "Other";
	}
	 
	public function __toString()
	{
		return $this->Name;
	}
}
?>
