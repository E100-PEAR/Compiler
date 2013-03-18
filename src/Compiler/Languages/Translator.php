<?php namespace Compiler\Languages;

abstract class Translator {

	/**
	 * The compiler that is translating the tokens.
	 *
	 * @var Compiler\Compiler
	 */
	protected $compiler;

	/**
	 * The language that the translator belongs to.
	 *
	 * @var Object
	 */
	protected $language;

	/**
	 * Register the translator's compiler and language.
	 *
	 * @param  Compiler\Compiler  $compiler
	 * @param  object             $language
	 */
	public function __construct($compiler, $language)
	{
		$this->compiler = $compiler;
		$this->language = $language;
	}

	/**
	 * Translate the token. Note that any side-effects will
	 * be sent to the language object.
	 *
	 * @param  mixed   $token
	 * @return string
	 */
	abstract public function translate($token);
}