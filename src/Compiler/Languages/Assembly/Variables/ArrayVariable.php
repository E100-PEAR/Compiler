<?php namespace Compiler\Languages\Assembly\Variables;

use Compiler\Languages\Variables\ArrayVariable as LanguageArrayVariable;

class ArrayVariable extends LanguageArrayVariable {

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