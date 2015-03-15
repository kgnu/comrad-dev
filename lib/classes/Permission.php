<?php

################################################################################
# OBJECT:       Permission                                                     #
# AUTHOR:       Bryan C. Callahan (12/01/2008)                                 #
# DESCRIPTION:  Represents a permission for something that can be: viewed,     #
#               created, modified, and/or removed.                             #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2008/12/01 (BCC) - <Revision Template>
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

final class Permission
{

################################################################################
# PRIVATE MEMBER VARIABLES                                                     #

	private $name;				// Name of the permission (or key)

	private $v;					// In PHP: 0 = false, 1 = true
	private $c;
	private $m;
	private $r;

#                                                                           [X]#
################################################################################
# PUBLIC FUNCTIONS                                                             #
	
	////////////////////////////////////////////////////////////////////////////
	// Default constructor...
	public function __construct($mode = '')
	{
		$this->setMode($mode);
	}
	
	////////////////////////////////////////////////////////////////////////////
	// ToString (Human readable representation of this object)...
	public function __toString()
	{
		return $this->getMode();
	}

	////////////////////////////////////////////////////////////////////////////
	// Simple accessor functions...
	public function getName() { return $this->name; }
	public function has($mode) { return ($mode == $this->mode); }
	public function hasView() { return $this->v; }
	public function hasCreate() { return $this->c; }
	public function hasModify() { return $this->m; }
	public function hasRemove() { return $this->r; }
	public function hasRainbow() { return $this->v && $this->c && $this->m && $this->r; }

	////////////////////////////////////////////////////////////////////////////
	// Simple mutator functions...
	public function setName($value) { $this->name = $value; }
	public function setView($value) { $this->v = (bool)$value; }
	public function setCreate($value) { $this->c = (bool)$value; }
	public function setModify($value) { $this->m = (bool)$value; }
	public function setRemove($value) { $this->r = (bool)$value; }
	
	////////////////////////////////////////////////////////////////////////////
	// Returns the mode of the permission...
	public function getMode()
	{
		$ret = '';
		if ($this->v) $ret .= 'v'; else $ret .= '-';
		if ($this->c) $ret .= 'c'; else $ret .= '-';
		if ($this->m) $ret .= 'm'; else $ret .= '-';
		if ($this->r) $ret .= 'r'; else $ret .= '-';
		return $ret;
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Sets the mode of the permission...
	public function setMode($mode)
	{
		$this->formatMode($mode);
		$this->v = (substr($mode, 0, 1) == 'v');
		$this->c = (substr($mode, 1, 1) == 'c');
		$this->m = (substr($mode, 2, 1) == 'm');
		$this->r = (substr($mode, 3, 1) == 'r');
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Resets the permission to mode restrictive mode...
	public function reset()
	{
		$this->v = false;
		$this->c = false;
		$this->m = false;
		$this->r = false;
	}
    
	////////////////////////////////////////////////////////////////////////////
	// Formats the permission mode...
	public function formatMode(&$mode)
	{
		// Make the key lowercase...
		$mode  = strtolower($mode);
		
		// Make sure we're four chars long...
		if (strlen($mode) != 4) $mode = '----';
		
		// Partition attributes...
		$v = substr($mode, 0, 1);
		$c = substr($mode, 1, 1);
		$m = substr($mode, 2, 1);
		$r = substr($mode, 3, 1);

		// Validate the attributes...
		if ( ($v != '-') && ($v != 'v') ) $v = '-';
		if ( ($c != '-') && ($c != 'c') ) $c = '-';
		if ( ($m != '-') && ($m != 'm') ) $m = '-';
		if ( ($r != '-') && ($r != 'r') ) $r = '-';

		// If create, modify, and/or remove are set, view must be set...
		if ( ($c == 'c') || ($m == 'm') || ($r == 'r') ) $v = 'v';

		// Reassemble attributes...
		$mode = $v . $c . $m . $r;
	}

#                                                                           [X]#
################################################################################

}

?>
