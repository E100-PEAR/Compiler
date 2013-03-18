<?php namespace Compiler;

use PHPParser_Node_Stmt;
use PHPParser_Node_Expr;

class Compiler {

	/**
	 * The language that the compiler compiles to.
	 *
	 * @var Object
	 */
	public $language;

	/**
	 * Register the language that the ocmpiler will compile to.
	 *
	 * @param  Object  $language
	 * @return void
	 */
	public function __construct($language)
	{
		$this->language = $language;
	}

	/**
	 * Compile tokens. If an array of tokens is passed, each token
	 * will be parsed.
	 *
	 * @param  mixed   $tokens
	 * @return string
	 */
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

	/**
	 * Get the language's translator for a specific token. The translator
	 * will read the token's data and output its translation.
	 *
	 * @param  mixed  $token
	 * @return void
	 */ 
	public function getTranslator($token)
	{
		$translator = $this->getTokenTranslatorName($token);

		return $this->language->getTranslator($translator);
	}

	/**
	 * Get the translator's name that will compile a token.
	 *
	 * @param  string  $token
	 * @return string
	 */
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