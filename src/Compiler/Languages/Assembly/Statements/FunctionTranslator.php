<?php namespace Compiler\Languages\Assembly\Statements;

use Compiler\Languages\Translator;

class FunctionTranslator extends Translator {

	public function translate($token)
	{
		$hash = spl_object_hash($token);

		$this->compileFunction($hash, $token);

		$this->compileParameters($hash, $token);

		$this->compileStatements($hash, $token);

		$this->compileEnd($hash, $token);
	}

	public function compileFunction($hash, $token)
	{
		// Redirect to the end of the function's statements if the code
		// procedurally reaches the function. 
		$this->language->redirectTo($hash.'0');

		$this->language->addMarker('function_'.$token->name);

		$this->language->variables->create('function_return_value');
		$this->language->variables->create('function_return_address');

		$this->language->setScope('func_'.$token->name);
	}

	public function compileParameters($hash, $token)
	{
		foreach($token->params as $param)
		{
			if(is_null($param->default))
			{
				$this->language->variables->create($param->name);
			}

			else
			{
				$this->language->variables->create($param->name, $param->default);
			}
		}
	}

	public function compileStatements($hash, $token)
	{
		$this->compiler->compile($token->stmts);
	}

	public function compileEnd($hash, $token)
	{
		$this->language->removeScope('func_'.$token->name);

		$this->language->addMarker($hash.'0');
	}
}