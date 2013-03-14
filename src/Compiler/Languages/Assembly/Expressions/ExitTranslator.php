<?php namespace Compiler\Languages\Assembly\Expressions;

use Compiler\Languages\Translator;

class ExitTranslator extends Translator {

	public function translate($token)
	{
		$this->language->addCommand('halt');
	}
}