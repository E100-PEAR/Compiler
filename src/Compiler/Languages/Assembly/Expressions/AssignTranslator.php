<?php namespace Compiler\Languages\Assembly\Expressions;

use PHPParser_Node_Name;
use PHPParser_Node_Expr_Array;
use PHPParser_Node_Expr_Variable;
use PHPParser_Node_Scalar_String;
use PHPParser_Node_Expr_FuncCall;
use PHPParser_Node_Scalar_LNumber;
use PHPParser_Node_Expr_ConstFetch;
use PHPParser_Node_Expr_ArrayDimFetch;

use Compiler\Languages\Translator;

class AssignTranslator extends Translator {

	/**
	 * The operators that can happen during the assignment.
	 *
	 * @var array
	 */
	protected $operations = array(
		'PHPParser_Node_Expr_Plus',
		'PHPParser_Node_Expr_Minus',
		'PHPParser_Node_Expr_Mul',
		'PHPParser_Node_Expr_Div',
	);

	const ARRAY_ASSIGNMENT = 0;
	const FUNCTION_ASSIGNMENT = 1;
	const ARRAY_ELEMENT_ASSIGNMENT = 2;
	const VARIABLE_ASSIGNMENT = 3;

	/**
	 * Translate the token. Note that any side-effects will
	 * be sent to the language object.
	 *
	 * @param  mixed  $token
	 * @return void
	 */
	public function translate($token)
	{
		$type = $this->getAssignmentType($token);

		if($type == static::ARRAY_ASSIGNMENT)
		{
			$this->handleArrayAssignment($token);
		}

		elseif($type == static::FUNCTION_ASSIGNMENT)
		{
			$this->handleFunctionAssignment($token);
		}

		elseif($type == static::ARRAY_ELEMENT_ASSIGNMENT)
		{
			$this->handleElementAssignment($token);
		}

		elseif($type == static::VARIABLE_ASSIGNMENT)
		{
			$this->handleVariableAssignment($token);
		}
	}

	/**
	 * Get the type of the assignment operation.
	 *
	 * @param  mixed  $token
	 * @return int
	 */
	public function getAssignmentType($token)
	{
		if($token->expr instanceof PHPParser_node_Expr_Array)
		{
			return static::ARRAY_ASSIGNMENT;
		}

		if($token->expr instanceof PHPParser_Node_Expr_FuncCall)
		{
			return static::FUNCTION_ASSIGNMENT;
		}

		if($token->var instanceof PHPParser_Node_Expr_ArrayDimFetch)
		{
			return static::ARRAY_ELEMENT_ASSIGNMENT;
		}

		return static::VARIABLE_ASSIGNMENT;
	}

	/**
	 * Handle array assignments.
	 *
	 * @param  string  $token
	 * @return void
	 */
	public function handleArrayAssignment($token)
	{
		// Are we assigning an array to the variable?
		if($token->expr instanceof PHPParser_Node_Expr_Array)
		{
			$arrayName = $token->var->name;
			
			// We'll create an empty array now. Otherwise, the array would not be
			// created if this assignment did not have any items to assign to memory.
			$array = $this->language->variables->createArray($arrayName);

			$offset = 0;
			$usedOffsets = array();

			// Add each item to the array.
			foreach($token->expr->items as $element)
			{
				// If the element's key isn't null we will use that key.
				if( ! is_null($element->key))
				{
					$key = $this->language->expressionToMemory($element->key);

					$usedOffsets[] = $element->key->value;
				}

				// Otherwise, let's find an unused offset and assign the value to
				// that offset.
				else
				{
					// We will increment the offset by one until we hit a fresh offset.
					while(in_array($offset, $usedOffsets))
					{
						$offset++;
					}

					$key = $this->language->expressionToMemory($offset);

					$usedOffsets[] = $offset;
				}

				// Fetch the element's value's label.
				$value = $this->language->expressionToMemory($element->value);

				// Make sure that the array has allocated memory for the
				// element.
				if( ! $array->exists($key))
				{
					$array->set($key, 0);
				}

				// Set the value of the element to the array.
				$this->language->addCommand('cpta', $value, $arrayName, $key);
			}
		}
	}

	/**
	 * Assign to a variable the output of a function.
	 *
	 * @param  mixed  $token
	 * @return void
	 */
	public function handleFunctionAssignment($token)
	{
		$hash = 'a'.spl_object_hash($token->expr);

		$this->compiler->compile($token->expr);

		$this->language->variables->create($hash, 0);
		$this->language->addCommand('cp', $token->var->name, $hash);
	}

	/**
	 * Handle assigning a value to a specific element from an array.
	 *
	 * @param  mixed  $token
	 * @return void
	 */
	public function handleElementAssignment($token)
	{
		$array = $token->var->var->name;
		$offset = $token->var->dim->value;

		// Make sure the array has allocated memory for the element.
		$this->language->variables->get($array)->set($offset, 0);

		// Convert the array offset and element value into valid assembly
		// labels.
		$offset = $this->language->expressionToMemory($offset);
		$value = $this->fetchValue($token);

		// And lastly, save the element's value to the array.
		$this->language->addCommand('cpta', $value, $array, $offset);
	}

	/**
	 * Assign a variable to a variable... variable-ception.
	 *
	 * @param  mixed  $token
	 * @return void
	 */
	public function handleVariableAssignment($token)
	{
		$this->language->variables->create($token->var->name);

		$this->language->addCommand('cp', $token->var->name, $this->fetchValue($token));
	}

	/** 
	 * Fetch the value of what will be assigned.
	 *
	 * @param  mixed  $token
	 * @return void
	 */
	public function fetchValue($token)
	{
		// If the value is just a variable we don't need to do anything fancy.
		if($token->expr instanceof PHPParser_Node_Expr_Variable)
		{
			return $token->expr->name;
		}

		// Handle basic mathetmatical operations.
		$tokenExpressionClass = get_class($token->expr);

		foreach($this->operations as $operation)
		{
			if($tokenExpressionClass == $operation)
			{
				$operatorReturnValue = 'a'.spl_object_hash($token->expr);

				$this->language->variables->create($operatorReturnValue);

				$this->compiler->compile(array($token->expr));

				return $operatorReturnValue;
			}
		}

		// If we're here, just blindly convert the expression into its matching
		// label in the memory.
		return $this->language->expressionToMemory($token->expr);
	}
}