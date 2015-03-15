<?php
class ScheduledEventException extends AbstractDBObject
{
	public function __construct($params = array())
	{
		$this->addColumns(array(
			'Id' => array(
				'type' => 'PrimaryKey'
			),
			'ScheduledEventId' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'ScheduledEvent',
				'required' => true
			),
			'ScheduledEvent' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'Scheduled',
				'localcolumn' => 'ScheduledEventId',
				'foreigncolumn' => 'Id'
			),
			'ExceptionDate' => array(
				'type' => 'Date',
				'required' => true
			)
		));
		
		parent::__construct($params);
	}
	
	public function getTableColumnPrefix() {
		return 'see_';
	}
	 
	public function getTableName()
	{
		return 'ScheduledEventException';
	}
	 
	public function __toString()
	{
		return 'ScheduledEventException #'.$this->Id;
	}
}
?>