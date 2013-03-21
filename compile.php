<?php

ini_set('xdebug.max_nesting_level', 2000);

$autoloader = include 'vendor/autoload.php';
$autoloader->add('Compiler', __DIR__.'/src/');

use Compiler\Compiler;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Application;
use Compiler\Languages\Assembly\Language as AssemblyLanguage;

$files = new Filesystem;
$parser = new PHPParser_Parser(new PHPParser_Lexer);
$compiler = new Compiler(new AssemblyLanguage);

try
{
	$code = $files->get('source.php');

	$code = $parser->parse($code);

	echo '<pre>';
	echo $compiler->compile($code).PHP_EOL;
	echo '</pre>';
}

catch(Illuminate\Filesystem\FileNotFoundException $e)
{
	echo 'File not found: ' . $e->getMessage();
}

catch(Compiler\Error $e)
{
	echo 'Compilation Error: ' . $e->getMessage();
}

catch (PHPParser_Error $e)
{
	echo 'Parse Error: ' . $e->getMessage();
}