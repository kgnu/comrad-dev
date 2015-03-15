<?php
class TicketGiveawayInstanceColumnSet extends EventWithCopyInstanceColumnSet
{
	public function __construct()
	{
		parent::__construct();
		
		$this->addColumns(array(
			'EventDate' => array(
				'type' => 'Date',
				'tostring' => 'Event Date',
				'showinform' => true
			),
			'VenueId' => array(
				'type' => 'Integer',
				'tostring' => 'Venue',
				'showinform' => true
			),
			'WinnerName' => array(
				'type' => 'UppercaseString',
				'tostring' => 'Winner Name',
				'showinform' => true
			),
			'WinnerPhone' => array(
				'type' => 'ShortString',
				'tostring' => 'Winner Phone',
				'showinform' => true
			),
			'TicketType' => array(
				'type' => 'Enumeration',
				'possiblevalues' => array(
					'Hard Ticket',
					'Guest List'
				),
				'showinform' => true
			)
		));
	}
}
?>
