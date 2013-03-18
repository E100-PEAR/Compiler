<?php namespace Compiler\Languages;

abstract class Language {

	protected $compiler;

	public $variables = array();

	public function __construct($variables)
	{
		$this->variables = $variables;
	}

	public function setCompiler($compiler)
	{
		$this->compiler = $compiler;
	}


	abstract public function getTranslation();
}