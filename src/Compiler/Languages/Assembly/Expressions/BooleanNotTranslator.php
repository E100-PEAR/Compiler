<?php namespace Compiler\Languages\Assembly\Expressions;

use Compiler\Languages\Translator;

class BooleanNotTranslator extends Translator {

	public function translate($token)
	{
		$return = 'a'.spl_object_hash($token);
		$expression = 'a'.spl_object_hash($token->expr);

		$this->compiler->compile($token->expr);

		$this->language->variables->create($expression);

		$this->language->addCommand('not', $return, $expression);	
	}
}