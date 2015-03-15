<?php

class StringLengthValidator extends ColumnValidator {
	
	public function isValid($value) {
		if (array_key_exists('max', $this->params) && strlen($value) > $this->params['max']) return false;
		if (array_key_exists('min', $this->params) && strlen($value) < $this->params['min']) return false;
		return true;
	}
	
}

?>