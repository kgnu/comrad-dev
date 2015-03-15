<?php
// See calendarview.php?cmd=ajaxPopulateEvents
final class EventSearch extends AbstractEventsConnector {
  private $start;
  private $end;
  
  private $results;
  
  public function __construct($start, $end, &$connection_owner = NULL) {
    // Call parent constructor to create the connection...
    parent::__construct($connection_owner);
    
    global $init;
    
    $this->start = $start;
    $this->end = $end;
    
    $this->doQuery();
  }
  
  private function doQuery() {
    // Need to expand this to query:
    //  - Recurring shows
    //  - Recurring show events
    //  - Recurring events
    //  - Show Instances
    //  - Show Event Instances
    //  - Event Instances
    // ...between $this->start and $this->end
    //
    // All instances should overwrite recurrences
    //
    // Store the results in $this->results
    
    $this->results = array();
    
    // Event Object reference here: http://arshaw.com/fullcalendar/docs/event_data/Event_Object/
    array_push($this->results, array(
      "title" => "Test Event!",
      "start" => date('U'),
      "allDay" => false,
      "customField" => "donkey")); // Can add custom fields to events, we can use this to differentiate between shows & events
  }
 
  public function getResults() {
    return $this->results;
  }
}

?>
