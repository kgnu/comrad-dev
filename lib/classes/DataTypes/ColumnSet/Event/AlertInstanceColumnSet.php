<?php
class AlertInstanceColumnSet extends AbstractColumnSet
{
	public function __construct()
	{
		parent::__construct();
		
		$this->addColumns(array(
			'Copy' => array(
				'type' => 'ShortString',
				'showinform' => true
			)
		));
	}
}
?>
