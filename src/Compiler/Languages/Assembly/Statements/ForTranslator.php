<?php namespace Compiler\Languages\Assembly\Statements;

use Compiler\Languages\Translator;

class ForTranslator extends Translator {

	public function translate($token)
	{
		$hash = spl_object_hash($token);
		$this->compiler->compile($token->init);
	}
}