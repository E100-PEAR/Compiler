<?php namespace Compiler\Languages\Assembly\Statements;

use Compiler\Languages\Translator;

class IfTranslator extends Translator {

	public function translate($token)
	{
		$hash = spl_object_hash($token->cond);

		$branch = $hash.'0';
		$else = $hash.'1';
		$end  = $hash.'2';

		$hasElses = ( ! is_null($token->else) and isset($token->else->stmts));

		// Create a variable for the conditional. If this is set
		// to 1, then the condition will pass.
		$this->language->variables->create($hash);

		// Compile the code for the conditional. This will set
		// the value of the variable for the conditional.
		$this->compiler->compile($token->cond);

		// If the conditional has elses we will need to redirect to those if the
		// condition is not met.
		$redirectElse = ($hasElses) ? $else : $end;
		
		// If the condition is not met, redirect to the end of the if statement's
		// code block, or the elses if they exist.
		$this->language->addCommand($branch.' bne', $redirectElse, $hash, 'one');

		// Compile the statements inside the if statement block. These will be skipped
		// if the conditional fails.
		$this->compiler->compile($token->stmts);

		// We're done with the if statement's code block. If we're here, the condition was met
		// and we'll need to skip over the elses, if they exist.
		$this->language->redirectTo($end);

		// Compile the else statements if they exist.
		if($hasElses)
		{
			// Add a marker so that the PC can jump to the else statement.
			$this->language->addMarker($else);

			// We don't need to redirect to the end as it will naturally
			// fall through to it.
			$this->compiler->compile($token->else->stmts);
		}

		// The marker for the end of the if and else statements.
		$this->language->addMarker($end);
	}
}