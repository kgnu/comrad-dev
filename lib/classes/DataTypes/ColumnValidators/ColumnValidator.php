<?php

abstract class ColumnValidator {
	protected $params;
	protected $message;
	
	public function __construct($params = array(), $message = '') {
		$this->params = $params;
		$this->message = $message;
	}
	
	public abstract function isValid($value);
	
	// The message can display values from the $params array by using the ${keyname} format.
	// It's case sensitive, so ${keyname} is not the same as ${keyName}.
	// echo new ColumnValidator(array('keyname' => 'value'), 'The keyname is ${keyname}.')->getMessage();
	// will print "The keyname is 'value'."
	public function getMessage() {
		$message = $this->message;
		
		$search = preg_match_all('/\${[^}]+}/', $message, $matches);
		
		for($i = 0; $i < $search; $i++) {
			$matches[0][$i] = str_replace(array('${', '}'), null, $matches[0][$i]);
		}
		
		foreach($matches[0] as $value) {
			if (array_key_exists($value, $this->params)) $message = str_replace('${' . $value . '}', $this->params[$value], $message);
		}
		
		return $message;
	}
}

?>