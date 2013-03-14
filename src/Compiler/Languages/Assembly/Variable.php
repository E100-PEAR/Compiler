<?php namespace Compiler\Languages\Assembly;

class Variable {

	public $value;

	public function __construct($value)
	{
		$this->value = $value;
	}
}