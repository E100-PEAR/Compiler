<?php namespace Compiler\Languages\Assembly\Expressions;

use Compiler\Languages\Translator;

class PreIncTranslator extends Translator {

	public function translate($expression)
	{
		$left  = $this->language->expressionToMemory($expression->var);

		$this->language->addCommand('add', $left, $left, '_int_1');

		return $left;
	}
}