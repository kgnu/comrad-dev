<?php
class VoiceBreak extends FloatingShowElement
{
	public function __construct($params = array())
	{
		$this->DISCRIMINATOR = 'VoiceBreak';
		
		parent::__construct($params);
	}
	
	public function __toString()
	{
		return 'VoiceBreak #'.$this->Id;
	}
}
?>