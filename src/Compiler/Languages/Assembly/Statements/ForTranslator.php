<?php namespace Compiler\Languages\Assembly\Statements;

use Compiler\Languages\Translator;

class ForTranslator extends Translator {

	public function translate($token)
	{
		$hash = spl_object_hash($token);

		// Compile the initial statements that occur before the loop.
		$this->compileInit($token);

		// Compile the conditionals that verify if the loop should again.
		$this->compileConditionals($hash, $token);

		// Compile the statements inside the loop's code block.
		$this->compileStatements($hash, $token);

		// Compile the statements after the loop has completed. This
		// will also redirect the PC back to the beginning of the loop.
		$this->compileLoop($hash, $token);
	}

	public function compileInit($token)
	{
		$this->compiler->compile($token->init);
	}

	public function compileConditionals($hash, $token)
	{
		// Create a marker for the conditions. After the statements
		// are successfully executed, the loop will redirect back here
		// to the conditionals.
		$this->language->addMarker($hash.'0');

		// Add each conditional individually.
		foreach($token->cond as $expression)
		{
			// The expression will set the conditional value to
			// 0 if the condition fails, or 1 if the condition passes.
			// Create the variable here so that the expression can set the
			// value later.
			$condition = spl_object_hash($expression);

			$this->language->variables->create($condition);

			// Run the expression and set the conditional value.
			$this->compiler->compile($expression);

			// Redirect to the end of the loop if the condition fails.
			$this->language->addCommand('be', $hash.'2', $condition, '_int_0');
		}
	}

	public function compileStatements($hash, $token)
	{
		$this->language->addMarker($hash.'1');

		$this->compiler->compile($token->stmts);
	}

	public function compileLoop($hash, $token)
	{
		// Run the statements that run each time a loop is completed.
		$this->compiler->compile($token->loop);

		// All the conditionals have passed. Redirect back to the beginning
		// of the loop and test the conditionals again.
		$this->language->redirectTo($hash.'0');

		// Add a marker for the loop to redirect to if the conditions fails.
		$this->language->addMarker($hash.'2');
	}
}