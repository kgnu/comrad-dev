<?php
class LegalIdColumnSet extends EventColumnSet
{
	public function __construct()
	{
		parent::__construct();
		
		$this->addColumns(array(
			'Copy' => array(
				'type' => 'ShortString',
				'required' => true
			)
		));
	}
}
?>
