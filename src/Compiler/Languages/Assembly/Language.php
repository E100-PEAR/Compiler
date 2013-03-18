<?php namespace Compiler\Languages\Assembly;

use PHPParser_Node_Name;
use PHPParser_Node_Expr_Variable;
use PHPParser_Node_Scalar_String;
use PHPParser_Node_Scalar_LNumber;
use PHPParser_Node_Expr_ConstFetch;

class Language {

	protected $commands = array();
	public $variables = array();

	public $scope = array();

	// The compiler uses zero's and one's so
	// we'll always need to include those.
	public $largestInteger = 1;

	public function __construct($variables)
	{
		$this->variables = $variables;
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
				$value = '_int_1';
			}

			else
			{
				$value = '_int_0';
			}
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
			$value = '_int_'.$expression->value;

			if($expression->value > $this->largestInteger)
			{
				$this->largestInteger = $expression->value;
			}
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
		$this->addCommand('be', $location, '_int_1', '_int_1');
	}

	public function addMarker($name)
	{
		$this->addCommand($name.' cp', '_int_0', '_int_0');
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
		$output .= $this->variables . PHP_EOL;

		for($i = 0; $i <= $this->largestInteger; $i++)
		{
			$output .= '_int_'.$i.' .data ' . $i.PHP_EOL;
		}

		return $output;
	}

	public function setScope($scope)
	{
		$this->addMarker($scope.'_start');
	}

	public function removeScope($scope)
	{
		$this->addMarker($scope.'_end');
	}
}