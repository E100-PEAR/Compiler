<?php namespace Compiler\Languages\Assembly\Commands;

class Command {

	/**
	 * The type of the command.
	 *
	 * @var string
	 */
	protected $type = null;

	/**
	 * The command's internal label.
	 *
	 * @var string
	 */
	protected $label = null;

	/**
	 * The command's parameters.
	 *
	 * @var array
	 */
	protected $parameters = array();

	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * Set the command's label.
	 *
	 * @param  string  $label
	 * @return Compiler\Languages\Assembly\Commands\Command
	 */
	public function setLabel($label)
	{
		$this->label = $label;

		return $this;
	}

	/**
	 * Set the command's parameters.
	 *
	 * @param  mixed  $one
	 * @param  mixed  $two
	 * @param  mixed  $three
	 * @return Compiler\Languages\Assembly\Commands\Command
	 */
	public function setParameters($one = null, $two = null, $three = null)
	{
		$offset = 0;

		foreach(compact('one', 'two', 'three') as $parameter)
		{
			$this->parameters[$offset] = $parameter;

			$offset++;
		}

		return $this;
	}

	/**
	 * Convert the command into a string.
	 *
	 * @return string
	 */
	public function __toString()
	{
		$output = '';

		// If there's a label, use it. Otherwise, add a tab.
		if( ! is_null($this->label))
		{
			$output = $this->label . ' ';
		}

		else
		{
			$output = "\t";
		}

		$output .= $this->type;

		foreach($this->parameters as $parameter)
		{
			$output .= ' ';
			$output .= $parameter;
		}

		return $output.PHP_EOL;
	}
}