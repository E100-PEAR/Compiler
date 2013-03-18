<?php namespace Compiler;

use PHPParser_Node_Stmt;
use PHPParser_Node_Expr;

class Compiler {

	public $language;

	public function __construct($language)
	{
		$this->language = $language;
	}

	public function compile($tokens)
	{
		// Make sure the tokens are wrapped around in an array so
		// that we can loop through the tokens.
		if( ! is_array($tokens))
		{
			$tokens = array($tokens);
		}

		foreach($tokens as $token)
		{
			$this->getTranslator($token)->translate($token);
		}

		return $this->language->getTranslation();
	}

	public function getTranslator($token)
	{
		$translator = $this->getTokenTranslatorName($token);

		return $this->language->get($this, $translator);
	}

	public function getTokenTranslatorName($token)
	{
		// Get rid of the suffix.
		$type = (substr(get_class($token), strlen('PHPPARSER_Node_')));

		// Make the names a little clearer.
		$type = str_replace(array('Expr', 'Stmt'), array('Expressions', 'Statements'), $type);
	
		// Replace underscore to backslashes for class names.
		return str_replace('_', '\\', $type);
	}
}