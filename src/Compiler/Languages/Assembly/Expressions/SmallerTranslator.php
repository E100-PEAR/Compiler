<?php namespace Compiler\Languages\Assembly\Expressions;

use Compiler\Languages\Translator;

class SmallerTranslator extends Translator {

	public function translate($token)
	{
		$left = $this->language->expressionToMemory($token->left);
		$right = $this->language->expressionToMemory($token->right);
		
		$hash = spl_object_hash($token);

		$smaller = $hash .'0';
		$end  = $hash.'1';

		// Check the condition.
		$this->language->addCommand('blt', $smaller, $left, $right);

		// The condition failed, set the value to zero.
		$this->language->addCommand('cp', $hash, 'zero');
		$this->language->redirectTo($end);

		// The left side was smaller than the right side.
		// Set the value to one and let the PC fall through.
		$this->language->addCommand($smaller.' cp', $hash, 'one');

		$this->language->addMarker($end);
	}
}