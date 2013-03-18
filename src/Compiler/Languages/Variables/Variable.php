<?php namespace Compiler\Languages\Variables

class Variable {

	public $name;
	public $value;

	public function __construct($name, $value = 0)
	{
		$this->name = $name;
		$this->value = $value;
	}

	// abstract public function __toString();
	public function __toString()
	{
		return $this->name . ' .data ' . $this->value . PHP_EOL;
	}
}