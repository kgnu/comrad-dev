<?php
class EASTestEvent extends Event
{
	public function __construct($params = array())
	{
		$this->DISCRIMINATOR = 'EASTestEvent';
		
		parent::__construct($params);
	}
}
?>