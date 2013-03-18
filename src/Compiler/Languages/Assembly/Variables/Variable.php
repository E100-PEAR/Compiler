<?php namespace Compiler\Languages\Assembly\Variables;

use Compiler\Languages\Variables\Variable as LanguageVariable;

class Variable extends LanguageVariable {

	/**
	 * Render the array.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->name . ' .data ' . $this->value . PHP_EOL;
	}
}