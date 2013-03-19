<?php namespace Compiler\Languages\Assembly\Expressions;

use Compiler\Languages\Translator;

class SmallerTranslator extends Translator {

	public function translate($token)
	{
		$left = $this->language->expressionToMemory($token->left);
		$right = $this->language->expressionToMemory($token->right);
		
		$hash = $this->getHash($token);

		$smaller = $hash .'0';
		$end  = $hash.'1';

		// Check the condition.
		$this->language->addCommand('blt', $smaller, $left, $right);

		// The condition failed, set the value to zero.
		$this->language->addCommand('cp', $hash, '_int_0');
		$this->language->redirectTo($end);

		// The left side was smaller than the right side.
		// Set the value to one and let the PC fall through.
		$this->language->addCommand($smaller.' cp', $hash, '_int_1');

		$this->language->addMarker($end);
	}

	public function getHash($token)
	{
		return 'a'.spl_object_hash($token);
	}
}