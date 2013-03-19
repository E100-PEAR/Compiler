<?php namespace Compiler\Languages\Assembly\Expressions;

use Compiler\Languages\Translator;

class DivTranslator extends Translator {

	public function translate($expression)
	{
		$left  = $this->language->expressionToMemory($expression->left);
		$right = $this->language->expressionToMemory($expression->right);

		$hash = 'a'.spl_object_hash($expression);

		$this->language->addCommand('div', $hash, $left, $right);

		return $hash;
	}
}