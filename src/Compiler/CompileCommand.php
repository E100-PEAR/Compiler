<?php namespace Compiler;

use Compiler\Compiler;
use PHPParser_Parser as Parser;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FileNotFoundException;

class CompileCommand extends Command {

	/**
	 * The filesystem that'll let us read and write to files.
	 *
	 * @var Illuminate\Filesystem\Filesystem
	 */
	protected $filesystem;

	/**
	 * The parser that will parse the inputted code into an
	 * Abstract Syntax Tree.
	 *
	 * @var PHPParser_Parser
	 */
	protected $parser;

	/**
	 * The compiler that will compile the Syntax Tree generated
	 * by the parser.
	 *
	 * @var Compiler\Compiler
	 */
	protected $compiler;

	/**
	 * Register the filesystem, parser, compiler and command.
	 *
	 * @param  Illuminate\Filesystem\Filesystem  $filesystem
	 * @param  PHPParser_Parser                  $parser
	 * @param  Compiler\Compiler                 $compiler
	 * @return void
	 */
	public function __construct(Filesystem $filesystem, Parser $parser, Compiler $compiler)
	{
		$this->filesystem = $filesystem;
		$this->parser = $parser;
		$this->compiler = $compiler;

		parent::__construct();
	}

	/**
	 * Set the command's configurations.
	 *
	 * @return void
	 */
	protected function configure()
	{
		$this->setName('compile')
		     ->setDescription('Compile a PHP script into E100 Assembly.')
		     ->addArgument('file', InputArgument::REQUIRED, 'Which file or folder do you want to compile?')
		     ->addArgument('output', InputArgument::OPTIONAL, 'The file where the code will be compiled.', 'compiled.txt');
	}

	/**
	 * Compile some code!
	 *
	 * @param  Symfony\Component\Console\Input\InputInterface;
	 * @param  Symfony\Component\Console\Output\OutputInterface;
	 * @return void
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$file = $input->getArgument('file');
		$out  = $input->getArgument('output');

		try
		{
			$output->writeln('<info>Reading file '.$file.'.<info>');

			$source = $this->filesystem->get($file);

			$output->writeln('<info>Parsing file '.$file.'.<info>');

			$code = $this->parser->parse($source);

			$output->writeln('<info>Compiling file '.$file.'.<info>');

			$code = $this->compiler->compile($code);

			$output->writeln('<info>Saving compiled code to '.$out.'.<info>');

			$this->filesystem->put($out, $code);
		}

		catch(\Exception $e)
		{
			$output->writeln('');
			$output->writeln('<error>'.$e->getMessage().'</error>');
		}
	}
}