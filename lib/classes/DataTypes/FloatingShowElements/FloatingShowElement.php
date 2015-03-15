<?php
class FloatingShowElement extends AbstractDBObject
{
	public function __construct($params = array())
	{
		$this->useDiscriminator(array(
			'TrackPlay',
			'DJComment',
			'VoiceBreak',
			'FloatingShowEvent'
		));
		
		$this->addColumns(array(
			'Id' => array(
				'type' => 'PrimaryKey'
			),
			'ScheduledShowInstanceId' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'ScheduledShowInstance',
				'required' => true
			),
			'ScheduledShowInstance' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'ScheduledShowInstance',
				'localcolumn' => 'ScheduledShowInstanceId',
				'foreigncolumn' => 'Id'
			),
			'StartDateTime' => array(
			    'type' => 'Date',
			    'required' => true
			),
			'Executed' => array(
				'type' => 'Date'
			)
		));
		
		parent::__construct($params);
	}
	 
	public function getTableName()
	{
		return 'FloatingShowElement';
	}
	
	public function getTableColumnPrefix() {
		return 'fse_';
	}
	 
	public function __toString()
	{
		return 'FloatingShowElement #'.$this->Id;
	}
}
?>