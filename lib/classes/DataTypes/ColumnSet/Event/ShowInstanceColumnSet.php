<?php
class ShowInstanceColumnSet extends AbstractColumnSet
{
	public function __construct()
	{
		global $init;
		
		parent::__construct();
		
		$this->addColumns(array(
			'HostId' => array(
				'type' => 'ForeignKey',
				'tostring' => 'Host',
				'foreignType' => 'Host',
				'showinform' => true
			),
			'Host' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'Host',
				'localcolumn' => 'HostId',
				'foreigncolumn' => 'UID'
			),
			'ShortDescription' => array(
				'type' => 'ShortString',
				'tostring' => 'Short Description',
				'showinform' => true,
				'validators' => array(
					new StringLengthValidator(array('max' => $init->getProp('ShowShortDescriptionMaxLength')), 'Short Description must be ${max} characters or less')
				)
			),
			'LongDescription' => array(
				'type' => 'String',
				'tostring' => 'Long Description',
				'showinform' => true
			),
			'RecordedFileName' => array(
				'type' => 'String'
			)
		));
	}
}
?>
