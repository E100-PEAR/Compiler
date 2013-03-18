<?php namespace Compiler\Languages\Variables;

class ArrayVariable {

	public $name;
	public $values;

	public function __construct($name, array $values = array())
	{
		$this->name = $name;
		$this->values = $values;
	}

	public function createElement($value = 0)
	{
		$this->values[] = $value;
	}

	public function set($key, $value)
	{
		// We'll need to create empty elements if the key we're
		// setting to is higher than all of the other elements' keys.
		while(count($this->values) < $key)
		{
			$this->createElement();
		}

		$this->values[$key] = $value;
	}

	public function __toString()
	{
		$output = "";

		// Do a little fancy spacing so that everything aligns neatly
		// with the array's name tag.
		$tab = str_pad($output, strlen($this->name), ' ', STR_PAD_LEFT);

		$first = true;

		foreach($this->values as $value)
		{
			if($first == true)
			{
				$first = false;

				$output = $this->name . ' .data ' . $value . PHP_EOL;
			}

			else
			{
				$output .= $tab.' .data ' . $value . PHP_EOL;
			}
		}

		return $output;
	}
}