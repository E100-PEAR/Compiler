<?php namespace Compiler\Languages\Assembly\Statements;

use Compiler\Languages\Translator;

class WhileTranslator extends Translator {

	public function translate($token)
	{
		$hash = 'a'.spl_object_hash($token);

		$this->compileConditionals($hash, $token);

		$this->compileStatements($hash, $token);

		$this->compileEnd($hash, $token);
	}

	public function compileConditionals($hash, $token)
	{
		// Add a marker for the conditionals for the while loop
		// to redirect to when the statement block is over.
		$this->language->addMarker($hash.'0');

		// Create a conditional value. This will be set to 0 if the conditions
		// fail, or 1 if the conditions pass.
		$conditional = 'a'.spl_object_hash($token->cond);

		$this->language->variables->create($conditional);

		// Compile the conditionals. This will set the conditional value.
		$this->compiler->compile($token->cond);

		// Redirect to the end of the while loop block if the conditionals fail.
		$this->language->addCommand('be', $hash.'1', $conditional, '_int_zero');
	}

	public function compileStatements($hash, $token)
	{
		$this->compiler->compile($token->stmts);
	}

	public function compileEnd($hash, $token)
	{
		// We're at the end of the statement block. Redirect back to the conditionals
		// and see if we need to run the while loop again.
		$this->language->redirectTo($hash.'0');

		// Add a marker at the end of the while loop. If the conditionals fail, the
		// PC will be redirected to this marker.
		$this->language->addMarker($hash.'1');
	}
}