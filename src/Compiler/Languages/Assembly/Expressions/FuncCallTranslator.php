<?php namespace Compiler\Languages\Assembly\Expressions;

use Compiler\Languages\Translator;

class FuncCallTranslator extends Translator {

	public function translate($expression)
	{
		$name = $expression->name->parts[0];

		if($name == 'set_port')
		{
			$this->handleSetPort($expression);
		}

		elseif($name == 'get_port')
		{
			$this->handleGetPort($expression);
		}

		else
		{
			$this->handleFunctionCall($expression);
		}
	}

	public function handleSetPort($expression)
	{
		$port = $expression->args[0]->value->value;
		$value = $expression->args[1]->value;

		$value = $this->language->expressionToMemory($value);

		$this->language->addCommand('out', $port, $value);
	}

	public function handleGetPort($expression)
	{
		$port = $expression->args[0]->value->value;

		$this->language->addCommand('in', $port, 'function_get_port_return_value');
	}

	public function handleFunctionCall($expression)
	{
		$hash = 'a'.spl_object_hash($expression);
		$name = $expression->name->parts[0];

		$marker = 'function_'.$name;
		$scope = $marker.'_';

		foreach($expression->args as $key => $argument)
		{
			$this->language->variables->create($scope.$key);

			$this->language->addCommand('cp', $scope.$key, $argument->value->name);
		}

		$this->language->addCommand('cp', $marker.'_return_address', $hash);

		$this->language->redirectTo($marker);
		$this->language->addMarker($hash);
	}
}