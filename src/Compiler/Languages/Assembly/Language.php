<?php namespace Compiler\Languages\Assembly;

use PHPParser_Node_Name;
use PHPParser_Node_Expr_Variable;
use PHPParser_Node_Scalar_String;
use PHPParser_Node_Scalar_LNumber;
use PHPParser_Node_Expr_ConstFetch;

class Language {

	protected $commands = array();
	public $variables = array();

	public function __construct()
	{
		$this->variables = new Variables;
	}

	public function get($compiler, $translator)
	{
		$translator = 'Compiler\\Languages\\Assembly\\'.$translator.'Translator';

		return new $translator($compiler, $this);
	}

	public function addCommand($name, $param1 = null, $param2 = null, $param3 = null, $param4 = null)
	{
		$tag = "\t";

		if(strpos($name, ' ') !== false)
		{
			list($tag, $name) = explode(' ', $name);
		}

		$this->commands[] = array(
			'tag' => $tag,
			'name' => $name,
			'parameters' => array(
				$param1,
				$param2,
				$param3,
				$param4,
			),
		);
	}

	public function expressionToMemory($expression)
	{
		// Variables 
		if($expression instanceof PHPParser_Node_Expr_Variable)
		{
			$value = $expression->name;

			return $value;
		}

		// Array

		// Booleans.
		if($expression instanceof PHPParser_Node_Expr_ConstFetch)
		{
			if($expression->name->parts[0] == 'true')
			{
				$value = true;
			}

			else
			{
				$value = false;
			}

			// Todo: handle like integers.
			$value = (int) $value;
		}

		// Strings.
		elseif($expression instanceof PHPParser_Node_Scalar_String)
		{
			$value = $expression->value;

			throw new \Compiler\Error("Strings are not supported yet.");
		}

		// Integers
		elseif($expression instanceof PHPParser_Node_Scalar_LNumber)
		{
			$value = $expression->value;
		}

		else
		{
			$type = get_class($expression);

			throw new \Compiler\Error("Assigning unknown type [$type] to variable.");
		}

		return $value;
	}

	public function redirectTo($location)
	{
		$this->addCommand('be', $location, 'one', 'one');
	}

	public function addMarker($name)
	{
		$this->addCommand($name.' cp', 'zero', 'zero');
	}

	public function getMemoryLocationName($input)
	{
		return $input;
	}

	public function getTranslation()
	{
		$output = '';

		foreach($this->commands as $command)
		{
			$output .= $command['tag'].' ';
			$output .= $command['name'];

			foreach($command['parameters'] as $param)
			{
				if( ! is_null($param))
				{
					$output .= ' ' . $param;
				}
			}

			$output .= PHP_EOL;
		}

		$output .= PHP_EOL;

		foreach($this->variables->get() as $key => $value)
		{
			$output .= $key . '  .data ' . $value . PHP_EOL;
		}

		foreach($this->variables->getArrays() as $name => $items)
		{
			$output .= $name . ' .data 0' . PHP_EOL;

			foreach($items as $key => $value)
			{
				$output .= "{$name}_{$key} .data $value" . PHP_EOL;
			}
		}

		return $output;
	}
}