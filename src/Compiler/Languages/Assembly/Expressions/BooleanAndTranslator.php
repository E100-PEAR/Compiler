<?php namespace Compiler\Languages\Assembly\Expressions;

use Compiler\Languages\Translator;

class BooleanAndTranslator extends Translator {

	public function translate($token)
	{
		$hash  = 'a'.spl_object_hash($token);
		$left  = 'a'.spl_object_hash($token->left);
		$right = 'a'.spl_object_hash($token->right);

		$this->compiler->compile($token->left);
		$this->compiler->compile($token->right);

		$this->language->addCommand('and', $hash, $left, $right);
	}
}