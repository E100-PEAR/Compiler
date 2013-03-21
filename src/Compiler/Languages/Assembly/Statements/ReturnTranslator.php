<?php namespace Compiler\Languages\Assembly\Statements;

use Compiler\Languages\Translator;

class ReturnTranslator extends Translator {

	public function translate($token)
	{
		$name = $this->language->currentCall();

		if( ! is_null($token->expr))
		{
			$this->language->addCommand('cp', 'function_'.$name.'_return_value', $token->expr->name);
		}

		$this->language->redirectTo('function_'.$name.'_return_address');
	}
}