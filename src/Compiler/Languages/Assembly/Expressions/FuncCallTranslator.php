<?php namespace Compiler\Languages\Assembly\Expressions;

use Compiler\Languages\Translator;

class FuncCallTranslator extends Translator {

	public function translate($expression)
	{
		$hash = spl_object_hash($expression);

		$name = $expression->name->parts[0];

		$marker = 'function_'.$name;
		$scope = $marker.'_';

		foreach($expression->args as $key => $argument)
		{
			$this->language->variables->create($scope.$key);

			$this->language->addCommand('cp', $scope.$key, $argument->value->name);
		}

		$this->language->variables->create($marker.'_ra');
		$this->language->variables->create($marker.'_return');

		$this->language->addCommand('cp', $marker.'_ra', $hash);

		$this->language->redirectTo($marker);
		$this->language->addMarker($hash);
	}
}