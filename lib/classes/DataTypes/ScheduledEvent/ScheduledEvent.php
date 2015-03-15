<?php
class ScheduledEvent extends AbstractDBObject
{
	public function __construct($params = array())
	{
		$this->addColumns(array(
			'Id' => array(
				'type' => 'PrimaryKey'
			),
			'EventId' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'Event',
				'required' => true
			),
			'Event' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'Event',
				'localcolumn' => 'EventId',
				'foreigncolumn' => 'Id'
			),
			'TimeInfoId' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'TimeInfo',
				'required' => true
			),
			'TimeInfo' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'TimeInfo',
				'localcolumn' => 'TimeInfoId',
				'foreigncolumn' => 'Id'
			),
			'RecordingOffset' => array(
				'type' => 'Integer'
			)
		));
		
		parent::__construct($params);
	}
	 
	public function getTableName()
	{
		return 'ScheduledEvent';
	}
	
	public function getTableColumnPrefix() {
		return 'se_';
	}
	 
	public function __toString()
	{
		return 'ScheduledEvent #'.$this->Id;
	}
}
?>