<?php namespace Compiler\Languages\Assembly\Expressions;

use Compiler\Languages\Translator;

class GreaterTranslator extends Translator {

	public function translate($token)
	{
		$left = $this->language->expressionToMemory($token->left);
		$right = $this->language->expressionToMemory($token->right);
		
		$hash = 'a'.spl_object_hash($token);

		$if = $hash.'0';
		$finish  = $hash.'1';
		$greater = $hash.'2';

		// Since e100 has no greater operator, we will reverse the left
		// and right side and do a less than.
		$this->language->addCommand('blt', $greater, $right, $left);

		// The left side was less than the right side. Set the value to zero and redirect
		// back to the if statement.
		$this->language->addCommand('cp', $hash, '_int_1');
		$this->language->addCommand('be', $if, '_int_1', '_int_1');

		// The left side was greater than the right side. Set the value to one and let
		// the PC fall through.
		$this->language->addCommand($greater.' cp', $hash, '_int_1');
	}
}