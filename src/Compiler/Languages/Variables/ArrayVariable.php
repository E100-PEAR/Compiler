<?php namespace Compiler\Languages\Variables;

class ArrayVariable {

	/**
	 * The name of the array.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The array's values.
	 *
	 * @var array
	 */
	public $values;

	/**
	 * Create a new array.
	 *
	 * @param  string  $name
	 * @param  array   $values
	 */
	public function __construct($name, array $values = array())
	{
		$this->name = $name;
		$this->values = $values;
	}

	/**
	 * Add a new element to the end of the array.
	 *
	 * @param  mixed  $value
	 * @return void
	 */
	public function createElement($value = 0)
	{
		$this->values[] = $value;
	}

	/**
	 * Set the value to an element.
	 *
	 * @param  int     $key
	 * @param  string  $value
	 * @return void
	 */
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

	/**
	 * Render the array.
	 *
	 * @return string
	 */
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