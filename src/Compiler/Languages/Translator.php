<?php namespace Compiler\Languages;

abstract class Translator {

	protected $compiler;
	protected $language;

	public function __construct($compiler, $language)
	{
		$this->compiler = $compiler;
		$this->language = $language;
	}

	abstract public function translate($token);
}