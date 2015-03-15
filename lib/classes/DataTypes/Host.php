<?php
class Host extends AbstractDBObject
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
			'Internal' => array(
				'type' => 'Boolean',
				'tostring' => 'Internal?',
				'required' => true,
				'default' => true
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
		return "Host";
	}
	 
	public function __toString()
	{
		return $this->Name;
	}
}
?>