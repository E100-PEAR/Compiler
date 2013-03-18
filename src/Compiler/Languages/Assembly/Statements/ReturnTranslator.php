<?php namespace Compiler\Languages\Assembly\Statements;

use Compiler\Languages\Translator;

class ReturnTranslator extends Translator {

	public function translate($token)
	{
		if( ! is_null($token->expr))
		{
			$this->language->addCommand('cp', 'function_return_value', $token->expr->name);
		}

		$this->language->redirectTo('function_redirect_address');
	}
}