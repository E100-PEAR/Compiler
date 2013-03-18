<?php namespace Compiler\Languages;

use Compiler\Compiler;
use Compiler\Languages\Variables\Variables;

abstract class Language {

	/**
	 * The current language's compiler.
	 *
	 * @var Compiler\Compiler
	 */
	protected $compiler;

	/**
	 * The currently defined language variables.
	 *
	 * @var Compiler\Languages\Variables\Variables
	 */
	public $variables = array();

	/**
	 * Register the current language and its variables.
	 *
	 * @param  Compiler\Languages\Variables\Variables  $variables
	 * @return void
	 */
	public function __construct(Variables $variables)
	{
		$this->variables = $variables;
	}

	/**
	 * Set the current language's compiler.
	 *
	 * @param  Compiler\Compiler $compiler
	 * @return void
	 */
	public function setCompiler(Compiler $compiler)
	{
		$this->compiler = $compiler;
	}

	/**
	 * Get a translator from the current language.
	 *
	 * @param  string                         $translator
	 * @return Compiler\Languages\Translator
	 */
	abstract public function getTranslator($translator);

	/**
	 * Get the current translation for the compiled code.
	 *
	 * @return string
	 */
	abstract public function getTranslation();
}