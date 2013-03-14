<?php namespace Compiler\Languages\Assembly\Expressions;

use Compiler\Languages\Translator;

class EqualTranslator extends Translator {

	public function translate($token)
	{
		$left = $this->language->expressionToMemory($token->left);
		$right = $this->language->expressionToMemory($token->right);
		
		$hash = spl_object_hash($token);

		$if = $hash.'a';
		$finish = $hash.'b';
		$match = $hash.'c';

		// Check if the two values match. If they do, redirect the PC
		// to the branch if they match. Otherwise, just let if fall through.
		$this->language->addCommand('be', $match, $left, $right);

		// The two values did not match. Set the value to zero and redirect
		// back to the if statement.
		$this->language->addCommand('cp', $hash, 'zero');
		$this->language->addCommand('be', $if, 'one', 'one');

		// The two values did match. Set the value to one and let
		// the PC fall through.
		$this->language->addCommand($match.' cp', $hash, 'one');
	}
}