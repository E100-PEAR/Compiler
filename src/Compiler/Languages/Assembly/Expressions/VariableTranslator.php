<?php namespace Compiler\Languages\Assembly\Expressions;

use Compiler\Languages\Translator;

class VariableTranslator extends Translator {

	public function translate($token)
	{
		$hash = 'a'.spl_object_hash($token);

		$if = $hash.'0';
		$finish = $hash.'1';
		$match = $hash.'2';

		// Check if the two values match. If they do, redirect the PC
		// to the branch if they match. Otherwise, just let if fall through.
		$this->language->addCommand('be', $match, $token->name, '_int_1');

		// The two values did not match. Set the value to zero and redirect
		// back to the if statement.
		$this->language->addCommand('cp', $hash, '_int_0');
		$this->language->redirectTo($if);

		// The two values did match. Set the value to one and let
		// the PC fall through.
		$this->language->addCommand($match.' cp', $hash, '_int_1');
	}
}