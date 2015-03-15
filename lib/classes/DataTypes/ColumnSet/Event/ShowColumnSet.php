<?php
class ShowColumnSet extends EventColumnSet
{
	public function __construct()
	{
		global $init;
		
		parent::__construct();
		
		$this->addColumns(array(
			'HostId' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'Host',
				'tostring' => 'Host'
			),
			'Host' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'Host',
				'localcolumn' => 'HostId',
				'foreigncolumn' => 'UID'
			),
			'HasHost' => array(
				'type' => 'Boolean',
				'required' => true,
				'tostring' => 'Has Host?'
			),
			'RecordAudio' => array(
				'type' => 'Boolean',
				'required' => true,
				'tostring' => 'Record Audio?'
			),
			'URL' => array(
				'type' => 'ShortString',
				'tostring' => 'Show URL'
			),
			'Source' => array(
				'type' => 'Enumeration',
				'possiblevalues' => array(
					'KGNU',
					'Ext'
				),
				'required' => true
			),
			'Category' => array(
				'type' => 'Enumeration',
				'possiblevalues' => array(
					'Announcements',
					'Mix',
					'Music',
					'NewsPA',
					'OurMusic'
				),
				'required' => true
			),
			'Class' => array(
				'type' => 'ShortString',
				'required'=> true
			),
			'ShortDescription' => array(
				'type' => 'ShortString',
				'tostring' => 'Short Description',
				'validators' => array(
					new StringLengthValidator(array('max' => $init->getProp('ShowShortDescriptionMaxLength')), 'Short Description must be ${max} characters or less')
				)
			),
			'LongDescription' => array(
				'type' => 'String',
				'tostring' => 'Long Description'
			)
		));
	}
}
?>
