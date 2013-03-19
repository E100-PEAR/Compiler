<?php namespace Compiler\Languages\Assembly;

use PHPParser_Node_Name;
use PHPParser_Node_Expr_Variable;
use PHPParser_Node_Scalar_String;
use PHPParser_Node_Scalar_LNumber;
use PHPParser_Node_Expr_ConstFetch;

use Compiler\Languages\Language as CompilerLanguage;
use Compiler\Languages\Assembly\Commands\Command;

class Language extends CompilerLanguage {

	/**
	 * The commands extracted from the AST.
	 *
	 * @var array
	 */
	protected $commands = array();

	/**
	 * The current scope of the language.
	 *
	 * @todo implement
	 * @var  array
	 */
	public $scope = array();

	/**
	 * The largest integer that the compiler should generate.
	 * Note that 0 and 1 are always generated to be used as booleans.
	 *
	 * @var int
	 */
	public $largestInteger = 1;

	/**
	 * Get the instance of the language's token translator.
	 *
	 * @return Compiler\Languges\Translator
	 */
	public function getTranslator($translator)
	{
		$translator = 'Compiler\\Languages\\Assembly\\'.$translator.'Translator';

		return new $translator($this->compiler, $this);
	}

	/**
	 * Add a new compiled command.
	 *
	 * @param  string  $type
	 * @param  mixed   $param1
	 * @param  mixed   $param2
	 * @param  mixed   $param3
	 * @return Compiler\Languages\Assembly\Commands\Command
	 */
	public function addCommand($name, $param1 = null, $param2 = null, $param3 = null)
	{
		$label = null;

		if(strpos($name, ' ') !== false)
		{
			list($label, $name) = explode(' ', $name);
		}

		$command = new Command;

		$command->setType($name);
		$command->setParameters($param1, $param2, $param3);
		$command->setLabel($label);

		$this->commands[] = $command;

		return $command;
	}

	/**
	 * Convert an expression class into its corresponding memory label.
	 *
	 * @param  mixed   $expression
	 * @return string
	 */
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
		elseif($expression instanceof PHPParser_Node_Scalar_LNumber or is_int($expression))
		{
			// We'll play nice and allow scalar integers to be converted even though
			// they're not really an expression.
			$integer = (is_int($expression)) ? $expression : $expression->value;

			$value = '_int_'.$integer;

			if($integer > $this->largestInteger)
			{
				$this->largestInteger = $integer;
			}
		}

		else
		{
			$type = get_class($expression);

			throw new \Compiler\Error("Assigning unknown type [$type] to variable.");
		}

		return $value;
	}

	/** 
	 * Create a command that will redirect the PC to a new location.
	 *
	 * @param  string  $location
	 * @return Compiler\Languages\Assembly\Commands\Command
	 */
	public function redirectTo($location)
	{
		return $this->addCommand('be', $location, '_int_1', '_int_1');
	}

	/**
	 * Add a useless labeled command that will serve as a marker. This marker
	 * can be used as a location to redirect the PC to.
	 *
	 * @param  string  $name
	 * @return Compiler\Languages\Assembly\Commands\Command
	 */
	public function addMarker($name)
	{
		return $this->addCommand($name.' cp', '_int_0', '_int_0');
	}

	/**
	 * Set the current scope.
	 *
	 * @todo   Make this actually do something!
	 * @param  string  $scope
	 * @return void
	 */
	public function setScope($scope)
	{
		$this->addMarker($scope.'_start');
	}

	/**
	 * Remove a scope.
	 *
	 * @todo   Make this actually do something!
	 * @param  string  $scope
	 * @return void
	 */
	public function removeScope($scope)
	{
		$this->addMarker($scope.'_end');
	}

	/**
	 * Get the final translation of the parsed code.
	 *
	 * @return string
	 */
	public function getTranslation()
	{
		$output = '';

		foreach($this->commands as $command)
		{
			$output .= $command;
		}

		$output .= PHP_EOL;
		$output .= 'halt' . PHP_EOL.PHP_EOL;

		$output .= $this->variables . PHP_EOL;

		for($i = 0; $i <= $this->largestInteger; $i++)
		{
			$output .= '_int_'.$i.' .data ' . $i.PHP_EOL;
		}

		return $output;
	}
}