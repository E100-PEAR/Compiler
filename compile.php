<?php

ini_set('xdebug.max_nesting_level', 2000);

$autoloader = include 'vendor/autoload.php';
$autoloader->add('Compiler', __DIR__.'/src/');

$code = file_get_contents('sample.php');

$parser = new PHPParser_Parser(new PHPParser_Lexer);
$language = new Compiler\Languages\Assembly\Language(new Compiler\Languages\Variables\Variables);
$compiler = new Compiler\Compiler($language);

try
{
	$code = $parser->parse($code);

	echo '<pre>';
	echo $compiler->compile($code).PHP_EOL;
	echo '</pre>';
}

catch(Compiler\Error $e)
{
	echo 'Compilation Error: ' . $e->getMessage();
}

catch (PHPParser_Error $e)
{
	echo 'Parse Error: ' . $e->getMessage();
}