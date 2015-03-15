<?php
class Venue extends AbstractDBObject
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
			'Location' => array(
				'type' => 'ShortString',
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
		return "Venue";
	}
	 
	public function __toString()
	{
		return $this->Location;
	}
}
?>
