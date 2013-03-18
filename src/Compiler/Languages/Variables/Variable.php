<?php namespace Compiler\Languages\Variables

class Variable {

	/**
	 * The variable's name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The variable's value.
	 *
	 * @var mixed
	 */
	public $value;

	/**
	 * Create the new variable.
	 *
	 * @param  string  $name
	 * @param  mixed   $value
	 */
	public function __construct($name, $value = 0)
	{
		$this->name = $name;
		$this->value = $value;
	}

	/**
	 * Render the variable.
	 *
	 * @todo Make the class abstract and remove this method.
	 * @return string
	 */
	// abstract public function __toString();
	public function __toString()
	{
		return $this->name . ' .data ' . $this->value . PHP_EOL;
	}
}